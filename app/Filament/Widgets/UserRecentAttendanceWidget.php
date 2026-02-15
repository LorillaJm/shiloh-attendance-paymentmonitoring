<?php

namespace App\Filament\Widgets;

use App\Models\AttendanceRecord;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class UserRecentAttendanceWidget extends BaseWidget
{
    protected static ?int $sort = 3;
    
    protected int | string | array $columnSpan = 'full';
    
    protected static ?string $heading = 'Recent Attendance (Last 7 Days)';
    
    protected static ?string $pollingInterval = '30s';

    public static function canView(): bool
    {
        return !auth()->user()->isAdmin();
    }

    public function table(Table $table): Table
    {
        $today = now('Asia/Manila');
        $sevenDaysAgo = $today->copy()->subDays(6);

        return $table
            ->query(
                AttendanceRecord::query()
                    ->with(['student'])
                    ->whereBetween('attendance_date', [
                        $sevenDaysAgo->format('Y-m-d'),
                        $today->format('Y-m-d')
                    ])
                    ->orderBy('attendance_date', 'desc')
                    ->orderBy('created_at', 'desc')
                    ->limit(50)
            )
            ->columns([
                Tables\Columns\TextColumn::make('attendance_date')
                    ->label('Date')
                    ->date('M d, Y')
                    ->sortable()
                    ->weight('semibold')
                    ->color('primary'),

                Tables\Columns\TextColumn::make('student.student_no')
                    ->label('Student No')
                    ->searchable()
                    ->copyable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('student.full_name')
                    ->label('Student Name')
                    ->searchable(['first_name', 'last_name'])
                    ->weight('medium')
                    ->limit(30),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->colors([
                        'success' => 'PRESENT',
                        'danger' => 'ABSENT',
                        'warning' => 'LATE',
                        'info' => 'EXCUSED',
                    ])
                    ->icons([
                        'heroicon-o-check-circle' => 'PRESENT',
                        'heroicon-o-x-circle' => 'ABSENT',
                        'heroicon-o-clock' => 'LATE',
                        'heroicon-o-document-text' => 'EXCUSED',
                    ])
                    ->sortable(),

                Tables\Columns\TextColumn::make('remarks')
                    ->limit(30)
                    ->placeholder('-')
                    ->toggleable()
                    ->wrap(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Encoded')
                    ->dateTime('M d, h:i A')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'PRESENT' => 'Present',
                        'ABSENT' => 'Absent',
                        'LATE' => 'Late',
                        'EXCUSED' => 'Excused',
                    ])
                    ->multiple(),

                Tables\Filters\SelectFilter::make('attendance_date')
                    ->label('Date')
                    ->options(function () {
                        $today = now('Asia/Manila');
                        $options = [];
                        for ($i = 0; $i < 7; $i++) {
                            $date = $today->copy()->subDays($i);
                            $options[$date->format('Y-m-d')] = $date->format('M d, Y');
                        }
                        return $options;
                    }),
            ])
            ->paginated([10, 25, 50])
            ->defaultPaginationPageOption(25)
            ->striped()
            ->description('Attendance records from the last 7 days');
    }
}
