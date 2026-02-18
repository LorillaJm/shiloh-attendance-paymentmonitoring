<?php

namespace App\Services;

use App\Models\StudentSchedule;
use App\Models\SessionOccurrence;
use Carbon\Carbon;

class SessionOccurrenceGenerator
{
    /**
     * Generate session occurrences from a schedule for a date range.
     */
    public static function generateFromSchedule(StudentSchedule $schedule, Carbon $startDate, Carbon $endDate): int
    {
        $count = 0;
        $current = $startDate->copy();

        // Respect schedule's effective dates
        if ($schedule->effective_from && $current->lt($schedule->effective_from)) {
            $current = Carbon::parse($schedule->effective_from);
        }

        $scheduleEnd = $schedule->effective_until ? Carbon::parse($schedule->effective_until) : $endDate;
        $actualEnd = $endDate->lt($scheduleEnd) ? $endDate : $scheduleEnd;

        while ($current->lte($actualEnd)) {
            if (self::shouldGenerateForDate($schedule, $current)) {
                // Check if occurrence already exists
                $exists = SessionOccurrence::where('student_schedule_id', $schedule->id)
                    ->where('session_date', $current->format('Y-m-d'))
                    ->exists();

                if (!$exists) {
                    SessionOccurrence::create([
                        'student_schedule_id' => $schedule->id,
                        'student_id' => $schedule->student_id,
                        'session_type_id' => $schedule->session_type_id,
                        'teacher_id' => $schedule->teacher_id,
                        'session_date' => $current->format('Y-m-d'),
                        'start_time' => $schedule->start_time,
                        'end_time' => $schedule->end_time,
                        'status' => 'SCHEDULED',
                    ]);
                    $count++;
                }
            }
            $current->addDay();
        }

        return $count;
    }

    /**
     * Check if session should be generated for a specific date.
     */
    private static function shouldGenerateForDate(StudentSchedule $schedule, Carbon $date): bool
    {
        if ($schedule->recurrence_type === 'DAILY') {
            return true;
        }

        if ($schedule->recurrence_type === 'WEEKLY' && $schedule->recurrence_days) {
            // recurrence_days contains day of week numbers (1=Monday, 7=Sunday)
            return in_array($date->dayOfWeekIso, $schedule->recurrence_days);
        }

        return false;
    }

    /**
     * Generate occurrences for all active schedules.
     */
    public static function generateForAllSchedules(Carbon $startDate, Carbon $endDate): int
    {
        $totalCount = 0;
        $schedules = StudentSchedule::active()->get();

        foreach ($schedules as $schedule) {
            $totalCount += self::generateFromSchedule($schedule, $startDate, $endDate);
        }

        return $totalCount;
    }
}
