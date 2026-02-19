<?php

namespace App\Observers;

use App\Models\PaymentSchedule;

class PaymentScheduleObserver
{
    /**
     * Handle the PaymentSchedule "created" event.
     */
    public function created(PaymentSchedule $paymentSchedule): void
    {
        $this->clearDashboardCaches();
    }

    /**
     * Handle the PaymentSchedule "updated" event.
     */
    public function updated(PaymentSchedule $paymentSchedule): void
    {
        // Clear caches if status changed to PAID or due_date changed
        if ($paymentSchedule->wasChanged(['status', 'due_date', 'paid_at'])) {
            $this->clearDashboardCaches();
        }
    }

    /**
     * Handle the PaymentSchedule "deleted" event.
     */
    public function deleted(PaymentSchedule $paymentSchedule): void
    {
        $this->clearDashboardCaches();
    }

    /**
     * Clear dashboard caches
     */
    private function clearDashboardCaches(): void
    {
        \App\Services\DashboardCacheService::clearPaymentCaches();
    }

    /**
     * Handle the PaymentSchedule "restored" event.
     */
    public function restored(PaymentSchedule $paymentSchedule): void
    {
        //
    }

    /**
     * Handle the PaymentSchedule "force deleted" event.
     */
    public function forceDeleted(PaymentSchedule $paymentSchedule): void
    {
        //
    }
}
