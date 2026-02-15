<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Student;
use App\Models\Package;
use App\Models\Enrollment;
use App\Models\PaymentSchedule;
use App\Services\PaymentScheduleService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

class EnrollmentFlowTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private PaymentScheduleService $paymentService;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->admin = User::factory()->admin()->create([
            'email' => 'admin@test.com',
            'name' => 'Test Admin',
        ]);
        
        $this->paymentService = new PaymentScheduleService();
        
        // Set fixed test time
        Carbon::setTestNow('2026-02-14 12:00:00');
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    /**
     * Test: Admin creates package and student, then enrolls student
     */
    public function test_admin_creates_package_and_student_then_enrolls()
    {
        // Step 1: Create package
        $package = Package::create([
            'name' => 'Basic Package',
            'total_fee' => 10000.00,
            'downpayment_percent' => 25.00,
            'installment_months' => 3,
            'description' => 'Basic training package',
        ]);

        $this->assertDatabaseHas('packages', [
            'name' => 'Basic Package',
            'total_fee' => 10000.00,
        ]);

        // Step 2: Create student
        $student = Student::create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'middle_name' => 'Smith',
            'birthdate' => '2010-05-15',
            'sex' => 'Male',
            'address' => '123 Main St',
            'guardian_name' => 'Jane Doe',
            'guardian_contact' => '+639123456789',
            'status' => 'ACTIVE',
        ]);

        $this->assertDatabaseHas('students', [
            'first_name' => 'John',
            'last_name' => 'Doe',
        ]);
        $this->assertNotNull($student->student_no);

        // Step 3: Enroll student
        $enrollment = Enrollment::create([
            'student_id' => $student->id,
            'package_id' => $package->id,
            'enrollment_date' => '2026-01-10',
            'total_fee' => $package->total_fee,
            'downpayment_percent' => $package->downpayment_percent,
            'downpayment_amount' => 2500.00,
            'remaining_balance' => 7500.00,
            'status' => 'ACTIVE',
        ]);

        $this->assertDatabaseHas('enrollments', [
            'student_id' => $student->id,
            'package_id' => $package->id,
            'status' => 'ACTIVE',
        ]);

        // Step 4: Generate payment schedules
        $this->paymentService->generateSchedules($enrollment);

        // Verify schedules created
        $schedules = $enrollment->paymentSchedules()->orderBy('installment_no')->get();
        $this->assertCount(4, $schedules); // 1 DP + 3 installments

        // Verify downpayment
        $this->assertEquals(0, $schedules[0]->installment_no);
        $this->assertEquals(2500.00, $schedules[0]->amount_due);
        $this->assertEquals('2026-01-10', $schedules[0]->due_date->format('Y-m-d'));

        // Verify installments
        $this->assertEquals('2026-02-15', $schedules[1]->due_date->format('Y-m-d'));
        $this->assertEquals('2026-03-15', $schedules[2]->due_date->format('Y-m-d'));
        $this->assertEquals('2026-04-15', $schedules[3]->due_date->format('Y-m-d'));
    }

    /**
     * Test: Enrollment on different dates generates correct schedules
     */
    public function test_enrollment_dates_generate_correct_schedules()
    {
        $package = Package::factory()->create([
            'total_fee' => 10000,
            'downpayment_percent' => 25,
            'installment_months' => 3,
        ]);

        $testCases = [
            ['date' => '2026-01-01', 'first_due' => '2026-02-15'],
            ['date' => '2026-01-10', 'first_due' => '2026-02-15'],
            ['date' => '2026-01-15', 'first_due' => '2026-02-15'],
            ['date' => '2026-01-16', 'first_due' => '2026-02-15'],
            ['date' => '2026-01-31', 'first_due' => '2026-02-15'],
        ];

        foreach ($testCases as $case) {
            $student = Student::factory()->create();
            
            $enrollment = Enrollment::create([
                'student_id' => $student->id,
                'package_id' => $package->id,
                'enrollment_date' => $case['date'],
                'total_fee' => 10000,
                'downpayment_percent' => 25,
                'downpayment_amount' => 2500,
                'remaining_balance' => 7500,
                'status' => 'ACTIVE',
            ]);

            $this->paymentService->generateSchedules($enrollment);

            $firstInstallment = $enrollment->paymentSchedules()
                ->where('installment_no', 1)
                ->first();

            $this->assertEquals(
                $case['first_due'],
                $firstInstallment->due_date->format('Y-m-d'),
                "Failed for enrollment date: {$case['date']}"
            );
        }
    }

    /**
     * Test: Mark installment as paid and remaining balance updates
     */
    public function test_mark_installment_paid_updates_balance()
    {
        // Create enrollment with schedules
        $package = Package::factory()->create([
            'total_fee' => 10000,
            'downpayment_percent' => 25,
            'installment_months' => 3,
        ]);

        $student = Student::factory()->create();
        
        $enrollment = Enrollment::create([
            'student_id' => $student->id,
            'package_id' => $package->id,
            'enrollment_date' => '2026-01-10',
            'total_fee' => 10000,
            'downpayment_percent' => 25,
            'downpayment_amount' => 2500,
            'remaining_balance' => 7500,
            'status' => 'ACTIVE',
        ]);

        $this->paymentService->generateSchedules($enrollment);

        // Initially, no payments made
        $this->assertEquals(0, $enrollment->total_paid);
        $this->assertEquals(10000, $enrollment->remaining_balance_computed);

        // Mark downpayment as paid
        $downpayment = $enrollment->paymentSchedules()->where('installment_no', 0)->first();
        $this->paymentService->markAsPaid($downpayment, 'CASH', 'REC-001');

        $enrollment->refresh();
        $this->assertEquals(2500, $enrollment->total_paid);
        $this->assertEquals(7500, $enrollment->remaining_balance_computed);

        // Mark first installment as paid
        $firstInstallment = $enrollment->paymentSchedules()->where('installment_no', 1)->first();
        $this->paymentService->markAsPaid($firstInstallment, 'BANK_TRANSFER', 'REC-002');

        $enrollment->refresh();
        $this->assertEquals(5000, $enrollment->total_paid); // 2500 + 2500
        $this->assertEquals(5000, $enrollment->remaining_balance_computed);

        // Mark all remaining as paid
        $secondInstallment = $enrollment->paymentSchedules()->where('installment_no', 2)->first();
        $thirdInstallment = $enrollment->paymentSchedules()->where('installment_no', 3)->first();
        
        $this->paymentService->markAsPaid($secondInstallment, 'CASH', 'REC-003');
        $this->paymentService->markAsPaid($thirdInstallment, 'CASH', 'REC-004');

        $enrollment->refresh();
        $this->assertEquals(10000, $enrollment->total_paid);
        $this->assertEquals(0, $enrollment->remaining_balance_computed);
    }

    /**
     * Test: Overdue status computed correctly after due date
     */
    public function test_overdue_status_computed_correctly()
    {
        $package = Package::factory()->create([
            'total_fee' => 10000,
            'downpayment_percent' => 25,
            'installment_months' => 3,
        ]);

        $student = Student::factory()->create();
        
        // Enroll in the past
        $enrollment = Enrollment::create([
            'student_id' => $student->id,
            'package_id' => $package->id,
            'enrollment_date' => '2025-11-10', // 3 months ago
            'total_fee' => 10000,
            'downpayment_percent' => 25,
            'downpayment_amount' => 2500,
            'remaining_balance' => 7500,
            'status' => 'ACTIVE',
        ]);

        $this->paymentService->generateSchedules($enrollment);

        // Current date is 2026-02-14
        // Schedules should be:
        // - Downpayment: 2025-11-10 (overdue)
        // - Installment 1: 2025-12-15 (overdue)
        // - Installment 2: 2026-01-15 (overdue)
        // - Installment 3: 2026-02-15 (not overdue yet - it's today)

        $schedules = $enrollment->paymentSchedules()->orderBy('installment_no')->get();

        // Check computed overdue status
        $this->assertTrue($schedules[0]->is_overdue); // Downpayment
        $this->assertTrue($schedules[1]->is_overdue); // Dec 15
        $this->assertTrue($schedules[2]->is_overdue); // Jan 15
        $this->assertFalse($schedules[3]->is_overdue); // Feb 15 (today)

        // Test scope
        $overdueCount = $enrollment->paymentSchedules()->overdue()->count();
        $this->assertEquals(3, $overdueCount);
    }

    /**
     * Test: Multiple enrollments for same student
     */
    public function test_student_can_have_multiple_enrollments()
    {
        $student = Student::factory()->create();
        $package1 = Package::factory()->create(['name' => 'Package 1']);
        $package2 = Package::factory()->create(['name' => 'Package 2']);

        $enrollment1 = Enrollment::create([
            'student_id' => $student->id,
            'package_id' => $package1->id,
            'enrollment_date' => '2025-06-01',
            'total_fee' => $package1->total_fee,
            'downpayment_percent' => $package1->downpayment_percent,
            'downpayment_amount' => $package1->downpayment_amount,
            'remaining_balance' => $package1->total_fee - $package1->downpayment_amount,
            'status' => 'ACTIVE',
        ]);

        $enrollment2 = Enrollment::create([
            'student_id' => $student->id,
            'package_id' => $package2->id,
            'enrollment_date' => '2026-01-01',
            'total_fee' => $package2->total_fee,
            'downpayment_percent' => $package2->downpayment_percent,
            'downpayment_amount' => $package2->downpayment_amount,
            'remaining_balance' => $package2->total_fee - $package2->downpayment_amount,
            'status' => 'ACTIVE',
        ]);

        $this->assertEquals(2, $student->enrollments()->count());
        $this->assertTrue($student->enrollments->contains($enrollment1));
        $this->assertTrue($student->enrollments->contains($enrollment2));
    }
}
