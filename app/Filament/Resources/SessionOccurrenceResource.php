<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SessionOccurrenceResource\Pages;
use App\Models\SessionOccurrence;
use App\Models\User;
use App\Enums\UserRole;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SessionOccurrenceResource extends Resource
{
    protected static ?string $model = SessionOccurrence::class;
    protected static ?string $navigationIcon = 'heroicon-o-calendar';
    protected static ?string $navigationGroup = 'Session Management';
    protected static ?int $navigationSort = 3;
    protected static ?string $navigationLabel = 'Session Occurrences';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('student_id')
                    ->relationship('student', 'first_name')
                    ->searchable()
                    ->required()
                    ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->student_no} - {$record->full_name}"),
                Forms\Components\Select::make('session_type_id')
                    ->relationship('sessionType', 'name')
                    ->required(),
                Forms\Components\Select::make('teacher_id')
                    ->label('Assigned Teacher / Faculty')
                    ->options(User::whereIn('role', [UserRole::ADMIN->value, UserRole::SUPERADMIN->value])->pluck('name', 'id'))
                    ->searchable()
                    ->helperText('Optional: Assign a teacher or faculty member to this session'),
                Forms\Components\DatePicker::make('session_date')->required()->default(now()),
                Forms\Components\TimePicker::make('start_time')->required(),
                Forms\Components\TimePicker::make('end_time')->required(),
                Forms\Components\Select::make('status')
                    ->options([
                        'SCHEDULED' => 'Scheduled',
                        'COMPLETED' => 'Completed',
                        'CANCELLED' => 'Cancelled',
                        'NO_SHOW' => 'No Show',
                    ])
                    ->default('SCHEDULED')
                    ->required(),
                Forms\Components\Textarea::make('notes')->rows(2),
                Forms\Components\Textarea::make('monitoring_notes')
                    ->label('Monitoring Notes (Age 10 and below)')
                    ->rows(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with(['student', 'sessionType', 'teacher', 'attendanceRecord']))
            ->columns([
                Tables\Columns\TextColumn::make('session_date')->date()->sortable(),
                Tables\Columns\TextColumn::make('student.student_no')->searchable(),
                Tables\Columns\TextColumn::make('student.full_name')->searchable(),
                Tables\Columns\TextColumn::make('sessionType.name'),
                Tables\Columns\TextColumn::make('teacher.name')
                    ->label('Assigned Teacher / Faculty'),
                Tables\Columns\TextColumn::make('start_time')->time('H:i'),
                Tables\Columns\TextColumn::make('status')->badge()->color(fn (string $state): string => match ($state) {
                    'SCHEDULED' => 'gray',
                    'COMPLETED' => 'success',
                    'CANCELLED' => 'danger',
                    'NO_SHOW' => 'warning',
                }),
                Tables\Columns\IconColumn::make('attendanceRecord')
                    ->label('Attendance')
                    ->boolean()
                    ->getStateUsing(fn ($record) => $record->attendanceRecord !== null),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status'),
                Tables\Filters\SelectFilter::make('session_type_id')
                    ->relationship('sessionType', 'name'),
                Tables\Filters\Filter::make('session_date')
                    ->form([
                        Forms\Components\DatePicker::make('from'),
                        Forms\Components\DatePicker::make('until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['from'], fn (Builder $query, $date): Builder => $query->whereDate('session_date', '>=', $date))
                            ->when($data['until'], fn (Builder $query, $date): Builder => $query->whereDate('session_date', '<=', $date));
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('session_date', 'desc')
            ->defaultPaginationPageOption(25)
            ->paginationPageOptions([10, 25, 50, 100]);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        // All admins see all sessions
        return $query;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSessionOccurrences::route('/'),
            'create' => Pages\CreateSessionOccurrence::route('/create'),
            'edit' => Pages\EditSessionOccurrence::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        // Only SUPERADMIN and ADMIN can view session occurrences
        $user = auth()->user();
        return $user && ($user->isSuperadmin() || $user->isAdmin());
    }
}
