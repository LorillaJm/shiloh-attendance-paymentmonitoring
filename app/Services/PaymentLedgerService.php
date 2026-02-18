<?php

namespace App\Services;

use App\Models\Enrollment;
use App\Models\PaymentSchedule;
use App\Models\PaymentTransaction;
use Illuminate\Support\Facades\DB;

class PaymentLedgerService
{
    /**
     * Record a payment transaction and update schedules.
     */
    public static function recordPayment(
        Enrollment $enrollment,
        float $amount,
        string $paymentMethod,
        ?string $referenceNo = null,
        ?string $remarks = null,
        ?int $userId = null
    ): PaymentTransaction {
        return DB::transaction(function () use ($enrollment, $amount, $paymentMethod, $referenceNo, $remarks, $userId) {
            // Create transaction
            $transaction = PaymentTransaction::create([
                'enrollment_id' => $enrollment->id,
                'amount' => $amount,
                'type' => 'PAYMENT',
                'transaction_date' => now()->format('Y-m-d'),
                'payment_method' => $paymentMethod,
                'reference_no' => $referenceNo,
                'remarks' => $remarks,
                'processed_by_user_id' => $userId ?? auth()->id(),
            ]);

            // Apply payment to unpaid schedules
            self::applyPaymentToSchedules($enrollment, $amount);

            return $transaction;
        });
    }

    /**
     * Apply payment amount to unpaid schedules in order.
     */
    public static function applyPaymentToSchedules(Enrollment $enrollment, float $amount): void
    {
        $remaining = $amount;
        
        $unpaidSchedules = $enrollment->paymentSchedules()
            ->where('status', 'UNPAID')
            ->orWhere('status', 'OVERDUE')
            ->orderBy('due_date')
            ->orderBy('installment_no')
            ->get();

        foreach ($unpaidSchedules as $schedule) {
            if ($remaining <= 0) {
                break;
            }

            $scheduleBalance = $schedule->amount_due - $schedule->total_paid_for_schedule;

            if ($remaining >= $scheduleBalance) {
                // Full payment for this schedule
                $schedule->update([
                    'status' => 'PAID',
                    'paid_at' => now(),
                ]);
                $remaining -= $scheduleBalance;
            } else {
                // Partial payment - schedule remains unpaid but has transaction
                $remaining = 0;
            }
        }

        // Update enrollment remaining balance
        $enrollment->update([
            'remaining_balance' => max(0, $enrollment->total_fee - $enrollment->total_paid),
        ]);
    }

    /**
     * Get payment balance for an enrollment.
     */
    public static function getBalance(Enrollment $enrollment): array
    {
        $totalPaid = $enrollment->paymentTransactions()
            ->where('type', 'PAYMENT')
            ->sum('amount');

        $totalAdjustments = $enrollment->paymentTransactions()
            ->where('type', 'ADJUSTMENT')
            ->sum('amount');

        $totalRefunds = $enrollment->paymentTransactions()
            ->where('type', 'REFUND')
            ->sum('amount');

        $netPaid = $totalPaid + $totalAdjustments - $totalRefunds;
        $balance = $enrollment->total_fee - $netPaid;

        return [
            'total_fee' => $enrollment->total_fee,
            'total_paid' => $totalPaid,
            'adjustments' => $totalAdjustments,
            'refunds' => $totalRefunds,
            'net_paid' => $netPaid,
            'balance' => max(0, $balance),
        ];
    }
}
