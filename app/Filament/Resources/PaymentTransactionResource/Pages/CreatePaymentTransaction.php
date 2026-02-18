<?php

namespace App\Filament\Resources\PaymentTransactionResource\Pages;

use App\Filament\Resources\PaymentTransactionResource;
use App\Services\PaymentLedgerService;
use Filament\Resources\Pages\CreateRecord;

class CreatePaymentTransaction extends CreateRecord
{
    protected static string $resource = PaymentTransactionResource::class;

    protected function afterCreate(): void
    {
        // Recalculate enrollment balance after transaction
        $enrollment = $this->record->enrollment;
        PaymentLedgerService::applyPaymentToSchedules($enrollment, 0); // Recalculate
    }
}
