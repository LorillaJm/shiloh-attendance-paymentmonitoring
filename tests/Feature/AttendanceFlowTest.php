<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Student;
use App\Models\AttendanceRecord;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

class AttendanceFlowTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create([
            'email' => 'user@test.com',
            'name' => 'Test User',
        ]);
        
        Carbon::setTestNow('2026-02-14 12:00:00');
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    /**
     * Test: User encodes attendance for multiple students in batch
     */
    public function test_user_encodes_batch_attendance()
    {
        // Create 5 students
        $students = Student::factory()->count(5)->create();
        $attendanceDate = '2026-02-14';

        // Encode attendance for all students
        foreach ($students as $index => $student) {
            $status = $index < 3 ? 'PRESENT' : ($index === 3 ? 'ABSENT' : 'LATE');
            
            AttendanceRecord::create([
                'student_id' => $student->id,
                'attendance_date' => $attendanceDate,
                'status' => $status,
                'remarks' => $status === 'LATE' ? 'Arrived 30 minutes late' : null,
                'encoded_by_user_id' => $this->user->id,
            ]);
        }

        // Verify all records created
        $this->assertEquals(5, AttendanceRecord::where('attendance_date', $attendanceDate)->count());
        
        // Verify counts by status
        $this->assertEquals(3, AttendanceRecord::where('status', 'PRESENT')->count());
        $this->assertEquals(1, AttendanceRecord::where('status', 'ABSENT')->count());
        $this->assertEquals(1, AttendanceRecord::where('status', 'LATE')->count());

        // Verify all encoded by same user
        $this->assertEquals(5, $this->user->attendanceRecords()->count());
    }

    /**
     * Test: Attendance records for last 30 days
     */
    public function test_attendance_records_for_last_30_days()
    {
        $student = Student::factory()->create();

        // Create attendance for last 30 days
        for ($i = 0; $i < 30; $i++) {
            $date = Carbon::now()->subDays($i);
            
            AttendanceRecord::create([
                'student_id' => $student->id,
                'attendance_date' => $date->format('Y-m-d'),
                'status' => $i % 5 === 0 ? 'ABSENT' : 'PRESENT',
                'encoded_by_user_id' => $this->user->id,
            ]);
        }

        // Verify count
        $this->assertEquals(30, $student->attendanceRecords()->count());

        // Test date range scope
        $startDate = Carbon::now()->subDays(29)->format('Y-m-d');
        $endDate = Carbon::now()->format('Y-m-d');
        
        $records = AttendanceRecord::dateRange($startDate, $endDate)->get();
        $this->assertEquals(30, $records->count());

        // Test status filtering
        $presentCount = AttendanceRecord::status('PRESENT')->count();
        $absentCount = AttendanceRecord::status('ABSENT')->count();
        
        $this->assertEquals(24, $presentCount); // 30 - 6 (every 5th day)
        $this->assertEquals(6, $absentCount);
    }

    /**
     * Test: Attendance can be edited within window
     */
    public function test_attendance_can_be_edited_within_window()
    {
        $student = Student::factory()->create();

        // Create attendance 3 days ago (within 7-day window)
        $record = AttendanceRecord::create([
            'student_id' => $student->id,
            'attendance_date' => Carbon::now()->subDays(3)->format('Y-m-d'),
            'status' => 'PRESENT',
            'encoded_by_user_id' => $this->user->id,
        ]);

        $this->assertTrue($record->canBeEdited());

        // Update the record
        $record->update([
            'status' => 'LATE',
            'remarks' => 'Corrected: Student was late',
        ]);

        $this->assertEquals('LATE', $record->status);
        $this->assertEquals('Corrected: Student was late', $record->remarks);
    }

    /**
     * Test: Old attendance cannot be edited beyond window
     */
    public function test_old_attendance_cannot_be_edited()
    {
        $student = Student::factory()->create();

        // Create attendance 10 days ago (beyond 7-day window)
        $record = AttendanceRecord::create([
            'student_id' => $student->id,
            'attendance_date' => Carbon::now()->subDays(10)->format('Y-m-d'),
            'status' => 'PRESENT',
            'encoded_by_user_id' => $this->user->id,
        ]);

        $this->assertFalse($record->canBeEdited());
    }

    /**
     * Test: Attendance filtering by month
     */
    public function test_attendance_filtering_by_month()
    {
        $student = Student::factory()->create();

        // Create records for January 2026
        for ($day = 1; $day <= 31; $day++) {
            AttendanceRecord::create([
                'student_id' => $student->id,
                'attendance_date' => "2026-01-{$day}",
                'status' => 'PRESENT',
                'encoded_by_user_id' => $this->user->id,
            ]);
        }

        // Create records for February 2026
        for ($day = 1; $day <= 14; $day++) {
            AttendanceRecord::create([
                'student_id' => $student->id,
                'attendance_date' => "2026-02-{$day}",
                'status' => 'PRESENT',
                'encoded_by_user_id' => $this->user->id,
            ]);
        }

        // Test month scope
        $januaryRecords = AttendanceRecord::month(2026, 1)->get();
        $februaryRecords = AttendanceRecord::month(2026, 2)->get();

        $this->assertEquals(31, $januaryRecords->count());
        $this->assertEquals(14, $februaryRecords->count());
    }

    /**
     * Test: Multiple students attendance on same date
     */
    public function test_multiple_students_same_date()
    {
        $students = Student::factory()->count(10)->create();
        $date = '2026-02-14';

        foreach ($students as $student) {
            AttendanceRecord::create([
                'student_id' => $student->id,
                'attendance_date' => $date,
                'status' => fake()->randomElement(['PRESENT', 'ABSENT', 'LATE']),
                'encoded_by_user_id' => $this->user->id,
            ]);
        }

        $records = AttendanceRecord::where('attendance_date', $date)->get();
        $this->assertEquals(10, $records->count());

        // Verify each student has exactly one record for this date
        foreach ($students as $student) {
            $studentRecords = AttendanceRecord::where('student_id', $student->id)
                ->where('attendance_date', $date)
                ->count();
            $this->assertEquals(1, $studentRecords);
        }
    }

    /**
     * Test: Attendance statistics
     */
    public function test_attendance_statistics()
    {
        $student = Student::factory()->create();

        // Create 20 days of attendance
        $statuses = [
            'PRESENT' => 15,
            'ABSENT' => 3,
            'LATE' => 2,
        ];

        $day = 1;
        foreach ($statuses as $status => $count) {
            for ($i = 0; $i < $count; $i++) {
                AttendanceRecord::create([
                    'student_id' => $student->id,
                    'attendance_date' => "2026-02-{$day}",
                    'status' => $status,
                    'encoded_by_user_id' => $this->user->id,
                ]);
                $day++;
            }
        }

        // Calculate statistics
        $totalRecords = $student->attendanceRecords()->count();
        $presentCount = $student->attendanceRecords()->where('status', 'PRESENT')->count();
        $absentCount = $student->attendanceRecords()->where('status', 'ABSENT')->count();
        $lateCount = $student->attendanceRecords()->where('status', 'LATE')->count();

        $this->assertEquals(20, $totalRecords);
        $this->assertEquals(15, $presentCount);
        $this->assertEquals(3, $absentCount);
        $this->assertEquals(2, $lateCount);

        // Calculate attendance rate
        $attendanceRate = ($presentCount + $lateCount) / $totalRecords * 100;
        $this->assertEquals(85.0, $attendanceRate);
    }
}
