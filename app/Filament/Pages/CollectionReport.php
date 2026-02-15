<?php

namespace App\Filament\Pages;

use App\Models\PaymentSchedule;
use App\Exports\PaymentCollectionExport;
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
use Maatwebsite\Excel\Facades\Excel;
use Filament\Notifications\Notification;

class CollectionReport extends Page implements HasTable, HasForms
{
    use InteractsWithTable, InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static string $view = 'filament.pages.collection-report';

    protected static ?string $navigationGroup = 'Reports';

    protected static ?string $navigationLabel = 'Collection Report';

    protected static ?int $navigationSort = 10;

    public static function shouldRegisterNavigation(): bool
    {
        // Only show to admins (financial data)
        return auth()->user()?->isAdmin() ?? false;
    }

    public static function canAccess(): bool
    {
        // Only admins can access financial reports
        return auth()->user()?->isAdmin() ?? false;
    }

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'start_date' => now()->startOfMonth()->format('Y-m-d'),
            'end_date' => now()->format('Y-m-d'),
            'payment_method' => null,
            'package_id' => null,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Report Filters')
                    ->schema([
                        Forms\Components\DatePicker::make('start_date')
                            ->label('Start Date')
                            ->required()
                            ->native(false)
                            ->reactive(),

                        Forms\Components\DatePicker::make('end_date')
                            ->label('End Date')
                            ->required()
                            ->native(false)
                            ->reactive(),

                        Forms\Components\Select::make('payment_method')
                            ->label('Payment Method')
                            ->options([
                                'CASH' => 'Cash',
                                'BANK_TRANSFER' => 'Bank Transfer',
                                'GCASH' => 'GCash',
                                'PAYMAYA' => 'PayMaya',
                                'CHECK' => 'Check',
                                'CREDIT_CARD' => 'Credit Card',
                                'DEBIT_CARD' => 'Debit Card',
                            ])
                            ->placeholder('All Methods')
                            ->reactive(),

                        Forms\Components\Select::make('package_id')
                            ->label('Package')
                            ->options(\App\Models\Package::pluck('name', 'id'))
                            ->placeholder('All Packages')
                            ->searchable()
                            ->reactive(),
                    ])
                    ->columns(4),
            ])
            ->statePath('data');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
            ->columns([
                Tables\Columns\TextColumn::make('paid_at')
                    ->label('Payment Date')
                    ->dateTime()
                    ->sortable(),

                Tables\Columns\TextColumn::make('enrollment.student.student_no')
                    ->label('Student No')
                    ->searchable(),

                Tables\Columns\TextColumn::make('enrollment.student.full_name')
                    ->label('Student Name')
                    ->searchable(['first_name', 'last_name']),

                Tables\Columns\TextColumn::make('enrollment.package.name')
                    ->label('Package'),

                Tables\Columns\TextColumn::make('installment_no')
                    ->label('Installment')
                    ->formatStateUsing(fn ($state) => $state == 0 ? 'Downpayment' : "Installment #{$state}"),

                Tables\Columns\TextColumn::make('amount_due')
                    ->label('Amount')
                    ->money('PHP')
                    ->sortable(),

                Tables\Columns\TextColumn::make('payment_method')
                    ->badge()
                    ->colors([
                        'success' => 'CASH',
                        'info' => ['BANK_TRANSFER', 'GCASH', 'PAYMAYA'],
                        'warning' => 'CHECK',
                    ]),

                Tables\Columns\TextColumn::make('receipt_no')
                    ->label('Receipt')
                    ->placeholder('-'),
            ])
            ->defaultSort('paid_at', 'desc')
            ->heading('Payment Collection Report')
            ->description($this->getReportDescription());
    }

    protected function getTableQuery(): Builder
    {
        $query = PaymentSchedule::query()
            ->with(['enrollment.student', 'enrollment.package'])
            ->where('status', 'PAID');

        if ($this->data['start_date'] ?? null) {
            $query->whereDate('paid_at', '>=', $this->data['start_date']);
        }

        if ($this->data['end_date'] ?? null) {
            $query->whereDate('paid_at', '<=', $this->data['end_date']);
        }

        if ($this->data['payment_method'] ?? null) {
            $query->where('payment_method', $this->data['payment_method']);
        }

        if ($this->data['package_id'] ?? null) {
            $query->whereHas('enrollment', function ($q) {
                $q->where('package_id', $this->data['package_id']);
            });
        }

        return $query;
    }

    protected function getReportDescription(): string
    {
        $start = $this->data['start_date'] ?? now()->startOfMonth()->format('Y-m-d');
        $end = $this->data['end_date'] ?? now()->format('Y-m-d');
        
        return "Collections from {$start} to {$end}";
    }

    public function getSummary(): array
    {
        // Optimize: Use database aggregation instead of loading all records
        $summary = $this->getTableQuery()
            ->selectRaw('
                COUNT(*) as total_count,
                SUM(amount_due) as total_amount
            ')
            ->first();
        
        // Get breakdown by payment method
        $byMethod = $this->getTableQuery()
            ->selectRaw('
                payment_method,
                COUNT(*) as count,
                SUM(amount_due) as amount
            ')
            ->groupBy('payment_method')
            ->get()
            ->mapWithKeys(fn ($item) => [
                $item->payment_method => [
                    'count' => $item->count,
                    'amount' => $item->amount,
                ]
            ]);

        return [
            'total_amount' => $summary->total_amount ?? 0,
            'total_count' => $summary->total_count ?? 0,
            'by_method' => $byMethod,
        ];
    }

    public function exportPdf()
    {
        // Check record count to prevent timeout
        $count = $this->getTableQuery()->count();
        
        if ($count > 5000) {
            Notification::make()
                ->danger()
                ->title('Export Too Large')
                ->body("Cannot export {$count} records. Please narrow your date range.")
                ->send();
            return;
        }
        
        if ($count > 1000) {
            Notification::make()
                ->warning()
                ->title('Large Export')
                ->body("Exporting {$count} records. This may take a moment...")
                ->send();
        }
        
        $records = $this->getTableQuery()->limit(5000)->get();
        $summary = $this->getSummary();
        
        $pdf = Pdf::loadView('reports.collection-pdf', [
            'records' => $records,
            'summary' => $summary,
            'filters' => $this->data,
        ]);

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'collection-report-' . now()->format('Y-m-d') . '.pdf');
    }

    public function exportExcel()
    {
        $start = $this->data['start_date'] ?? now()->startOfMonth()->format('Y-m-d');
        $end = $this->data['end_date'] ?? now()->format('Y-m-d');

        return Excel::download(
            new PaymentCollectionExport($start, $end),
            'collection-report-' . now()->format('Y-m-d') . '.xlsx'
        );
    }
}
