<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StudentScheduleResource\Pages;
use App\Models\StudentSchedule;
use App\Models\User;
use App\Enums\UserRole;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class StudentScheduleResource extends Resource
{
    protected static ?string $model = StudentSchedule::class;
    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $navigationGroup = 'Session Management';
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationLabel = 'Student Schedules';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('student_id')
                    ->relationship('student', 'first_name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->student_no} - {$record->full_name}"),
                Forms\Components\Select::make('session_type_id')
                    ->relationship('sessionType', 'name')
                    ->required(),
                Forms\Components\Select::make('teacher_id')
                    ->label('Assigned Teacher')
                    ->options(User::where('role', UserRole::TEACHER->value)->pluck('name', 'id'))
                    ->searchable(),
                Forms\Components\Select::make('recurrence_type')
                    ->options([
                        'DAILY' => 'Daily',
                        'WEEKLY' => 'Weekly',
                        'CUSTOM' => 'Custom',
                    ])
                    ->required()
                    ->reactive(),
                Forms\Components\CheckboxList::make('recurrence_days')
                    ->label('Days of Week')
                    ->options([
                        1 => 'Monday',
                        2 => 'Tuesday',
                        3 => 'Wednesday',
                        4 => 'Thursday',
                        5 => 'Friday',
                        6 => 'Saturday',
                        7 => 'Sunday',
                    ])
                    ->columns(4)
                    ->visible(fn ($get) => $get('recurrence_type') === 'WEEKLY'),
                Forms\Components\TimePicker::make('start_time')->required(),
                Forms\Components\TimePicker::make('end_time')->required(),
                Forms\Components\DatePicker::make('effective_from')->required()->default(now()),
                Forms\Components\DatePicker::make('effective_until'),
                Forms\Components\Toggle::make('is_active')->default(true),
                Forms\Components\Textarea::make('notes')->rows(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('student.student_no')->searchable(),
                Tables\Columns\TextColumn::make('student.full_name')->searchable(),
                Tables\Columns\TextColumn::make('sessionType.name'),
                Tables\Columns\TextColumn::make('teacher.name')->label('Teacher'),
                Tables\Columns\TextColumn::make('recurrence_type'),
                Tables\Columns\TextColumn::make('start_time')->time('H:i'),
                Tables\Columns\TextColumn::make('end_time')->time('H:i'),
                Tables\Columns\IconColumn::make('is_active')->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('session_type_id')
                    ->relationship('sessionType', 'name'),
                Tables\Filters\SelectFilter::make('teacher_id')
                    ->label('Teacher')
                    ->options(User::where('role', UserRole::TEACHER->value)->pluck('name', 'id')),
                Tables\Filters\TernaryFilter::make('is_active'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('generate_sessions')
                    ->label('Generate Sessions')
                    ->icon('heroicon-o-calendar')
                    ->color('success')
                    ->form([
                        Forms\Components\DatePicker::make('start_date')->required()->default(now()),
                        Forms\Components\DatePicker::make('end_date')->required()->default(now()->addMonth()),
                    ])
                    ->action(function (StudentSchedule $record, array $data) {
                        $count = \App\Services\SessionOccurrenceGenerator::generateFromSchedule(
                            $record,
                            \Carbon\Carbon::parse($data['start_date']),
                            \Carbon\Carbon::parse($data['end_date'])
                        );
                        \Filament\Notifications\Notification::make()
                            ->success()
                            ->title("Generated {$count} session occurrences")
                            ->send();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStudentSchedules::route('/'),
            'create' => Pages\CreateStudentSchedule::route('/create'),
            'edit' => Pages\EditStudentSchedule::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        $user = auth()->user();
        return $user->isAdmin() || $user->isTeacher();
    }
}
