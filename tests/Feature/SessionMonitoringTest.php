<?php

namespace Tests\Feature;

use App\Models\Student;
use App\Models\SessionType;
use App\Models\SessionOccurrence;
use App\Models\StudentSchedule;
use App\Services\SessionOccurrenceGenerator;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SessionMonitoringTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_age_10_and_below_requires_monitoring(): void
    {
        $youngStudent = Student::factory()->create([
            'birthdate' => now()->subYears(8),
        ]);

        $olderStudent = Student::factory()->create([
            'birthdate' => now()->subYears(12),
        ]);

        $youngStudent->refresh();
        $olderStudent->refresh();

        $this->assertTrue($youngStudent->requires_monitoring);
        $this->assertFalse($olderStudent->requires_monitoring);
    }

    public function test_session_occurrences_generated_from_schedule(): void
    {
        $student = Student::factory()->create();
        $sessionType = SessionType::factory()->create();
        
        $schedule = StudentSchedule::create([
            'student_id' => $student->id,
            'session_type_id' => $sessionType->id,
            'recurrence_type' => 'WEEKLY',
            'recurrence_days' => [1, 3, 5], // Mon, Wed, Fri
            'start_time' => '09:00',
            'end_time' => '10:00',
            'effective_from' => now(),
            'is_active' => true,
        ]);

        $startDate = Carbon::now()->startOfWeek();
        $endDate = Carbon::now()->endOfWeek();

        $count = SessionOccurrenceGenerator::generateFromSchedule($schedule, $startDate, $endDate);

        $this->assertGreaterThan(0, $count);
        $this->assertEquals($count, SessionOccurrence::where('student_schedule_id', $schedule->id)->count());
    }

    public function test_monitoring_notes_can_be_added_to_sessions(): void
    {
        $student = Student::factory()->create(['birthdate' => now()->subYears(7)]);
        $student->refresh();
        
        $sessionType = SessionType::factory()->create();
        
        $occurrence = SessionOccurrence::create([
            'student_id' => $student->id,
            'session_type_id' => $sessionType->id,
            'session_date' => now(),
            'start_time' => '09:00',
            'end_time' => '10:00',
            'status' => 'COMPLETED',
            'monitoring_notes' => 'Student showed improvement in focus and engagement.',
        ]);

        $this->assertNotNull($occurrence->monitoring_notes);
        $this->assertTrue($student->requires_monitoring);
    }
}
