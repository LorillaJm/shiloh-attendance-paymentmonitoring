<?php

namespace App\Services;

use App\Models\Student;
use Illuminate\Support\Facades\DB;

class StudentNumberGenerator
{
    /**
     * Generate a unique student number with database locking.
     * Format: SHILOH-YYYY-0001
     *
     * @return string
     */
    public static function generate(): string
    {
        return DB::transaction(function () {
            $year = date('Y');
            $prefix = "SHILOH-{$year}-";

            // Get the last student number for this year with row locking
            // This prevents race conditions during concurrent student creation
            $lastStudent = Student::where('student_no', 'LIKE', "{$prefix}%")
                ->lockForUpdate()
                ->orderBy('student_no', 'desc')
                ->first();

            if ($lastStudent) {
                // Extract the sequence number and increment
                $lastNumber = (int) substr($lastStudent->student_no, -4);
                $newNumber = $lastNumber + 1;
            } else {
                // First student of the year
                $newNumber = 1;
            }

            // Format with leading zeros (4 digits)
            $sequence = str_pad($newNumber, 4, '0', STR_PAD_LEFT);

            return $prefix . $sequence;
        });
    }

    /**
     * Check if a student number already exists.
     *
     * @param string $studentNo
     * @return bool
     */
    public static function exists(string $studentNo): bool
    {
        return Student::where('student_no', $studentNo)->exists();
    }

    /**
     * Generate a unique student number with retry logic.
     *
     * @param int $maxRetries
     * @return string
     * @throws \Exception
     */
    public static function generateUnique(int $maxRetries = 10): string
    {
        $attempts = 0;

        while ($attempts < $maxRetries) {
            $studentNo = self::generate();

            if (!self::exists($studentNo)) {
                return $studentNo;
            }

            $attempts++;
        }

        throw new \Exception('Unable to generate unique student number after ' . $maxRetries . ' attempts');
    }
}
