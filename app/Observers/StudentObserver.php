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
}
