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

class DuePayments extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-clock';

    protected static string $view = 'filament.pages.due-payments';

    protected static ?string $navigationGroup = 'Payment Management';

    protected static ?string $navigationLabel = 'Due Payments';

    protected static ?int $navigationSort = 1;

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
        // Calculate next 15th using same logic as schedule generation
        // This ensures consistency with when payments are actually due
        $next15th = now()->startOfMonth()->addMonth()->day(15);

        // Get polling interval from config
        $pollInterval = $this->getPollingInterval();

        return $table
            ->query(
                PaymentSchedule::query()
                    ->with(['enrollment.student', 'enrollment.package'])
                    ->where('status', 'UNPAID')
                    ->whereDate('due_date', $next15th->format('Y-m-d'))
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
                    ->sortable(),

                Tables\Columns\TextColumn::make('amount_due')
                    ->money('PHP')
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'UNPAID',
                    ]),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('view_enrollment')
                    ->label('View Enrollment')
                    ->icon('heroicon-o-eye')
                    ->url(fn ($record) => route('filament.admin.resources.enrollments.view', $record->enrollment_id)),
            ])
            ->heading('Due on Next 15th: ' . $next15th->format('F d, Y'))
            ->description('All unpaid installments due on the upcoming 15th of the month (next month)')
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
        $interval = config('realtime.due_payments_poll_interval', 20);
        
        return $interval ? "{$interval}s" : null;
    }

    public function getDueCount(): int
    {
        // Cache the count for performance
        $cacheKey = 'due_payments_count';
        $cacheTtl = config('realtime.cache_ttl.due_count', 15);

        return Cache::remember($cacheKey, $cacheTtl, function() {
            $next15th = now()->startOfMonth()->addMonth()->day(15);
            
            return PaymentSchedule::where('status', 'UNPAID')
                ->whereDate('due_date', $next15th->format('Y-m-d'))
                ->count();
        });
    }
}
