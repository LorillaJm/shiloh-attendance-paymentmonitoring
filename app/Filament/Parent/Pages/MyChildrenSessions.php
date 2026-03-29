<?php

namespace App\Filament\Parent\Pages;

use Filament\Pages\Page;
use Filament\Tables\Table;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use App\Models\SessionOccurrence;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;

class MyChildrenSessions extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';
    protected static string $view = 'filament.parent.pages.my-children-sessions';
    protected static ?string $navigationLabel = 'Sessions';
    protected static ?string $title = 'Session History';
    protected static ?int $navigationSort = 4;

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
            ->columns([
                TextColumn::make('student.full_name')
                    ->label('Student')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('sessionType.name')
                    ->label('Session Type')
                    ->searchable(),
                TextColumn::make('session_date')
                    ->label('Date')
                    ->date('M d, Y')
                    ->sortable(),
                TextColumn::make('start_time')
                    ->label('Time')
                    ->formatStateUsing(fn ($state, $record) => 
                        \Carbon\Carbon::parse($state)->format('g:i A') . ' - ' . 
                        \Carbon\Carbon::parse($record->end_time)->format('g:i A')
                    ),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'COMPLETED' => 'success',
                        'SCHEDULED' => 'info',
                        'CANCELLED' => 'danger',
                        'RESCHEDULED' => 'warning',
                        default => 'gray',
                    }),
                TextColumn::make('notes')
                    ->limit(40)
                    ->placeholder('-')
                    ->toggleable(),
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
                        'SCHEDULED' => 'Scheduled',
                        'COMPLETED' => 'Completed',
                        'CANCELLED' => 'Cancelled',
                        'RESCHEDULED' => 'Rescheduled',
                    ]),
                \Filament\Tables\Filters\Filter::make('session_date')
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
                                fn (Builder $query, $date): Builder => $query->whereDate('session_date', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('session_date', '<=', $date),
                            );
                    }),
            ])
            ->defaultSort('session_date', 'desc')
            ->paginated([10, 25, 50])
            ->poll('30s');
    }

    protected function getTableQuery(): Builder
    {
        $guardian = Auth::user()->guardian;
        
        if (!$guardian) {
            return SessionOccurrence::query()->whereRaw('1 = 0');
        }

        $studentIds = $guardian->students->pluck('id');

        return SessionOccurrence::query()
            ->whereIn('student_id', $studentIds)
            ->with(['student', 'sessionType']);
    }

    public static function canAccess(): bool
    {
        return Auth::check() && Auth::user()->isParent();
    }
}
