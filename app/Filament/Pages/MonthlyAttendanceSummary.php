<?php

namespace App\Filament\Pages;

use App\Models\Student;
use App\Models\AttendanceRecord;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MonthlyAttendanceSummary extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static string $view = 'filament.pages.monthly-attendance-summary';

    protected static ?string $navigationGroup = 'Reports';

    protected static ?string $navigationLabel = 'Monthly Summary';

    protected static ?int $navigationSort = 2;

    public $selectedMonth;
    public $selectedYear;

    public function mount(): void
    {
        $this->selectedMonth = now()->month;
        $this->selectedYear = now()->year;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
            ->columns([
                Tables\Columns\TextColumn::make('student_no')
                    ->label('Student No')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('full_name')
                    ->label('Student Name')
                    ->searchable(['first_name', 'last_name'])
                    ->sortable(),

                Tables\Columns\TextColumn::make('present_count')
                    ->label('Present')
                    ->alignCenter()
                    ->color('success')
                    ->sortable(),

                Tables\Columns\TextColumn::make('absent_count')
                    ->label('Absent')
                    ->alignCenter()
                    ->color('danger')
                    ->sortable(),

                Tables\Columns\TextColumn::make('late_count')
                    ->label('Late')
                    ->alignCenter()
                    ->color('warning')
                    ->sortable(),

                Tables\Columns\TextColumn::make('excused_count')
                    ->label('Excused')
                    ->alignCenter()
                    ->color('info')
                    ->sortable(),

                Tables\Columns\TextColumn::make('total_days')
                    ->label('Total Days')
                    ->alignCenter()
                    ->sortable(),

                Tables\Columns\TextColumn::make('attendance_rate')
                    ->label('Attendance %')
                    ->alignCenter()
                    ->formatStateUsing(fn ($state) => number_format($state, 1) . '%')
                    ->color(fn ($state) => match(true) {
                        $state >= 90 => 'success',
                        $state >= 75 => 'warning',
                        default => 'danger',
                    })
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Student Status')
                    ->options([
                        'ACTIVE' => 'Active',
                        'INACTIVE' => 'Inactive',
                        'DROPPED' => 'Dropped',
                    ]),
            ])
            ->heading('Monthly Attendance Summary')
            ->description("Attendance summary for {$this->getMonthName()} {$this->selectedYear}");
    }

    protected function getTableQuery(): Builder
    {
        // Use date range instead of year/month functions for better index usage
        $startDate = Carbon::create($this->selectedYear, $this->selectedMonth, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();
        
        return Student::query()
            ->select([
                'students.*',
                DB::raw('COALESCE(SUM(CASE WHEN attendance_records.status = \'PRESENT\' THEN 1 ELSE 0 END), 0) as present_count'),
                DB::raw('COALESCE(SUM(CASE WHEN attendance_records.status = \'ABSENT\' THEN 1 ELSE 0 END), 0) as absent_count'),
                DB::raw('COALESCE(SUM(CASE WHEN attendance_records.status = \'LATE\' THEN 1 ELSE 0 END), 0) as late_count'),
                DB::raw('COALESCE(SUM(CASE WHEN attendance_records.status = \'EXCUSED\' THEN 1 ELSE 0 END), 0) as excused_count'),
                DB::raw('COUNT(attendance_records.id) as total_days'),
                DB::raw('CASE WHEN COUNT(attendance_records.id) > 0 THEN (SUM(CASE WHEN attendance_records.status IN (\'PRESENT\', \'LATE\') THEN 1 ELSE 0 END) * 100.0 / COUNT(attendance_records.id)) ELSE 0 END as attendance_rate'),
            ])
            ->leftJoin('attendance_records', function ($join) use ($startDate, $endDate) {
                $join->on('students.id', '=', 'attendance_records.student_id')
                    ->whereBetween('attendance_records.attendance_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')]);
            })
            ->groupBy('students.id');
    }

    public function getMonthName(): string
    {
        return \Carbon\Carbon::create($this->selectedYear, $this->selectedMonth)->format('F');
    }

    public function updatedSelectedMonth(): void
    {
        $this->resetTable();
    }

    public function updatedSelectedYear(): void
    {
        $this->resetTable();
    }
}
