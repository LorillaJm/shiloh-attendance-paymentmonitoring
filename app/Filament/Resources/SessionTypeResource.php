<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SessionTypeResource\Pages;
use App\Models\SessionType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SessionTypeResource extends Resource
{
    protected static ?string $model = SessionType::class;
    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';
    protected static ?string $navigationGroup = 'Session Management';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')->required(),
                Forms\Components\TextInput::make('code')->required()->unique(ignoreRecord: true)->uppercase(),
                Forms\Components\Textarea::make('description')->rows(2),
                Forms\Components\TextInput::make('default_duration_minutes')
                    ->numeric()
                    ->default(60)
                    ->suffix('minutes')
                    ->required(),
                Forms\Components\Toggle::make('requires_monitoring')
                    ->label('Requires Monitoring (Age 10 and below)')
                    ->default(false),
                Forms\Components\Toggle::make('is_active')->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('code')->searchable(),
                Tables\Columns\TextColumn::make('default_duration_minutes')->suffix(' min'),
                Tables\Columns\IconColumn::make('requires_monitoring')->boolean(),
                Tables\Columns\IconColumn::make('is_active')->boolean(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListSessionTypes::route('/'),
            'create' => Pages\CreateSessionType::route('/create'),
            'edit' => Pages\EditSessionType::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->isAdmin();
    }
}
