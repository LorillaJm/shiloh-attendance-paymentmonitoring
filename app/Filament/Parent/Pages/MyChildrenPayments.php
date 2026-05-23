<?php

namespace App\Filament\Parent\Pages;

use Filament\Pages\Page;
use Filament\Tables\Table;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use App\Models\PaymentTransaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;

class MyChildrenPayments extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-m-credit-card';
    protected static string $view = 'filament.parent.pages.my-children-payments';
    protected static ?string $navigationLabel = 'Payments';
    protected static ?string $title = 'Payment History';
    protected static ?int $navigationSort = 5;

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
            ->columns([
                TextColumn::make('enrollment.student.full_name')
                    ->label('Student')
                    ->searchable(['first_name', 'last_name'])
                    ->sortable(),
                TextColumn::make('transaction_date')
                    ->label('Date')
                    ->date('M d, Y')
                    ->sortable(),
                TextColumn::make('amount')
                    ->label('Amount')
                    ->money('PHP')
                    ->sortable(),
                TextColumn::make('type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'PAYMENT' => 'success',
                        'REFUND' => 'warning',
                        default => 'gray',
                    }),
                TextColumn::make('payment_method')
                    ->label('Method'),
                TextColumn::make('reference_no')
                    ->label('Reference')
                    ->placeholder('-')
                    ->toggleable(),
                TextColumn::make('remarks')
                    ->limit(40)
                    ->placeholder('-')
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('student')
                    ->label('Student')
                    ->options(function () {
                        $guardian = Auth::user()->guardian;
                        if (!$guardian) return [];
                        return $guardian->students->pluck('full_name', 'id');
                    })
                    ->query(function (Builder $query, array $data) {
                        if (!empty($data['value'])) {
                            $query->whereHas('enrollment', function ($q) use ($data) {
                                $q->where('student_id', $data['value']);
                            });
                        }
                    }),
                SelectFilter::make('type')
                    ->options([
                        'PAYMENT' => 'Payment',
                        'REFUND' => 'Refund',
                    ]),
                \Filament\Tables\Filters\Filter::make('transaction_date')
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
                                fn (Builder $query, $date): Builder => $query->whereDate('transaction_date', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('transaction_date', '<=', $date),
                            );
                    }),
            ])
            ->defaultSort('transaction_date', 'desc')
            ->paginated([10, 25, 50]);
    }

    protected function getTableQuery(): Builder
    {
        $guardian = Auth::user()->guardian;
        
        if (!$guardian) {
            return PaymentTransaction::query()->whereRaw('1 = 0');
        }

        $studentIds = $guardian->students->pluck('id');

        return PaymentTransaction::query()
            ->whereHas('enrollment', function ($query) use ($studentIds) {
                $query->whereIn('student_id', $studentIds);
            })
            ->with(['enrollment.student']);
    }

    public static function canAccess(): bool
    {
        return Auth::check() && Auth::user()->isParent();
    }
}
