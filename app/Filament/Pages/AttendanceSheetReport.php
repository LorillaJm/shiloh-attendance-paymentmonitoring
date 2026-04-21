<?php

namespace App\Filament\Pages;

use App\Models\AttendanceRecord;
use App\Exports\AttendanceExport;
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

class AttendanceSheetReport extends Page implements HasTable, HasForms
{
    use InteractsWithTable, InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static string $view = 'filament.pages.attendance-sheet-report';

    protected static ?string $navigationGroup = 'Reports';

    protected static ?string $navigationLabel = 'Attendance Sheet';

    protected static ?int $navigationSort = 13;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'start_date' => now()->startOfMonth()->format('Y-m-d'),
            'end_date' => now()->format('Y-m-d'),
            'status' => null,
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

                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options(config('attendance.status_options'))
                            ->placeholder('All Statuses')
                            ->reactive(),
                    ])
                    ->columns(3),
            ])
            ->statePath('data');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
            ->columns([
                Tables\Columns\TextColumn::make('attendance_date')
                    ->label('Date')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('student.student_no')
                    ->label('Student No')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('student.full_name')
                    ->label('Student Name')
                    ->searchable(['first_name', 'last_name']),

                Tables\Columns\BadgeColumn::make('status')
                    ->colors(config('attendance.status_colors')),

                Tables\Columns\TextColumn::make('remarks')
                    ->limit(50)
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('encodedBy.name')
                    ->label('Encoded By')
                    ->toggleable(),
            ])
            ->defaultSort('attendance_date', 'desc')
            ->heading('Attendance Sheet Report')
            ->description($this->getReportDescription());
    }

    protected function getTableQuery(): Builder
    {
        $query = AttendanceRecord::query()
            ->with(['student', 'encodedBy']);

        if ($this->data['start_date'] ?? null) {
            $query->whereDate('attendance_date', '>=', $this->data['start_date']);
        }

        if ($this->data['end_date'] ?? null) {
            $query->whereDate('attendance_date', '<=', $this->data['end_date']);
        }

        if ($this->data['status'] ?? null) {
            $query->where('status', $this->data['status']);
        }

        return $query;
    }

    protected function getReportDescription(): string
    {
        $start = $this->data['start_date'] ?? now()->startOfMonth()->format('Y-m-d');
        $end = $this->data['end_date'] ?? now()->format('Y-m-d');
        
        return "Attendance records from {$start} to {$end}";
    }

    public function getSummary(): array
    {
        // Optimize: Use database aggregation instead of loading all records
        $summary = $this->getTableQuery()
            ->selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN status = \'PRESENT\' THEN 1 ELSE 0 END) as present,
                SUM(CASE WHEN status = \'ABSENT\' THEN 1 ELSE 0 END) as absent,
                SUM(CASE WHEN status = \'LATE\' THEN 1 ELSE 0 END) as late,
                SUM(CASE WHEN status = \'EXCUSED\' THEN 1 ELSE 0 END) as excused
            ')
            ->first();

        return [
            'total' => $summary->total ?? 0,
            'present' => $summary->present ?? 0,
            'absent' => $summary->absent ?? 0,
            'late' => $summary->late ?? 0,
            'excused' => $summary->excused ?? 0,
        ];
    }

    public function exportPdf()
    {
        try {
            set_time_limit(120);

            $records = $this->getTableQuery()
                ->whereHas('student')
                ->whereHas('encodedBy')
                ->get();

            $summary = $this->getSummary();

            $pdf = Pdf::loadView('reports.attendance-sheet-pdf', [
                'records' => $records,
                'summary' => $summary,
                'filters' => $this->data,
            ])->setPaper('a4', 'portrait');

            $filename = 'attendance-sheet-' . now()->format('Y-m-d-His') . '.pdf';
            $dir = storage_path('app/temp-reports');
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
            file_put_contents("{$dir}/{$filename}", $pdf->output());

            $url = \Illuminate\Support\Facades\URL::signedRoute('report.download', ['filename' => $filename]);
            $this->js("window.open('{$url}', '_blank')");
        } catch (\Throwable $e) {
            \Filament\Notifications\Notification::make()
                ->danger()
                ->title('PDF Export Failed')
                ->body($e->getMessage())
                ->send();
        }
    }

    public function exportExcel()
    {
        try {
            set_time_limit(120);

            $start = $this->data['start_date'] ?? now()->startOfMonth()->format('Y-m-d');
            $end = $this->data['end_date'] ?? now()->format('Y-m-d');

            $filename = 'attendance-sheet-' . now()->format('Y-m-d-His') . '.xlsx';
            $dir = storage_path('app/temp-reports');
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
            Excel::store(new AttendanceExport($start, $end), "temp-reports/{$filename}", 'local');

            $url = \Illuminate\Support\Facades\URL::signedRoute('report.download', ['filename' => $filename]);
            $this->js("window.open('{$url}', '_blank')");
        } catch (\Throwable $e) {
            \Filament\Notifications\Notification::make()
                ->danger()
                ->title('Excel Export Failed')
                ->body($e->getMessage())
                ->send();
        }
    }
}
