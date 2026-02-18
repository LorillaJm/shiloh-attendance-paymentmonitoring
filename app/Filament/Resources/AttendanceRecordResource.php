<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AttendanceRecordResource\Pages;
use App\Filament\Resources\AttendanceRecordResource\RelationManagers;
use App\Models\AttendanceRecord;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class AttendanceRecordResource extends Resource
{
    protected static ?string $model = AttendanceRecord::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static ?string $navigationGroup = 'Attendance Management';

    protected static ?string $navigationLabel = 'Attendance Records';

    protected static ?int $navigationSort = 2;
    
    protected static ?string $recordTitleAttribute = 'attendance_date';

    public static function shouldRegisterNavigation(): bool
    {
        // Show to both admin and users
        return true;
    }

    public static function canViewAny(): bool
    {
        // Both admin and users can view attendance records
        return true;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Attendance Information')
                    ->description('Record student attendance details')
                    ->icon('heroicon-o-clipboard-document-check')
                    ->schema([
                        Forms\Components\Select::make('session_occurrence_id')
                            ->label('Session (Optional)')
                            ->relationship('sessionOccurrence', 'id')
                            ->getOptionLabelFromRecordUsing(fn ($record) => 
                                "{$record->student->student_no} - {$record->sessionType->name} - {$record->session_date->format('M d, Y')}"
                            )
                            ->searchable()
                            ->preload(),

                        Forms\Components\Select::make('student_id')
                            ->relationship('student', 'first_name')
                            ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->student_no} - {$record->full_name}")
                            ->searchable(['student_no', 'first_name', 'last_name'])
                            ->required()
                            ->preload()
                            ->columnSpanFull(),

                        Forms\Components\DatePicker::make('attendance_date')
                            ->required()
                            ->default(now())
                            ->native(false)
                            ->maxDate(now()),

                        Forms\Components\Select::make('status')
                            ->options(config('attendance.status_options'))
                            ->required()
                            ->default(config('attendance.default_status')),

                        Forms\Components\Textarea::make('remarks')
                            ->rows(3)
                            ->placeholder('Optional notes about this attendance record')
                            ->columnSpanFull(),

                        Forms\Components\Hidden::make('encoded_by_user_id')
                            ->default(Auth::id())
                            ->dehydrated(),
                    ])
                    ->columns(2)
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('attendance_date')
                    ->label('Date')
                    ->date('M d, Y')
                    ->sortable()
                    ->searchable()
                    ->icon('heroicon-o-calendar')
                    ->weight('semibold'),

                Tables\Columns\TextColumn::make('student.student_no')
                    ->label('Student No')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-o-identification')
                    ->color('primary')
                    ->copyable(),

                Tables\Columns\TextColumn::make('student.full_name')
                    ->label('Student Name')
                    ->searchable(['first_name', 'last_name'])
                    ->sortable()
                    ->weight('medium'),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->colors(config('attendance.status_colors'))
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

                Tables\Columns\TextColumn::make('encodedBy.name')
                    ->label('Encoded By')
                    ->toggleable()
                    ->sortable()
                    ->icon('heroicon-o-user'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('M d, Y h:i A')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('attendance_date', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(config('attendance.status_options'))
                    ->multiple(),

                Tables\Filters\Filter::make('attendance_date')
                    ->form([
                        Forms\Components\DatePicker::make('from')
                            ->native(false)
                            ->label('From Date'),
                        Forms\Components\DatePicker::make('until')
                            ->native(false)
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
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['from'] ?? null) {
                            $indicators['from'] = 'From: ' . \Carbon\Carbon::parse($data['from'])->format('M d, Y');
                        }
                        if ($data['until'] ?? null) {
                            $indicators['until'] = 'Until: ' . \Carbon\Carbon::parse($data['until'])->format('M d, Y');
                        }
                        return $indicators;
                    }),

                Tables\Filters\SelectFilter::make('student')
                    ->relationship('student', 'first_name')
                    ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->student_no} - {$record->full_name}")
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->iconButton(),
                Tables\Actions\DeleteAction::make()
                    ->iconButton(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn () => Auth::user()->isAdmin()),
                ]),
            ])
            ->emptyStateHeading('No attendance records')
            ->emptyStateDescription('Start marking attendance using the Daily Encoder.')
            ->emptyStateIcon('heroicon-o-clipboard-document-check')
            ->emptyStateActions([
                Tables\Actions\Action::make('encode')
                    ->label('Go to Daily Encoder')
                    ->icon('heroicon-o-pencil-square')
                    ->url(route('filament.admin.pages.daily-attendance-encoder')),
            ])
            ->striped()
            ->poll('30s');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAttendanceRecords::route('/'),
            'create' => Pages\CreateAttendanceRecord::route('/create'),
            'edit' => Pages\EditAttendanceRecord::route('/{record}/edit'),
        ];
    }

    // Authorization now handled by AttendanceRecordPolicy
}
