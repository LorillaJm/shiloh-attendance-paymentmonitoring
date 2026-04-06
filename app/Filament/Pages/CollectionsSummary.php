<?php

namespace App\Filament\Pages;

use App\Models\PaymentSchedule;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;

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
        // Only show to SUPERADMIN and ADMIN (financial data)
        $user = auth()->user();
        return $user && ($user->isSuperadmin() || $user->isAdmin());
    }

    public static function canAccess(): bool
    {
        // Only SUPERADMIN and ADMIN can access financial summaries
        $user = auth()->user();
        return $user && ($user->isSuperadmin() || $user->isAdmin());
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

        // Single query instead of 4 separate queries (~600ms saved)
        $stats = DB::selectOne("
            SELECT
                COALESCE(SUM(CASE WHEN DATE(paid_at) = ? THEN amount_due ELSE 0 END), 0) as paid_today,
                COALESCE(SUM(amount_due), 0) as paid_this_month,
                COUNT(CASE WHEN DATE(paid_at) = ? THEN 1 END) as count_today,
                COUNT(*) as count_this_month
            FROM payment_schedules
            WHERE status = 'PAID'
              AND paid_at BETWEEN ? AND ?
        ", [$today, $today, $monthStart, $monthEnd]);

        return [
            'paid_today' => $stats->paid_today ?? 0,
            'paid_this_month' => $stats->paid_this_month ?? 0,
            'count_today' => $stats->count_today ?? 0,
            'count_this_month' => $stats->count_this_month ?? 0,
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
