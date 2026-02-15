<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PackageResource\Pages;
use App\Models\Package;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Support\RawJs;

class PackageResource extends Resource
{
    protected static ?string $model = Package::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube-transparent';

    protected static ?string $navigationGroup = 'Master Data';

    protected static ?int $navigationSort = 2;
    
    protected static ?string $navigationLabel = 'Programs';
    
    protected static ?string $modelLabel = 'Program';
    
    protected static ?string $pluralModelLabel = 'Programs';

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Package Information')
                    ->description('Define package details and pricing')
                    ->icon('heroicon-o-cube-transparent')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->placeholder('e.g., Package A, Package B')
                            ->columnSpanFull()
                            ->autofocus(),
                        
                        Forms\Components\TextInput::make('total_fee')
                            ->required()
                            ->numeric()
                            ->prefix('₱')
                            ->mask(RawJs::make('$money($input)'))
                            ->stripCharacters(',')
                            ->minValue(1)
                            ->step(0.01)
                            ->placeholder('0.00')
                            ->helperText('Total package fee amount (minimum ₱1.00)')
                            ->reactive(),
                        
                        Forms\Components\TextInput::make('downpayment_percent')
                            ->required()
                            ->numeric()
                            ->suffix('%')
                            ->default(25)
                            ->minValue(0)
                            ->maxValue(100)
                            ->step(0.01)
                            ->helperText('Percentage of total fee for downpayment (0-100%)')
                            ->reactive()
                            ->afterStateUpdated(function ($state, $set, $get) {
                                if ($state >= 100) {
                                    $set('installment_months', 0);
                                }
                            }),
                        
                        Forms\Components\TextInput::make('installment_months')
                            ->required()
                            ->numeric()
                            ->default(3)
                            ->minValue(0)
                            ->maxValue(24)
                            ->suffix('months')
                            ->helperText('Number of months for installment payment (0 if full downpayment)')
                            ->reactive(),
                        
                        Forms\Components\Textarea::make('description')
                            ->maxLength(65535)
                            ->rows(3)
                            ->placeholder('Package details and inclusions...')
                            ->columnSpanFull(),
                    ])
                    ->columns(3)
                    ->collapsible(),
                
                Forms\Components\Section::make('Calculated Amounts')
                    ->description('Auto-calculated payment breakdown')
                    ->icon('heroicon-o-calculator')
                    ->schema([
                        Forms\Components\Placeholder::make('downpayment_amount')
                            ->label('Downpayment Amount')
                            ->content(function ($get) {
                                $totalFee = (float) $get('total_fee');
                                $percent = (float) $get('downpayment_percent');
                                $amount = ($totalFee * $percent) / 100;
                                return '₱' . number_format($amount, 2);
                            }),
                        
                        Forms\Components\Placeholder::make('monthly_installment')
                            ->label('Monthly Installment')
                            ->content(function ($get) {
                                $totalFee = (float) $get('total_fee');
                                $percent = (float) $get('downpayment_percent');
                                $months = (int) $get('installment_months');
                                
                                $downpayment = ($totalFee * $percent) / 100;
                                $balance = $totalFee - $downpayment;
                                $monthly = $months > 0 ? $balance / $months : 0;
                                
                                return '₱' . number_format($monthly, 2);
                            }),
                    ])
                    ->columns(2)
                    ->visible(fn ($context) => $context !== 'create')
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Program Name')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold')
                    ->icon('heroicon-o-cube-transparent')
                    ->color('primary'),
                
                Tables\Columns\TextColumn::make('total_fee')
                    ->label('Total Fee')
                    ->money('PHP')
                    ->sortable()
                    ->alignEnd()
                    ->weight('semibold'),
                
                Tables\Columns\TextColumn::make('downpayment_percent')
                    ->label('Down Payment')
                    ->formatStateUsing(fn ($state) => $state . '%')
                    ->alignEnd()
                    ->badge()
                    ->color('info'),
                
                Tables\Columns\TextColumn::make('downpayment_amount')
                    ->label('Down Payment Amount')
                    ->money('PHP')
                    ->alignEnd()
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('installment_months')
                    ->label('Payment Period')
                    ->formatStateUsing(fn ($state) => $state . ' months')
                    ->alignEnd()
                    ->badge()
                    ->color('warning'),
                
                Tables\Columns\TextColumn::make('monthly_installment')
                    ->label('Monthly Payment')
                    ->money('PHP')
                    ->alignEnd()
                    ->weight('medium')
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('M d, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->iconButton(),
                Tables\Actions\EditAction::make()
                    ->iconButton(),
                Tables\Actions\DeleteAction::make()
                    ->iconButton(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('No programs yet')
            ->emptyStateDescription('Create your first program/package to start enrolling students.')
            ->emptyStateIcon('heroicon-o-cube-transparent')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Create Program')
                    ->icon('heroicon-o-plus'),
            ])
            ->defaultSort('name')
            ->striped();
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
            'index' => Pages\ListPackages::route('/'),
            'create' => Pages\CreatePackage::route('/create'),
            'edit' => Pages\EditPackage::route('/{record}/edit'),
        ];
    }
}
