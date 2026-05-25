<?php

namespace App\Filament\Resources\EnrollmentResource\RelationManagers;

use App\Services\PaymentScheduleService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Notifications\Notification;

class PaymentSchedulesRelationManager extends RelationManager
{
    protected static string $relationship = 'paymentSchedules';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('installment_no')
                    ->label('Installment Number')
                    ->disabled(),
                    
                Forms\Components\DatePicker::make('due_date')
                    ->disabled(),
                    
                Forms\Components\TextInput::make('amount_due')
                    ->prefix('₱')
                    ->disabled(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('installment_no')
            ->columns([
                Tables\Columns\TextColumn::make('installment_no')
                    ->label('Installment')
                    ->formatStateUsing(fn ($state) => $state == 0 ? 'Downpayment' : "Installment #{$state}")
                    ->sortable(),

                Tables\Columns\TextColumn::make('due_date')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('amount_due')
                    ->money('PHP')
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('computed_status')
                    ->label('Status')
                    ->colors([
                        'success' => 'PAID',
                        'warning' => 'UNPAID',
                        'danger' => 'OVERDUE',
                    ])
                    ->sortable(),

                Tables\Columns\TextColumn::make('paid_at')
                    ->dateTime()
                    ->placeholder('-')
                    ->sortable(),

                Tables\Columns\TextColumn::make('payment_method')
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('receipt_no')
                    ->label('Receipt')
                    ->placeholder('-'),
            ])
            ->defaultSort('installment_no')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'UNPAID' => 'Unpaid',
                        'PAID' => 'Paid',
                        'OVERDUE' => 'Overdue',
                    ]),
            ])
            ->headerActions([
                // No create action - schedules are auto-generated
            ])
            ->actions([
                Tables\Actions\Action::make('markAsPaid')
                    ->label('Mark as Paid')
                    ->icon('heroicon-m-check-circle')
                    ->color('success')
                    ->visible(fn ($record) => $record->status !== 'PAID')
                    ->form([
                        Forms\Components\DateTimePicker::make('paid_at')
                            ->label('Payment Date')
                            ->default(now())
                            ->required()
                            ->maxDate(now())
                            ->native(false)
                            ->validationMessages([
                                'max_date' => 'Payment date cannot be in the future.',
                            ]),

                        Forms\Components\Select::make('payment_method')
                            ->label('Payment Method')
                            ->options([
                                'CASH' => 'Cash',
                                'BANK_TRANSFER' => 'Bank Transfer',
                                'GCASH' => 'GCash',
                                'PAYMAYA' => 'PayMaya',
                                'CHECK' => 'Check',
                                'CREDIT_CARD' => 'Credit Card',
                                'DEBIT_CARD' => 'Debit Card',
                            ])
                            ->required(),

                        Forms\Components\TextInput::make('receipt_no')
                            ->label('Receipt Number')
                            ->placeholder('Optional'),

                        Forms\Components\Textarea::make('remarks')
                            ->label('Remarks')
                            ->placeholder('Optional notes')
                            ->rows(3),
                    ])
                    ->action(function ($record, array $data) {
                        // Defensive check: prevent marking already paid schedules
                        if ($record->status === 'PAID') {
                            Notification::make()
                                ->warning()
                                ->title('Already Paid')
                                ->body('This payment has already been marked as paid.')
                                ->send();
                            return;
                        }
                        
                        // Update payment schedule
                        $record->update([
                            'status' => 'PAID',
                            'paid_at' => $data['paid_at'],
                            'payment_method' => $data['payment_method'],
                            'receipt_no' => $data['receipt_no'] ?? null,
                            'remarks' => $data['remarks'] ?? null,
                        ]);

                        // Create payment transaction for ledger
                        \App\Models\PaymentTransaction::create([
                            'enrollment_id' => $record->enrollment_id,
                            'payment_schedule_id' => $record->id,
                            'transaction_date' => $data['paid_at'],
                            'type' => 'PAYMENT',
                            'amount' => $record->amount_due,
                            'payment_method' => $data['payment_method'],
                            'reference_no' => $data['receipt_no'] ?? null,
                            'remarks' => $data['remarks'] ?? null,
                            'processed_by_user_id' => auth()->id(),
                        ]);

                        // Update enrollment remaining balance (use payment_schedules as source of truth)
                        $enrollment = $record->enrollment;
                        if ($enrollment) {
                            $totalPaid = $enrollment->paymentSchedules()
                                ->where('status', 'PAID')
                                ->sum('amount_due');
                            $enrollment->update([
                                'remaining_balance' => max(0, $enrollment->total_fee - $totalPaid),
                            ]);
                        }

                        // Log the activity
                        \App\Services\ActivityLogger::log(
                            description: "Payment marked as paid",
                            subject: $record,
                            properties: [
                                'enrollment_id' => $record->enrollment_id,
                                'installment_no' => $record->installment_no,
                                'amount' => $record->amount_due,
                                'payment_method' => $data['payment_method'],
                                'receipt_no' => $data['receipt_no'] ?? null,
                                'paid_at' => $data['paid_at'],
                                'marked_by' => auth()->user()->name,
                            ],
                            logName: 'payment'
                        );

                        Notification::make()
                            ->success()
                            ->title('Payment Recorded')
                            ->body("Payment of ₱" . number_format($record->amount_due, 2) . " marked as paid.")
                            ->send();

                        // Force page refresh to update Balance Overview
                        redirect(\App\Filament\Resources\EnrollmentResource::getUrl('view', ['record' => $record->enrollment_id]));
                    }),

                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                // No bulk actions for payment schedules
            ]);
    }
}
