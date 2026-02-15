<?php

namespace App\Filament\Widgets;

use App\Models\PaymentSchedule;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class RecentPaymentsTable extends BaseWidget
{
    protected static ?int $sort = 6;
    
    protected static ?string $heading = 'Recent Payments';
    
    protected int | string | array $columnSpan = 'full';
    
    protected static ?string $pollingInterval = '15s';

    public function table(Table $table): Table
    {
        $today = now('Asia/Manila');
        $sevenDaysAgo = $today->copy()->subDays(7);

        return $table
            ->query(
                PaymentSchedule::query()
                    ->with(['enrollment.student', 'enrollment.package'])
                    ->where('status', 'PAID')
                    ->whereBetween('paid_at', [$sevenDaysAgo->startOfDay(), $today->endOfDay()])
                    ->orderBy('paid_at', 'desc')
                    ->limit(20)
            )
            ->columns([
                Tables\Columns\TextColumn::make('paid_at')
                    ->label('Payment Date')
                    ->dateTime('M d, Y h:i A')
                    ->sortable()
                    ->description(fn ($record) => $record->paid_at->diffForHumans())
                    ->weight('semibold')
                    ->color('success'),

                Tables\Columns\TextColumn::make('enrollment.student.student_no')
                    ->label('Student No')
                    ->searchable()
                    ->weight('semibold')
                    ->color('primary'),

                Tables\Columns\TextColumn::make('enrollment.student.full_name')
                    ->label('Student Name')
                    ->searchable(['first_name', 'last_name'])
                    ->weight('medium'),

                Tables\Columns\TextColumn::make('enrollment.package.name')
                    ->label('Program')
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('installment_no')
                    ->label('Payment #')
                    ->formatStateUsing(fn ($state) => $state == 0 ? 'Down Payment' : "Payment #{$state}")
                    ->badge()
                    ->color('success'),

                Tables\Columns\TextColumn::make('amount_due')
                    ->label('Amount')
                    ->money('PHP')
                    ->sortable()
                    ->weight('semibold')
                    ->color('success')
                    ->alignEnd(),

                Tables\Columns\TextColumn::make('payment_method')
                    ->badge()
                    ->colors([
                        'success' => 'CASH',
                        'info' => ['BANK_TRANSFER', 'GCASH', 'PAYMAYA'],
                        'warning' => 'CHECK',
                    ]),

                Tables\Columns\TextColumn::make('receipt_no')
                    ->label('Receipt')
                    ->placeholder('-')
                    ->copyable()
                    ->toggleable(),
            ])
            ->actions([
                Tables\Actions\Action::make('view_student')
                    ->label('Student')
                    ->icon('heroicon-o-user')
                    ->url(fn ($record) => route('filament.admin.resources.students.edit', $record->enrollment->student_id))
                    ->openUrlInNewTab(),
                    
                Tables\Actions\Action::make('view_enrollment')
                    ->label('Enrollment')
                    ->icon('heroicon-o-academic-cap')
                    ->url(fn ($record) => route('filament.admin.resources.enrollments.view', $record->enrollment_id))
                    ->openUrlInNewTab(),
            ])
            ->paginated(false)
            ->heading('Recent Payments (Last 7 Days - Top 20)')
            ->description('Latest payment transactions');
    }
}
