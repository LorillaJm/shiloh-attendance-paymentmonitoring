<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Guardian;
use App\Models\Student;
use App\Models\SessionType;
use App\Models\StudentSchedule;
use App\Models\SessionOccurrence;
use App\Models\AttendanceRecord;
use App\Models\Enrollment;
use App\Models\Package;
use App\Enums\UserRole;
use App\Services\SessionOccurrenceGenerator;
use App\Services\PaymentLedgerService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShilohWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_complete_student_workflow(): void
    {
        // 1. Create student with guardian
        $parentUser = User::factory()->create(['role' => UserRole::PARENT]);
        $guardian = Guardian::factory()->create(['user_id' => $parentUser->id]);
        
        $student = Student::factory()->create([
            'age' => 8,
            'requires_monitoring' => true,
        ]);
        
        $guardian->students()->attach($student->id, ['is_primary' => true]);

        // 2. Create enrollment with 3-month package
        $package = Package::factory()->create(['total_fee' => 9000]);
        $enrollment = Enrollment::factory()->create([
            'student_id' => $student->id,
            'package_id' => $package->id,
            'total_fee' => 9000,
            'package_start_date' => now(),
            'package_end_date' => now()->addMonths(3),
            'monthly_installments' => 3,
        ]);

        // 3. Create session schedule
        $teacher = User::factory()->create(['role' => UserRole::TEACHER]);
        $sessionType = SessionType::factory()->create();
        
        $schedule = StudentSchedule::create([
            'student_id' => $student->id,
            'session_type_id' => $sessionType->id,
            'teacher_id' => $teacher->id,
            'recurrence_type' => 'WEEKLY',
            'recurrence_days' => [1, 3, 5],
            'start_time' => '09:00',
            'end_time' => '10:00',
            'effective_from' => now(),
            'is_active' => true,
        ]);

        // 4. Generate session occurrences
        $count = SessionOccurrenceGenerator::generateFromSchedule(
            $schedule,
            Carbon::now(),
            Carbon::now()->addWeek()
        );
        
        $this->assertGreaterThan(0, $count);

        // 5. Record attendance for a session
        $occurrence = SessionOccurrence::first();
        $attendance = AttendanceRecord::create([
            'student_id' => $student->id,
            'session_occurrence_id' => $occurrence->id,
            'attendance_date' => $occurrence->session_date,
            'status' => 'PRESENT',
            'encoded_by_user_id' => $teacher->id,
        ]);

        $this->assertEquals('PRESENT', $attendance->status);

        // 6. Make partial payment
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $transaction = PaymentLedgerService::recordPayment(
            $enrollment,
            3000,
            'Cash',
            'REC-001',
            'First payment',
            $admin->id
        );

        $this->assertEquals(3000, $transaction->amount);

        // 7. Verify parent can see their child
        $this->actingAs($parentUser);
        $children = $guardian->students;
        $this->assertCount(1, $children);
        $this->assertTrue($children->contains($student));

        // 8. Verify teacher can see their assigned schedule
        $this->actingAs($teacher);
        $assignedSchedules = $teacher->assignedSchedules;
        $this->assertCount(1, $assignedSchedules);
        $this->assertTrue($assignedSchedules->contains($schedule));
    }

    public function test_monitoring_required_for_young_students(): void
    {
        $youngStudent = Student::factory()->create(['birthdate' => now()->subYears(7)]);
        $olderStudent = Student::factory()->create(['birthdate' => now()->subYears(12)]);

        $youngStudent->refresh();
        $olderStudent->refresh();

        $this->assertTrue($youngStudent->requires_monitoring);
        $this->assertFalse($olderStudent->requires_monitoring);
    }

    public function test_package_validity_is_three_months(): void
    {
        $package = Package::factory()->create(['total_fee' => 9000]);
        $student = Student::factory()->create();
        
        $enrollment = Enrollment::factory()->create([
            'student_id' => $student->id,
            'package_id' => $package->id,
            'package_start_date' => Carbon::parse('2026-02-01'),
            'package_end_date' => Carbon::parse('2026-05-01'),
        ]);

        $this->assertEquals(3, $enrollment->package_start_date->diffInMonths($enrollment->package_end_date));
    }
}
