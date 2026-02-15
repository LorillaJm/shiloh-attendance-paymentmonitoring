<?php

namespace App\Filament\Pages;

use App\Models\PaymentSchedule;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Illuminate\Database\Eloquent\Builder;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Cache;

class DueOverdueReport extends Page implements HasTable, HasForms
{
    use InteractsWithTable, InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-exclamation-circle';

    protected static string $view = 'filament.pages.due-overdue-report';

    protected static ?string $navigationGroup = 'Reports';

    protected static ?string $navigationLabel = 'Due/Overdue Report';

    protected static ?int $navigationSort = 12;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'report_type' => 'overdue',
            'package_id' => null,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Report Filters')
                    ->schema([
                        Forms\Components\Select::make('report_type')
                            ->label('Report Type')
                            ->options([
                                'due' => 'Due Payments (Next 15th)',
                                'overdue' => 'Overdue Payments',
                                'all_unpaid' => 'All Unpaid',
                            ])
                            ->required()
                            ->reactive(),

                        Forms\Components\Select::make('package_id')
                            ->label('Package')
                            ->options(function() {
                                // Cache package list for 10 minutes
                                return Cache::remember('packages_list', 600, function() {
                                    return \App\Models\Package::pluck('name', 'id');
                                });
                            })
                            ->placeholder('All Packages')
                            ->searchable()
                            ->reactive(),
                    ])
                    ->columns(2),
            ])
            ->statePath('data');
    }

    public function table(Table $table): Table
    {
        // Get polling interval from config
        $pollInterval = $this->getPollingInterval();

        return $table
            ->query($this->getTableQuery())
            ->columns([
                Tables\Columns\TextColumn::make('enrollment.student.student_no')
                    ->label('Student No')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('enrollment.student.full_name')
                    ->label('Student Name')
                    ->searchable(['first_name', 'last_name']),

                Tables\Columns\TextColumn::make('enrollment.package.name')
                    ->label('Package'),

                Tables\Columns\TextColumn::make('installment_no')
                    ->label('Installment')
                    ->formatStateUsing(fn ($state) => $state == 0 ? 'Downpayment' : "Installment #{$state}"),

                Tables\Columns\TextColumn::make('due_date')
                    ->date()
                    ->sortable()
                    ->description(fn ($record) => $record->due_date && $record->due_date->isPast() ? $record->due_date->diffForHumans() : null),

                Tables\Columns\TextColumn::make('amount_due')
                    ->money('PHP')
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('computed_status')
                    ->label('Status')
                    ->colors([
                        'warning' => 'UNPAID',
                        'danger' => 'OVERDUE',
                    ]),

                Tables\Columns\TextColumn::make('enrollment.student.guardian_contact')
                    ->label('Contact')
                    ->placeholder('-'),
            ])
            ->defaultSort('due_date')
            ->defaultPaginationPageOption(25)
            ->poll($pollInterval)
            ->heading($this->getReportHeading())
            ->description($this->getReportDescription());
    }

    protected function getPollingInterval(): ?string
    {
        // Check if polling is enabled and user has permission
        if (!config('realtime.enabled', true)) {
            return null;
        }

        $user = auth()->user();
        $allowedRoles = config('realtime.allowed_roles', ['ADMIN', 'USER']);
        
        if (!in_array($user->role->value ?? $user->role, $allowedRoles)) {
            return null;
        }

        // Get interval from config (in seconds)
        $interval = config('realtime.reports_poll_interval', 30);
        
        return $interval ? "{$interval}s" : null;
    }

    protected function getTableQuery(): Builder
    {
        $query = PaymentSchedule::query()
            ->with(['enrollment.student', 'enrollment.package'])
            ->where('payment_schedules.status', 'UNPAID');

        $reportType = $this->data['report_type'] ?? 'overdue';

        if ($reportType === 'due') {
            // Due on next 15th - use same logic as DuePayments page (Phase 3 fix)
            // Always next month's 15th to be consistent with schedule generation
            $next15th = now()->startOfMonth()->addMonth()->day(15);
            
            $query->whereDate('due_date', $next15th->format('Y-m-d'));
        } elseif ($reportType === 'overdue') {
            // Overdue
            $query->whereNotNull('due_date')
                ->where('due_date', '<', now()->format('Y-m-d'));
        }
        // 'all_unpaid' - no additional filter

        if ($this->data['package_id'] ?? null) {
            $query->whereHas('enrollment', function ($q) {
                $q->where('package_id', $this->data['package_id']);
            });
        }

        return $query;
    }

    protected function getReportHeading(): string
    {
        $reportType = $this->data['report_type'] ?? 'overdue';

        return match($reportType) {
            'due' => 'Due Payments (Next 15th)',
            'overdue' => 'Overdue Payments',
            'all_unpaid' => 'All Unpaid Payments',
            default => 'Payment Report',
        };
    }

    protected function getReportDescription(): string
    {
        $reportType = $this->data['report_type'] ?? 'overdue';

        return match($reportType) {
            'due' => 'Payments due on the next 15th of the month',
            'overdue' => 'Payments past their due date',
            'all_unpaid' => 'All unpaid payment schedules',
            default => '',
        };
    }

    public function getSummary(): array
    {
        // Cache summary for 30 seconds with filters in cache key
        $cacheKey = 'due_overdue_summary_' . md5(json_encode($this->data));
        
        return Cache::remember($cacheKey, 30, function() {
            // Optimize: Use database aggregation instead of loading all records
            $summary = $this->getTableQuery()
                ->selectRaw('
                    COUNT(*) as total_count,
                    SUM(amount_due) as total_amount
                ')
                ->first();
            
            // Get breakdown by package
            $byPackage = $this->getTableQuery()
                ->join('enrollments', 'payment_schedules.enrollment_id', '=', 'enrollments.id')
                ->join('packages', 'enrollments.package_id', '=', 'packages.id')
                ->selectRaw('
                    packages.name as package_name,
                    COUNT(*) as count,
                    SUM(payment_schedules.amount_due) as amount
                ')
                ->groupBy('packages.id', 'packages.name')
                ->get()
                ->mapWithKeys(fn ($item) => [
                    $item->package_name => [
                        'count' => $item->count,
                        'amount' => $item->amount,
                    ]
                ]);

            return [
                'total_amount' => $summary->total_amount ?? 0,
                'total_count' => $summary->total_count ?? 0,
                'by_package' => $byPackage,
            ];
        });
    }

    public function exportPdf()
    {
        $records = $this->getTableQuery()->get();
        $summary = $this->getSummary();
        
        $pdf = Pdf::loadView('reports.due-overdue-pdf', [
            'records' => $records,
            'summary' => $summary,
            'filters' => $this->data,
            'reportType' => $this->data['report_type'] ?? 'overdue',
        ]);

        $filename = ($this->data['report_type'] ?? 'overdue') . '-report-' . now()->format('Y-m-d') . '.pdf';

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, $filename);
    }
}
