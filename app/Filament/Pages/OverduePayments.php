<?php

namespace App\Filament\Pages;

use App\Models\PaymentSchedule;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;

class OverduePayments extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-exclamation-triangle';

    protected static string $view = 'filament.pages.overdue-payments';

    protected static ?string $navigationGroup = 'Payment Management';

    protected static ?string $navigationLabel = 'Overdue Payments';

    protected static ?int $navigationSort = 2;

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public function table(Table $table): Table
    {
        // Get polling interval from config
        $pollInterval = $this->getPollingInterval();

        return $table
            ->query(
                PaymentSchedule::query()
                    ->with(['enrollment.student', 'enrollment.package'])
                    ->where('status', 'UNPAID')
                    ->whereNotNull('due_date')
                    ->where('due_date', '<', now()->format('Y-m-d'))
                    ->orderBy('due_date')
            )
            ->columns([
                Tables\Columns\TextColumn::make('enrollment.student.student_no')
                    ->label('Student No')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('enrollment.student.full_name')
                    ->label('Student Name')
                    ->searchable(['first_name', 'last_name']),

                Tables\Columns\TextColumn::make('enrollment.package.name')
                    ->label('Package'),

                Tables\Columns\TextColumn::make('installment_no')
                    ->label('Installment')
                    ->formatStateUsing(fn ($state) => $state == 0 ? 'Downpayment' : "Installment #{$state}"),

                Tables\Columns\TextColumn::make('due_date')
                    ->date()
                    ->sortable()
                    ->description(fn ($record) => $record->due_date->diffForHumans()),

                Tables\Columns\TextColumn::make('amount_due')
                    ->money('PHP')
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('computed_status')
                    ->label('Status')
                    ->colors([
                        'danger' => 'OVERDUE',
                    ]),

                Tables\Columns\TextColumn::make('enrollment.student.guardian_contact')
                    ->label('Contact')
                    ->placeholder('-'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('package')
                    ->relationship('enrollment.package', 'name'),
            ])
            ->actions([
                Tables\Actions\Action::make('view_enrollment')
                    ->label('View')
                    ->icon('heroicon-o-eye')
                    ->url(fn ($record) => route('filament.admin.resources.enrollments.view', $record->enrollment_id)),
            ])
            ->heading('Overdue Payments')
            ->description('All unpaid installments past their due date')
            ->poll($pollInterval)
            ->defaultPaginationPageOption(25);
    }

    protected function getPollingInterval(): ?string
    {
        // Check if polling is enabled and user has permission
        if (!config('realtime.enabled', true)) {
            return null;
        }

        $user = auth()->user();
        $allowedRoles = config('realtime.allowed_roles', ['ADMIN', 'USER']);
        
        if (!in_array($user->role->value ?? $user->role, $allowedRoles)) {
            return null;
        }

        // Get interval from config (in seconds)
        $interval = config('realtime.overdue_payments_poll_interval', 20);
        
        return $interval ? "{$interval}s" : null;
    }

    public function getOverdueCount(): int
    {
        // Cache the count for performance
        $cacheKey = 'overdue_payments_count';
        $cacheTtl = config('realtime.cache_ttl.overdue_count', 15);

        return Cache::remember($cacheKey, $cacheTtl, function() {
            return PaymentSchedule::where('status', 'UNPAID')
                ->whereNotNull('due_date')
                ->where('due_date', '<', now()->format('Y-m-d'))
                ->count();
        });
    }

    public function getOverdueTotal(): float
    {
        // Cache the total for performance
        $cacheKey = 'overdue_payments_total';
        $cacheTtl = config('realtime.cache_ttl.overdue_count', 15);

        return Cache::remember($cacheKey, $cacheTtl, function() {
            return PaymentSchedule::where('status', 'UNPAID')
                ->whereNotNull('due_date')
                ->where('due_date', '<', now()->format('Y-m-d'))
                ->sum('amount_due');
        });
    }
}
