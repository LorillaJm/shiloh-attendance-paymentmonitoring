<?php

namespace App\Filament\Parent\Pages;

use Filament\Pages\Page;
use Filament\Tables\Table;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use App\Models\AttendanceRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;

class MyChildrenAttendance extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static string $view = 'filament.parent.pages.my-children-attendance';
    protected static ?string $navigationLabel = 'Attendance';
    protected static ?string $title = 'Attendance Records';
    protected static ?int $navigationSort = 3;

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
            ->columns([
                TextColumn::make('student.full_name')
                    ->label('Student')
                    ->searchable(['first_name', 'last_name'])
                    ->sortable(),
                TextColumn::make('attendance_date')
                    ->label('Date')
                    ->date('M d, Y')
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'PRESENT' => 'success',
                        'ABSENT' => 'danger',
                        'LATE' => 'warning',
                        'EXCUSED' => 'info',
                        default => 'gray',
                    }),
                TextColumn::make('remarks')
                    ->limit(50)
                    ->placeholder('-')
                    ->wrap(),
            ])
            ->filters([
                SelectFilter::make('student_id')
                    ->label('Student')
                    ->options(function () {
                        $guardian = Auth::user()->guardian;
                        if (!$guardian) return [];
                        return $guardian->students->pluck('full_name', 'id');
                    }),
                SelectFilter::make('status')
                    ->options([
                        'PRESENT' => 'Present',
                        'ABSENT' => 'Absent',
                        'LATE' => 'Late',
                        'EXCUSED' => 'Excused',
                    ]),
                \Filament\Tables\Filters\Filter::make('attendance_date')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('from')
                            ->label('From Date'),
                        \Filament\Forms\Components\DatePicker::make('until')
                            ->label('Until Date'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('attendance_date', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('attendance_date', '<=', $date),
                            );
                    }),
            ])
            ->defaultSort('attendance_date', 'desc')
            ->paginated([10, 25, 50])
            ->poll('30s');
    }

    protected function getTableQuery(): Builder
    {
        $guardian = Auth::user()->guardian;
        
        if (!$guardian) {
            return AttendanceRecord::query()->whereRaw('1 = 0');
        }

        $studentIds = $guardian->students->pluck('id');

        return AttendanceRecord::query()
            ->whereIn('student_id', $studentIds)
            ->with(['student']);
    }

    public static function canAccess(): bool
    {
        return Auth::check() && Auth::user()->isParent();
    }
}
