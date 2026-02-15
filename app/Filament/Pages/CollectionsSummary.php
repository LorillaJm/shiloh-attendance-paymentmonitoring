<?php

namespace App\Filament\Pages;

use App\Models\PaymentSchedule;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Support\Facades\DB;

class CollectionsSummary extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

    protected static string $view = 'filament.pages.collections-summary';

    protected static ?string $navigationGroup = 'Payment Monitoring';

    protected static ?string $navigationLabel = 'Collections Summary';

    protected static ?int $navigationSort = 3;

    public static function shouldRegisterNavigation(): bool
    {
        // Only show to admins (financial data)
        return auth()->user()?->isAdmin() ?? false;
    }

    public static function canAccess(): bool
    {
        // Only admins can access financial summaries
        return auth()->user()?->isAdmin() ?? false;
    }

    public function getHeading(): string
    {
        return 'Collections Summary';
    }

    public function getViewData(): array
    {
        $today = now()->format('Y-m-d');
        $monthStart = now()->startOfMonth()->format('Y-m-d');
        $monthEnd = now()->endOfMonth()->format('Y-m-d');

        $paidToday = PaymentSchedule::where('status', 'PAID')
            ->whereDate('paid_at', $today)
            ->sum('amount_due');

        $paidThisMonth = PaymentSchedule::where('status', 'PAID')
            ->whereBetween('paid_at', [$monthStart, $monthEnd])
            ->sum('amount_due');

        $countToday = PaymentSchedule::where('status', 'PAID')
            ->whereDate('paid_at', $today)
            ->count();

        $countThisMonth = PaymentSchedule::where('status', 'PAID')
            ->whereBetween('paid_at', [$monthStart, $monthEnd])
            ->count();

        return [
            'paid_today' => $paidToday,
            'paid_this_month' => $paidThisMonth,
            'count_today' => $countToday,
            'count_this_month' => $countThisMonth,
            'today_date' => now()->format('F d, Y'),
            'month_name' => now()->format('F Y'),
        ];
    }

    public function table(Table $table): Table
    {
        $monthStart = now()->startOfMonth()->format('Y-m-d');
        $monthEnd = now()->endOfMonth()->format('Y-m-d');

        return $table
            ->query(
                PaymentSchedule::query()
                    ->with(['enrollment.student', 'enrollment.package'])
                    ->where('status', 'PAID')
                    ->whereBetween('paid_at', [$monthStart, $monthEnd])
                    ->orderBy('paid_at', 'desc')
            )
            ->columns([
                Tables\Columns\TextColumn::make('paid_at')
                    ->label('Payment Date')
                    ->dateTime()
                    ->sortable(),

                Tables\Columns\TextColumn::make('enrollment.student.student_no')
                    ->label('Student No')
                    ->searchable(),

                Tables\Columns\TextColumn::make('enrollment.student.full_name')
                    ->label('Student Name')
                    ->searchable(['first_name', 'last_name']),

                Tables\Columns\TextColumn::make('installment_no')
                    ->label('Installment')
                    ->formatStateUsing(fn ($state) => $state == 0 ? 'Downpayment' : "Installment #{$state}"),

                Tables\Columns\TextColumn::make('amount_due')
                    ->label('Amount')
                    ->money('PHP')
                    ->sortable(),

                Tables\Columns\TextColumn::make('payment_method')
                    ->badge()
                    ->colors([
                        'success' => 'CASH',
                        'info' => ['BANK_TRANSFER', 'GCASH', 'PAYMAYA'],
                        'warning' => 'CHECK',
                    ]),

                Tables\Columns\TextColumn::make('receipt_no')
                    ->label('Receipt')
                    ->placeholder('-'),
            ])
            ->filters([
                Tables\Filters\Filter::make('paid_today')
                    ->label('Paid Today')
                    ->query(fn (Builder $query) => $query->whereDate('paid_at', now()->format('Y-m-d'))),
            ])
            ->actions([
                Tables\Actions\Action::make('view_enrollment')
                    ->label('View')
                    ->icon('heroicon-o-eye')
                    ->url(fn ($record) => route('filament.admin.resources.enrollments.view', $record->enrollment_id)),
            ])
            ->heading('Payment History - ' . now()->format('F Y'))
            ->description('All payments received this month');
    }
}
