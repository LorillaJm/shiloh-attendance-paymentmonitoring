<?php

namespace Tests\Feature;

use App\Models\Enrollment;
use App\Models\PaymentSchedule;
use App\Models\PaymentTransaction;
use App\Models\Student;
use App\Models\Package;
use App\Models\User;
use App\Services\PaymentLedgerService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PartialPaymentTest extends TestCase
{
    use RefreshDatabase;

    public function test_partial_payment_is_recorded_correctly(): void
    {
        $student = Student::factory()->create();
        $package = Package::factory()->create(['total_fee' => 10000]);
        
        $enrollment = Enrollment::factory()->create([
            'student_id' => $student->id,
            'package_id' => $package->id,
            'total_fee' => 10000,
            'remaining_balance' => 10000,
        ]);

        PaymentSchedule::create([
            'enrollment_id' => $enrollment->id,
            'installment_no' => 1,
            'due_date' => now(),
            'amount_due' => 3333.33,
            'status' => 'UNPAID',
        ]);

        $user = User::factory()->create();
        
        // Make partial payment of 1500
        $transaction = PaymentLedgerService::recordPayment(
            $enrollment,
            1500,
            'Cash',
            'REC-001',
            'Partial payment',
            $user->id
        );

        $this->assertEquals(1500, $transaction->amount);
        $this->assertEquals('PAYMENT', $transaction->type);
        
        // Balance should reflect partial payment
        $balance = PaymentLedgerService::getBalance($enrollment);
        $this->assertEquals(1500, $balance['total_paid']);
        $this->assertEquals(8500, $balance['balance']);
    }

    public function test_advance_payment_covers_multiple_schedules(): void
    {
        $student = Student::factory()->create();
        $package = Package::factory()->create(['total_fee' => 9000]);
        
        $enrollment = Enrollment::factory()->create([
            'student_id' => $student->id,
            'package_id' => $package->id,
            'total_fee' => 9000,
            'remaining_balance' => 9000,
        ]);

        // Create 3 monthly schedules
        PaymentSchedule::create([
            'enrollment_id' => $enrollment->id,
            'installment_no' => 1,
            'due_date' => now(),
            'amount_due' => 3000,
            'status' => 'UNPAID',
        ]);

        PaymentSchedule::create([
            'enrollment_id' => $enrollment->id,
            'installment_no' => 2,
            'due_date' => now()->addMonth(),
            'amount_due' => 3000,
            'status' => 'UNPAID',
        ]);

        PaymentSchedule::create([
            'enrollment_id' => $enrollment->id,
            'installment_no' => 3,
            'due_date' => now()->addMonths(2),
            'amount_due' => 3000,
            'status' => 'UNPAID',
        ]);

        $user = User::factory()->create();
        
        // Make advance payment covering first 2 schedules
        PaymentLedgerService::recordPayment($enrollment, 6000, 'Bank Transfer', 'REF-002', 'Advance payment', $user->id);

        $enrollment->refresh();
        
        // First two schedules should be paid
        $this->assertEquals('PAID', $enrollment->paymentSchedules()->where('installment_no', 1)->first()->status);
        $this->assertEquals('PAID', $enrollment->paymentSchedules()->where('installment_no', 2)->first()->status);
        $this->assertEquals('UNPAID', $enrollment->paymentSchedules()->where('installment_no', 3)->first()->status);
    }

    public function test_payment_transactions_are_tracked_in_ledger(): void
    {
        $student = Student::factory()->create();
        $package = Package::factory()->create(['total_fee' => 5000]);
        
        $enrollment = Enrollment::factory()->create([
            'student_id' => $student->id,
            'package_id' => $package->id,
            'total_fee' => 5000,
        ]);

        $user = User::factory()->create();

        // Multiple payments
        PaymentLedgerService::recordPayment($enrollment, 2000, 'Cash', 'REC-001', null, $user->id);
        PaymentLedgerService::recordPayment($enrollment, 1500, 'Cash', 'REC-002', null, $user->id);
        PaymentLedgerService::recordPayment($enrollment, 1500, 'Bank', 'REC-003', null, $user->id);

        $this->assertEquals(3, $enrollment->paymentTransactions()->count());
        $this->assertEquals(5000, $enrollment->paymentTransactions()->sum('amount'));
    }
}
