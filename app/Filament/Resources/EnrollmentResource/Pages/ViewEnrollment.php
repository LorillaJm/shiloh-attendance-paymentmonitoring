<?php

namespace App\Filament\Resources\EnrollmentResource\Pages;

use App\Filament\Resources\EnrollmentResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class ViewEnrollment extends ViewRecord
{
    protected static string $resource = EnrollmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Enrollment Information')
                    ->schema([
                        Infolists\Components\TextEntry::make('student.student_no')
                            ->label('Student Number'),
                        Infolists\Components\TextEntry::make('student.full_name')
                            ->label('Student Name'),
                        Infolists\Components\TextEntry::make('package.name')
                            ->label('Package'),
                        Infolists\Components\TextEntry::make('enrollment_date')
                            ->date(),
                        Infolists\Components\TextEntry::make('status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'ACTIVE' => 'success',
                                'CANCELLED' => 'danger',
                            }),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('Balance Overview')
                    ->description('Real-time payment status and balance information')
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('total_fee')
                                    ->label('Total Fee')
                                    ->money('PHP')
                                    ->size('lg'),
                                Infolists\Components\TextEntry::make('total_paid')
                                    ->label('Total Paid')
                                    ->money('PHP')
                                    ->color('success')
                                    ->size('lg'),
                                Infolists\Components\TextEntry::make('remaining_balance_computed')
                                    ->label('Balance Due')
                                    ->money('PHP')
                                    ->color(fn ($state) => $state > 0 ? 'danger' : 'success')
                                    ->size('lg')
                                    ->weight('bold'),
                            ]),
                    ]),

                Infolists\Components\Section::make('Payment Statistics')
                    ->schema([
                        Infolists\Components\TextEntry::make('paid_count')
                            ->label('Paid Installments')
                            ->suffix(fn ($record) => ' / ' . $record->paymentSchedules()->count())
                            ->color('success'),
                        Infolists\Components\TextEntry::make('unpaid_count')
                            ->label('Unpaid')
                            ->color('warning'),
                        Infolists\Components\TextEntry::make('overdue_count')
                            ->label('Overdue')
                            ->color('danger'),
                    ])
                    ->columns(3),

                Infolists\Components\Section::make('Payment Details')
                    ->schema([
                        Infolists\Components\TextEntry::make('downpayment_percent')
                            ->suffix('%'),
                        Infolists\Components\TextEntry::make('downpayment_amount')
                            ->money('PHP'),
                        Infolists\Components\TextEntry::make('remaining_balance')
                            ->label('Initial Balance')
                            ->money('PHP'),
                        Infolists\Components\TextEntry::make('package.installment_months')
                            ->label('Installment Period')
                            ->suffix(' months'),
                    ])
                    ->columns(4)
                    ->collapsed(),
            ]);
    }
}
