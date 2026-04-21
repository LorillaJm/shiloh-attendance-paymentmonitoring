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
use Illuminate\Database\Eloquent\Builder;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

class DailyAttendanceReport extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.daily-attendance-report';

    protected static ?string $navigationGroup = 'Reports';

    protected static ?string $navigationLabel = 'Daily Attendance';

    protected static ?int $navigationSort = 1;

    public $selectedDate;

    public function mount(): void
    {
        $this->selectedDate = now()->format('Y-m-d');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                AttendanceRecord::query()
                    ->with(['student', 'encodedBy'])
                    ->when($this->selectedDate, fn ($query) => $query->whereDate('attendance_date', $this->selectedDate))
                    ->orderBy('student_id')
            )
            ->columns([
                Tables\Columns\TextColumn::make('student.student_no')
                    ->label('Student No')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('student.full_name')
                    ->label('Student Name')
                    ->searchable(['first_name', 'last_name'])
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('status')
                    ->colors(config('attendance.status_colors')),

                Tables\Columns\TextColumn::make('remarks')
                    ->limit(50)
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('encodedBy.name')
                    ->label('Encoded By')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Encoded At')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(config('attendance.status_options'))
                    ->multiple(),
            ])
            ->heading('Daily Attendance Report')
            ->description('View attendance records for a specific date');
    }

    public function getTableRecordKey($record): string
    {
        return (string) $record->id;
    }

    public function getSummary(): array
    {
        $summary = AttendanceRecord::whereDate('attendance_date', $this->selectedDate)
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

            $records = AttendanceRecord::with(['student', 'encodedBy'])
                ->whereHas('student')
                ->whereDate('attendance_date', $this->selectedDate)
                ->orderBy('student_id')
                ->get();

            $summary = $this->getSummary();

            $pdf = Pdf::loadView('reports.daily-attendance-pdf', [
                'records' => $records,
                'summary' => $summary,
                'date' => $this->selectedDate,
            ])->setPaper('a4', 'portrait');

            $filename = 'daily-attendance-' . $this->selectedDate . '-' . now()->format('His') . '.pdf';
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

            $filename = 'daily-attendance-' . $this->selectedDate . '-' . now()->format('His') . '.xlsx';
            $dir = storage_path('app/temp-reports');
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
            Excel::store(
                new AttendanceExport($this->selectedDate, $this->selectedDate),
                "temp-reports/{$filename}",
                'local'
            );

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
