<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentTransactionResource\Pages;
use App\Models\PaymentTransaction;
use App\Models\Enrollment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;

class PaymentTransactionResource extends Resource
{
    protected static ?string $model = PaymentTransaction::class;
    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationGroup = 'Payment Management';
    protected static ?string $navigationLabel = 'Payment Transactions';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('enrollment_id')
                    ->label('Enrollment')
                    ->searchable()
                    ->required()
                    ->getSearchResultsUsing(function (string $search): array {
                        return Enrollment::query()
                            ->with(['student', 'package'])
                            ->whereHas('student', function ($q) use ($search) {
                                $q->where('first_name', 'like', "%{$search}%")
                                    ->orWhere('last_name', 'like', "%{$search}%")
                                    ->orWhere('student_no', 'like', "%{$search}%");
                            })
                            ->limit(50)
                            ->get()
                            ->mapWithKeys(fn ($e) => [$e->id => "{$e->student->full_name} - {$e->package->name}"])
                            ->toArray();
                    })
                    ->getOptionLabelUsing(function ($value): ?string {
                        $enrollment = Enrollment::with(['student', 'package'])->find($value);
                        return $enrollment ? "{$enrollment->student->full_name} - {$enrollment->package->name}" : null;
                    }),
                Forms\Components\TextInput::make('amount')
                    ->numeric()
                    ->prefix('₱')
                    ->required(),
                Forms\Components\Select::make('type')
                    ->options([
                        'PAYMENT' => 'Payment',
                        'ADJUSTMENT' => 'Adjustment',
                        'REFUND' => 'Refund',
                    ])
                    ->default('PAYMENT')
                    ->required(),
                Forms\Components\DatePicker::make('transaction_date')
                    ->default(now())
                    ->required(),
                Forms\Components\TextInput::make('payment_method')
                    ->placeholder('Cash, Bank Transfer, etc.'),
                Forms\Components\TextInput::make('reference_no')
                    ->label('Receipt/Reference No'),
                Forms\Components\Textarea::make('remarks')->rows(2),
                Forms\Components\Hidden::make('processed_by_user_id')
                    ->default(Auth::id()),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with(['enrollment.student', 'enrollment.package', 'processedBy']))
            ->columns([
                Tables\Columns\TextColumn::make('transaction_date')->date()->sortable(),
                Tables\Columns\TextColumn::make('enrollment.student.full_name')->searchable(),
                Tables\Columns\TextColumn::make('amount')->money('PHP')->sortable(),
                Tables\Columns\TextColumn::make('type')->badge()->color(fn (string $state): string => match ($state) {
                    'PAYMENT' => 'success',
                    'ADJUSTMENT' => 'warning',
                    'REFUND' => 'danger',
                }),
                Tables\Columns\TextColumn::make('payment_method'),
                Tables\Columns\TextColumn::make('reference_no')->searchable(),
                Tables\Columns\TextColumn::make('processedBy.name')->label('Processed By'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type'),
                Tables\Filters\Filter::make('transaction_date')
                    ->form([
                        Forms\Components\DatePicker::make('from'),
                        Forms\Components\DatePicker::make('until'),
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('transaction_date', 'desc')
            ->defaultPaginationPageOption(25)
            ->paginationPageOptions([10, 25, 50, 100])
            ->deferLoading();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPaymentTransactions::route('/'),
            'create' => Pages\CreatePaymentTransaction::route('/create'),
            'edit' => Pages\EditPaymentTransaction::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        $user = Auth::user();
        return $user && ($user->isSuperadmin() || $user->isAdmin());
    }
}
