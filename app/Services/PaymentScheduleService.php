<?php

namespace App\Services;

use App\Models\Enrollment;
use App\Models\PaymentSchedule;
use App\Events\PaymentUpdated;
use App\Events\EnrollmentCreated;
use Carbon\Carbon;

class PaymentScheduleService
{
    /**
     * Generate payment schedules for an enrollment.
     * 
     * Business Rules:
     * - Downpayment is installment_no = 0, due on enrollment_date
     * - Remaining balance is divided into equal installments
     * - Due dates are ALWAYS on the 15th of the month
     * - First installment due is the next 15th AFTER enrollment_date
     *   Example: Enrolled Jan 10 → First due Feb 15
     *   Example: Enrolled Jan 15 → First due Feb 15
     *   Example: Enrolled Jan 20 → First due Feb 15
     * - Last installment is adjusted for rounding differences
     * - All amounts rounded to 2 decimal places
     */
    public function generateSchedules(Enrollment $enrollment): void
    {
        // Delete existing schedules if regenerating
        $enrollment->paymentSchedules()->delete();

        $schedules = [];

        // 1. Create downpayment schedule (installment_no = 0)
        $schedules[] = [
            'enrollment_id' => $enrollment->id,
            'installment_no' => 0,
            'due_date' => $enrollment->enrollment_date,
            'amount_due' => round($enrollment->downpayment_amount, 2),
            'status' => 'UNPAID',
            'created_at' => now(),
            'updated_at' => now(),
        ];

        // 2. Calculate installment amounts
        $remainingBalance = $enrollment->remaining_balance;
        $installmentMonths = $enrollment->package->installment_months;
        
        // Handle case where there are no installments (100% downpayment)
        if ($installmentMonths == 0) {
            // Only downpayment, no installments
            PaymentSchedule::insert($schedules);
            return;
        }
        
        // Base installment amount (rounded down to 2 decimals)
        $baseInstallment = floor(($remainingBalance / $installmentMonths) * 100) / 100;
        
        // Calculate the difference to adjust in the last installment
        $totalBaseInstallments = $baseInstallment * $installmentMonths;
        $lastInstallmentAdjustment = round($remainingBalance - $totalBaseInstallments, 2);

        // 3. Generate installment schedules
        $enrollmentDate = Carbon::parse($enrollment->enrollment_date);
        
        // Find the first due date: next 15th AFTER enrollment_date
        // Always go to next month's 15th
        // Use startOfMonth() then add 1 month to avoid day overflow issues
        $firstDueDate = $enrollmentDate->copy()->startOfMonth()->addMonth()->day(15);
        
        for ($i = 1; $i <= $installmentMonths; $i++) {
            // Calculate due date: first due date + (i-1) months
            $dueDate = $firstDueDate->copy()->addMonths($i - 1);
            
            // Amount for this installment
            $amount = $baseInstallment;
            
            // Adjust last installment to ensure exact total
            if ($i === $installmentMonths) {
                $amount = round($amount + $lastInstallmentAdjustment, 2);
            }

            $schedules[] = [
                'enrollment_id' => $enrollment->id,
                'installment_no' => $i,
                'due_date' => $dueDate->format('Y-m-d'),
                'amount_due' => $amount,
                'status' => 'UNPAID',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // 4. Bulk insert all schedules
        PaymentSchedule::insert($schedules);
        
        // Broadcast enrollment created event
        broadcast(new EnrollmentCreated(
            $enrollment->id,
            $enrollment->student_id,
            count($schedules)
        ))->toOthers();
        
        // Log schedule generation
        ActivityLogger::log(
            description: "Payment schedules generated",
            subject: $enrollment,
            properties: [
                'enrollment_id' => $enrollment->id,
                'student_id' => $enrollment->student_id,
                'package_id' => $enrollment->package_id,
                'total_schedules' => count($schedules),
                'total_fee' => $enrollment->total_fee,
                'downpayment_amount' => $enrollment->downpayment_amount,
                'installment_months' => $installmentMonths,
            ],
            logName: 'payment'
        );
    }

    /**
     * Mark a payment schedule as paid.
     */
    public function markAsPaid(
        PaymentSchedule $schedule,
        string $paymentMethod,
        ?string $receiptNo = null,
        ?string $remarks = null
    ): void {
        $schedule->update([
            'status' => 'PAID',
            'paid_at' => now(),
            'payment_method' => $paymentMethod,
            'receipt_no' => $receiptNo,
            'remarks' => $remarks,
        ]);

        // Broadcast real-time update
        broadcast(new PaymentUpdated(
            $schedule->id,
            'PAID',
            'paid'
        ))->toOthers();

        // TODO: Add activity logging if needed
        // ActivityLogger::log(...);
    }

    /**
     * Update overdue statuses for unpaid schedules past their due date.
     */
    public function updateOverdueStatuses(): void
    {
        PaymentSchedule::where('status', 'UNPAID')
            ->where('due_date', '<', now()->format('Y-m-d'))
            ->update(['status' => 'OVERDUE']);
    }
}
