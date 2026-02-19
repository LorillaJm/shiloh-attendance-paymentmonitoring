<?php

namespace App\Observers;

use App\Models\Student;

class StudentObserver
{
    public function saving(Student $student): void
    {
        // Auto-calculate age from birthdate
        if ($student->birthdate) {
            $student->age = (int) now()->diffInYears($student->birthdate);
            $student->requires_monitoring = $student->age <= 10;
        }
    }

    public function updated(Student $student): void
    {
        // Clear dashboard caches if status changed
        if ($student->wasChanged('status')) {
            \App\Services\DashboardCacheService::clearStudentCaches();
        }
    }

    public function deleted(Student $student): void
    {
        \App\Services\DashboardCacheService::clearStudentCaches();
    }
}
