<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Student;
use App\Models\Package;
use App\Models\Enrollment;
use App\Services\PaymentScheduleService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

class PaymentScheduleServiceTest extends TestCase
{
    use RefreshDatabase;

    private PaymentScheduleService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new PaymentScheduleService();
        
        // Set a fixed date for consistent testing
        Carbon::setTestNow('2026-02-14 12:00:00');
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    /**
     * Test Case 1: Enrollment before 15th generates correct due date
     */
    public function test_enrollment_before_15th_generates_correct_due_date()
    {
        // Arrange: Enroll on January 10, 2026
        $enrollment = $this->createEnrollment('2026-01-10', 10000, 25, 3);

        // Act
        $this->service->generateSchedules($enrollment);

        // Assert
        $schedules = $enrollment->paymentSchedules()->orderBy('installment_no')->get();
        
        // Downpayment due on enrollment date
        $this->assertEquals('2026-01-10', $schedules[0]->due_date->format('Y-m-d'));
        $this->assertEquals(0, $schedules[0]->installment_no);
        
        // First installment due on next month's 15th (Feb 15)
        $this->assertEquals('2026-02-15', $schedules[1]->due_date->format('Y-m-d'));
        $this->assertEquals(1, $schedules[1]->installment_no);
        
        // Second installment (Mar 15)
        $this->assertEquals('2026-03-15', $schedules[2]->due_date->format('Y-m-d'));
        
        // Third installment (Apr 15)
        $this->assertEquals('2026-04-15', $schedules[3]->due_date->format('Y-m-d'));
    }

    /**
     * Test Case 2: Enrollment on 15th generates correct due date
     */
    public function test_enrollment_on_15th_generates_correct_due_date()
    {
        // Arrange: Enroll on January 15, 2026
        $enrollment = $this->createEnrollment('2026-01-15', 10000, 25, 3);

        // Act
        $this->service->generateSchedules($enrollment);

        // Assert
        $schedules = $enrollment->paymentSchedules()->orderBy('installment_no')->get();
        
        // First installment should be Feb 15 (next 15th AFTER Jan 15)
        $this->assertEquals('2026-02-15', $schedules[1]->due_date->format('Y-m-d'));
    }

    /**
     * Test Case 3: Enrollment after 15th generates correct due date
     */
    public function test_enrollment_after_15th_generates_correct_due_date()
    {
        // Arrange: Enroll on January 20, 2026
        $enrollment = $this->createEnrollment('2026-01-20', 10000, 25, 3);

        // Act
        $this->service->generateSchedules($enrollment);

        // Assert
        $schedules = $enrollment->paymentSchedules()->orderBy('installment_no')->get();
        
        // First installment should be Feb 15
        $this->assertEquals('2026-02-15', $schedules[1]->due_date->format('Y-m-d'));
    }

    /**
     * Test Case 4: End of month enrollment
     */
    public function test_end_of_month_enrollment()
    {
        // Arrange: Enroll on January 31, 2026
        $enrollment = $this->createEnrollment('2026-01-31', 10000, 25, 3);

        // Act
        $this->service->generateSchedules($enrollment);

        // Assert
        $schedules = $enrollment->paymentSchedules()->orderBy('installment_no')->get();
        
        // First installment should be Feb 15
        $this->assertEquals('2026-02-15', $schedules[1]->due_date->format('Y-m-d'));
        $this->assertEquals('2026-03-15', $schedules[2]->due_date->format('Y-m-d'));
        $this->assertEquals('2026-04-15', $schedules[3]->due_date->format('Y-m-d'));
    }

    /**
     * Test Case 5: Leap year February enrollment
     */
    public function test_leap_year_february_enrollment()
    {
        // Arrange: Enroll on February 10, 2024 (leap year)
        $enrollment = $this->createEnrollment('2024-02-10', 10000, 25, 3);

        // Act
        $this->service->generateSchedules($enrollment);

        // Assert
        $schedules = $enrollment->paymentSchedules()->orderBy('installment_no')->get();
        
        // First installment should be Mar 15, 2024
        $this->assertEquals('2024-03-15', $schedules[1]->due_date->format('Y-m-d'));
        $this->assertEquals('2024-04-15', $schedules[2]->due_date->format('Y-m-d'));
        $this->assertEquals('2024-05-15', $schedules[3]->due_date->format('Y-m-d'));
    }

    /**
     * Test Case 6: Non-leap year February enrollment
     */
    public function test_non_leap_year_february_enrollment()
    {
        // Arrange: Enroll on February 10, 2025 (non-leap year)
        $enrollment = $this->createEnrollment('2025-02-10', 10000, 25, 3);

        // Act
        $this->service->generateSchedules($enrollment);

        // Assert
        $schedules = $enrollment->paymentSchedules()->orderBy('installment_no')->get();
        
        // First installment should be Mar 15, 2025
        $this->assertEquals('2025-03-15', $schedules[1]->due_date->format('Y-m-d'));
    }

    /**
     * Test Case 7: Different downpayment percentages
     */
    public function test_different_downpayment_percentages()
    {
        $testCases = [
            ['dp' => 0, 'total' => 10000, 'months' => 3, 'expected_dp' => 0, 'expected_remaining' => 10000],
            ['dp' => 25, 'total' => 10000, 'months' => 3, 'expected_dp' => 2500, 'expected_remaining' => 7500],
            ['dp' => 50, 'total' => 10000, 'months' => 3, 'expected_dp' => 5000, 'expected_remaining' => 5000],
            ['dp' => 75, 'total' => 10000, 'months' => 3, 'expected_dp' => 7500, 'expected_remaining' => 2500],
            ['dp' => 100, 'total' => 10000, 'months' => 0, 'expected_dp' => 10000, 'expected_remaining' => 0],
        ];

        foreach ($testCases as $case) {
            $enrollment = $this->createEnrollment('2026-01-10', $case['total'], $case['dp'], $case['months']);
            $this->service->generateSchedules($enrollment);
            
            $schedules = $enrollment->paymentSchedules()->orderBy('installment_no')->get();
            
            // Check downpayment amount
            $this->assertEquals($case['expected_dp'], $schedules[0]->amount_due);
            
            // For 100% downpayment with 0 months, should only have 1 schedule (downpayment)
            if ($case['dp'] == 100 && $case['months'] == 0) {
                $this->assertCount(1, $schedules);
            }
            
            // Clean up
            $enrollment->delete();
        }
    }

    /**
     * Test Case 8: Different installment counts
     */
    public function test_different_installment_counts()
    {
        $testCases = [0, 1, 3, 6, 12, 24];

        foreach ($testCases as $months) {
            $enrollment = $this->createEnrollment('2026-01-10', 10000, 25, $months);
            $this->service->generateSchedules($enrollment);
            
            $schedules = $enrollment->paymentSchedules()->get();
            
            // Should have downpayment + installments
            $expectedCount = 1 + $months; // 1 downpayment + N installments
            $this->assertCount($expectedCount, $schedules);
            
            // Clean up
            $enrollment->delete();
        }
    }

    /**
     * Test Case 9: Rounding edge cases (33.33% downpayment)
     */
    public function test_rounding_edge_cases()
    {
        // Arrange: 33.33% downpayment creates rounding issues
        $enrollment = $this->createEnrollment('2026-01-10', 10000, 33.33, 3);

        // Act
        $this->service->generateSchedules($enrollment);

        // Assert
        $schedules = $enrollment->paymentSchedules()->orderBy('installment_no')->get();
        
        // Downpayment: 10000 * 0.3333 = 3333.00
        $this->assertEquals(3333.00, $schedules[0]->amount_due);
        
        // Remaining: 10000 - 3333 = 6667.00
        // Base installment: floor(6667/3 * 100) / 100 = 2222.33
        // Total base: 2222.33 * 3 = 6666.99
        // Adjustment: 6667.00 - 6666.99 = 0.01
        
        $this->assertEquals(2222.33, $schedules[1]->amount_due);
        $this->assertEquals(2222.33, $schedules[2]->amount_due);
        $this->assertEquals(2222.34, $schedules[3]->amount_due); // Last adjusted
        
        // Verify total equals original
        $total = $schedules->sum('amount_due');
        $this->assertEquals(10000.00, $total);
    }

    /**
     * Test Case 10: Timezone consistency
     */
    public function test_timezone_consistency()
    {
        // Test with different times of day
        $times = ['00:00:00', '12:00:00', '23:59:59'];
        
        foreach ($times as $time) {
            $enrollment = $this->createEnrollment("2026-01-10 {$time}", 10000, 25, 3);
            $this->service->generateSchedules($enrollment);
            
            $schedules = $enrollment->paymentSchedules()->orderBy('installment_no')->get();
            
            // Due dates should be consistent regardless of time
            $this->assertEquals('2026-02-15', $schedules[1]->due_date->format('Y-m-d'));
            
            // Clean up
            $enrollment->delete();
        }
    }

    /**
     * Test: Verify all amounts sum to total fee
     */
    public function test_all_amounts_sum_to_total_fee()
    {
        $enrollment = $this->createEnrollment('2026-01-10', 9999.99, 33.33, 7);
        $this->service->generateSchedules($enrollment);
        
        $schedules = $enrollment->paymentSchedules()->get();
        $total = $schedules->sum('amount_due');
        
        $this->assertEquals(9999.99, $total);
    }

    /**
     * Test: Verify schedule count is correct
     */
    public function test_schedule_count_is_correct()
    {
        $enrollment = $this->createEnrollment('2026-01-10', 10000, 25, 5);
        $this->service->generateSchedules($enrollment);
        
        $schedules = $enrollment->paymentSchedules()->get();
        
        // Should have 1 downpayment + 5 installments = 6 total
        $this->assertCount(6, $schedules);
    }

    /**
     * Test: Verify installment numbers are sequential
     */
    public function test_installment_numbers_are_sequential()
    {
        $enrollment = $this->createEnrollment('2026-01-10', 10000, 25, 3);
        $this->service->generateSchedules($enrollment);
        
        $schedules = $enrollment->paymentSchedules()->orderBy('installment_no')->get();
        
        $this->assertEquals(0, $schedules[0]->installment_no); // Downpayment
        $this->assertEquals(1, $schedules[1]->installment_no);
        $this->assertEquals(2, $schedules[2]->installment_no);
        $this->assertEquals(3, $schedules[3]->installment_no);
    }

    /**
     * Helper: Create enrollment for testing
     */
    private function createEnrollment(
        string $enrollmentDate,
        float $totalFee,
        float $downpaymentPercent,
        int $installmentMonths
    ): Enrollment {
        // Create student
        $student = Student::create([
            'first_name' => 'Test',
            'last_name' => 'Student',
            'guardian_name' => 'Test Guardian',
            'guardian_contact' => '+639123456789',
            'status' => 'ACTIVE',
        ]);

        // Create package
        $package = Package::create([
            'name' => 'Test Package ' . uniqid(),
            'total_fee' => $totalFee,
            'downpayment_percent' => $downpaymentPercent,
            'installment_months' => $installmentMonths,
        ]);

        // Calculate amounts
        $downpaymentAmount = round(($totalFee * $downpaymentPercent) / 100, 2);
        $remainingBalance = round($totalFee - $downpaymentAmount, 2);

        // Create enrollment
        return Enrollment::create([
            'student_id' => $student->id,
            'package_id' => $package->id,
            'enrollment_date' => $enrollmentDate,
            'total_fee' => $totalFee,
            'downpayment_percent' => $downpaymentPercent,
            'downpayment_amount' => $downpaymentAmount,
            'remaining_balance' => $remainingBalance,
            'status' => 'ACTIVE',
        ]);
    }

    /**
     * Test Case: Jan 1 enrollment (start of year)
     */
    public function test_jan_1_enrollment()
    {
        $enrollment = $this->createEnrollment('2026-01-01', 10000, 25, 3);
        $this->service->generateSchedules($enrollment);
        
        $schedules = $enrollment->paymentSchedules()->orderBy('installment_no')->get();
        
        $this->assertEquals('2026-01-01', $schedules[0]->due_date->format('Y-m-d'));
        $this->assertEquals('2026-02-15', $schedules[1]->due_date->format('Y-m-d'));
        $this->assertEquals('2026-03-15', $schedules[2]->due_date->format('Y-m-d'));
        $this->assertEquals('2026-04-15', $schedules[3]->due_date->format('Y-m-d'));
    }

    /**
     * Test Case: Jan 16 enrollment (day after 15th)
     */
    public function test_jan_16_enrollment()
    {
        $enrollment = $this->createEnrollment('2026-01-16', 10000, 25, 3);
        $this->service->generateSchedules($enrollment);
        
        $schedules = $enrollment->paymentSchedules()->orderBy('installment_no')->get();
        
        // First installment should be Feb 15 (next month)
        $this->assertEquals('2026-02-15', $schedules[1]->due_date->format('Y-m-d'));
    }

    /**
     * Test Case: 10% downpayment with 3 installments
     */
    public function test_10_percent_downpayment_3_installments()
    {
        $enrollment = $this->createEnrollment('2026-01-10', 10000, 10, 3);
        $this->service->generateSchedules($enrollment);
        
        $schedules = $enrollment->paymentSchedules()->orderBy('installment_no')->get();
        
        // Downpayment: 10% of 10000 = 1000
        $this->assertEquals(1000.00, $schedules[0]->amount_due);
        
        // Remaining: 9000 / 3 = 3000 each
        $this->assertEquals(3000.00, $schedules[1]->amount_due);
        $this->assertEquals(3000.00, $schedules[2]->amount_due);
        $this->assertEquals(3000.00, $schedules[3]->amount_due);
        
        // Verify total
        $this->assertEquals(10000.00, $schedules->sum('amount_due'));
    }

    /**
     * Test Case: 50% downpayment with 4 installments
     */
    public function test_50_percent_downpayment_4_installments()
    {
        $enrollment = $this->createEnrollment('2026-01-10', 10000, 50, 4);
        $this->service->generateSchedules($enrollment);
        
        $schedules = $enrollment->paymentSchedules()->orderBy('installment_no')->get();
        
        // Downpayment: 50% of 10000 = 5000
        $this->assertEquals(5000.00, $schedules[0]->amount_due);
        
        // Remaining: 5000 / 4 = 1250 each
        $this->assertEquals(1250.00, $schedules[1]->amount_due);
        $this->assertEquals(1250.00, $schedules[2]->amount_due);
        $this->assertEquals(1250.00, $schedules[3]->amount_due);
        $this->assertEquals(1250.00, $schedules[4]->amount_due);
        
        // Verify total
        $this->assertEquals(10000.00, $schedules->sum('amount_due'));
    }

    /**
     * Test Case: Leap year Feb enrollment with 3 installments
     */
    public function test_leap_year_feb_with_3_installments()
    {
        $enrollment = $this->createEnrollment('2024-02-29', 12000, 25, 3);
        $this->service->generateSchedules($enrollment);
        
        $schedules = $enrollment->paymentSchedules()->orderBy('installment_no')->get();
        
        // Downpayment on Feb 29
        $this->assertEquals('2024-02-29', $schedules[0]->due_date->format('Y-m-d'));
        
        // First installment: Mar 15, 2024
        $this->assertEquals('2024-03-15', $schedules[1]->due_date->format('Y-m-d'));
        $this->assertEquals('2024-04-15', $schedules[2]->due_date->format('Y-m-d'));
        $this->assertEquals('2024-05-15', $schedules[3]->due_date->format('Y-m-d'));
    }

    /**
     * Test Case: Rounding with 10% DP and 3 installments on 9999.99
     */
    public function test_rounding_case_9999_10_percent_3_months()
    {
        $enrollment = $this->createEnrollment('2026-01-10', 9999.99, 10, 3);
        $this->service->generateSchedules($enrollment);
        
        $schedules = $enrollment->paymentSchedules()->orderBy('installment_no')->get();
        
        // Downpayment: 9999.99 * 0.10 = 999.999 -> 1000.00
        $this->assertEquals(1000.00, $schedules[0]->amount_due);
        
        // Remaining: 9999.99 - 1000.00 = 8999.99
        // Base: floor(8999.99 / 3 * 100) / 100 = 2999.99
        // Total base: 2999.99 * 3 = 8999.97
        // Adjustment: 8999.99 - 8999.97 = 0.02
        
        $this->assertEquals(2999.99, $schedules[1]->amount_due);
        $this->assertEquals(2999.99, $schedules[2]->amount_due);
        $this->assertEquals(3000.01, $schedules[3]->amount_due); // Last adjusted
        
        // Verify total
        $this->assertEquals(9999.99, $schedules->sum('amount_due'));
    }

    /**
     * Test Case: Rounding with 25% DP and 4 installments on 7777.77
     */
    public function test_rounding_case_7777_25_percent_4_months()
    {
        $enrollment = $this->createEnrollment('2026-01-10', 7777.77, 25, 4);
        $this->service->generateSchedules($enrollment);
        
        $schedules = $enrollment->paymentSchedules()->orderBy('installment_no')->get();
        
        // Downpayment: 7777.77 * 0.25 = 1944.4425 -> 1944.44
        $this->assertEquals(1944.44, $schedules[0]->amount_due);
        
        // Remaining: 7777.77 - 1944.44 = 5833.33
        // Base: floor(5833.33 / 4 * 100) / 100 = 1458.33
        // Total base: 1458.33 * 4 = 5833.32
        // Adjustment: 5833.33 - 5833.32 = 0.01
        
        $this->assertEquals(1458.33, $schedules[1]->amount_due);
        $this->assertEquals(1458.33, $schedules[2]->amount_due);
        $this->assertEquals(1458.33, $schedules[3]->amount_due);
        $this->assertEquals(1458.34, $schedules[4]->amount_due); // Last adjusted
        
        // Verify total
        $this->assertEquals(7777.77, $schedules->sum('amount_due'));
    }

    /**
     * Test Case: 50% DP with 3 installments - odd amount
     */
    public function test_50_percent_3_installments_odd_amount()
    {
        $enrollment = $this->createEnrollment('2026-01-15', 11111.11, 50, 3);
        $this->service->generateSchedules($enrollment);
        
        $schedules = $enrollment->paymentSchedules()->orderBy('installment_no')->get();
        
        // Downpayment: 11111.11 * 0.50 = 5555.555 -> 5555.56
        $this->assertEquals(5555.56, $schedules[0]->amount_due);
        
        // Remaining: 11111.11 - 5555.56 = 5555.55
        // Base: floor(5555.55 / 3 * 100) / 100 = 1851.85
        // Total base: 1851.85 * 3 = 5555.55
        // Adjustment: 5555.55 - 5555.55 = 0.00
        
        $this->assertEquals(1851.85, $schedules[1]->amount_due);
        $this->assertEquals(1851.85, $schedules[2]->amount_due);
        $this->assertEquals(1851.85, $schedules[3]->amount_due);
        
        // Verify total
        $this->assertEquals(11111.11, $schedules->sum('amount_due'));
    }

    /**
     * Test Case: Year-end enrollment (Dec 31)
     */
    public function test_dec_31_enrollment()
    {
        $enrollment = $this->createEnrollment('2025-12-31', 10000, 25, 3);
        $this->service->generateSchedules($enrollment);
        
        $schedules = $enrollment->paymentSchedules()->orderBy('installment_no')->get();
        
        // Downpayment on Dec 31
        $this->assertEquals('2025-12-31', $schedules[0]->due_date->format('Y-m-d'));
        
        // First installment: Jan 15, 2026 (next year)
        $this->assertEquals('2026-01-15', $schedules[1]->due_date->format('Y-m-d'));
        $this->assertEquals('2026-02-15', $schedules[2]->due_date->format('Y-m-d'));
        $this->assertEquals('2026-03-15', $schedules[3]->due_date->format('Y-m-d'));
    }

    /**
     * Test Case: 4 installments spanning multiple months
     */
    public function test_4_installments_date_progression()
    {
        $enrollment = $this->createEnrollment('2026-01-10', 10000, 25, 4);
        $this->service->generateSchedules($enrollment);
        
        $schedules = $enrollment->paymentSchedules()->orderBy('installment_no')->get();
        
        $this->assertCount(5, $schedules); // 1 DP + 4 installments
        
        $this->assertEquals('2026-02-15', $schedules[1]->due_date->format('Y-m-d'));
        $this->assertEquals('2026-03-15', $schedules[2]->due_date->format('Y-m-d'));
        $this->assertEquals('2026-04-15', $schedules[3]->due_date->format('Y-m-d'));
        $this->assertEquals('2026-05-15', $schedules[4]->due_date->format('Y-m-d'));
    }

    /**
     * Test Case: Complex rounding with 33.33% and 7 installments
     */
    public function test_complex_rounding_33_percent_7_installments()
    {
        $enrollment = $this->createEnrollment('2026-01-10', 15000, 33.33, 7);
        $this->service->generateSchedules($enrollment);
        
        $schedules = $enrollment->paymentSchedules()->orderBy('installment_no')->get();
        
        // Downpayment: 15000 * 0.3333 = 4999.50
        $this->assertEquals(4999.50, $schedules[0]->amount_due);
        
        // Remaining: 15000 - 4999.50 = 10000.50
        // Base: floor(10000.50 / 7 * 100) / 100 = 1428.64
        // Total base: 1428.64 * 7 = 10000.48
        // Adjustment: 10000.50 - 10000.48 = 0.02
        
        $this->assertEquals(1428.64, $schedules[1]->amount_due);
        $this->assertEquals(1428.64, $schedules[2]->amount_due);
        $this->assertEquals(1428.64, $schedules[3]->amount_due);
        $this->assertEquals(1428.64, $schedules[4]->amount_due);
        $this->assertEquals(1428.64, $schedules[5]->amount_due);
        $this->assertEquals(1428.64, $schedules[6]->amount_due);
        $this->assertEquals(1428.66, $schedules[7]->amount_due); // Last adjusted
        
        // Verify total (with delta for floating point precision)
        $this->assertEqualsWithDelta(15000.00, $schedules->sum('amount_due'), 0.01);
    }

    /**
     * Test Case: Verify mark as paid updates correctly
     */
    public function test_mark_as_paid_updates_schedule()
    {
        $enrollment = $this->createEnrollment('2026-01-10', 10000, 25, 3);
        $this->service->generateSchedules($enrollment);
        
        $schedule = $enrollment->paymentSchedules()->where('installment_no', 1)->first();
        
        $this->assertEquals('UNPAID', $schedule->status);
        $this->assertNull($schedule->paid_at);
        
        $this->service->markAsPaid($schedule, 'CASH', 'REC-001', 'Test payment');
        
        $schedule->refresh();
        
        $this->assertEquals('PAID', $schedule->status);
        $this->assertNotNull($schedule->paid_at);
        $this->assertEquals('CASH', $schedule->payment_method);
        $this->assertEquals('REC-001', $schedule->receipt_no);
        $this->assertEquals('Test payment', $schedule->remarks);
    }
}
