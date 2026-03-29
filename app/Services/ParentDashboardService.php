<?php

namespace App\Services;

use App\Models\Guardian;
use App\Models\Student;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ParentDashboardService
{
    /**
     * Get dashboard data for a specific student with caching.
     */
    public function getStudentDashboard(Student $student): array
    {
        $cacheKey = "parent_dashboard_student_{$student->id}";
        
        return Cache::remember($cacheKey, now()->addMinutes(5), function () use ($student) {
            return [
                'profile' => $this->getStudentProfile($student),
                'attendance_summary' => $this->getAttendanceSummary($student),
                'upcoming_schedule' => $this->getUpcomingSchedule($student),
                'payment_summary' => $this->getPaymentSummary($student),
                'session_balance' => $this->getSessionBalance($student),
                'recent_attendance' => $this->getRecentAttendance($student),
                'recent_payments' => $this->getRecentPayments($student),
            ];
        });
    }

    /**
     * Get student profile information.
     */
    private function getStudentProfile(Student $student): array
    {
        $activeEnrollment = $student->enrollments()
            ->where('status', 'ACTIVE')
            ->with('package')
            ->first();

        return [
            'id' => $student->id,
            'student_no' => $student->student_no,
            'full_name' => $student->full_name,
            'first_name' => $student->first_name,
            'status' => $student->status,
            'age' => $student->age,
            'sex' => $student->sex,
            'enrollment' => $activeEnrollment ? [
                'package_name' => $activeEnrollment->package->name,
                'balance' => $activeEnrollment->remaining_balance_computed,
                'status' => $activeEnrollment->status,
            ] : null,
        ];
    }

    /**
     * Get today's attendance summary.
     */
    private function getAttendanceSummary(Student $student): array
    {
        $today = Carbon::today();
        
        $todayAttendance = $student->attendanceRecords()
            ->whereDate('attendance_date', $today)
            ->first();

        // Get this month's stats
        $monthStats = $student->attendanceRecords()
            ->whereYear('attendance_date', $today->year)
            ->whereMonth('attendance_date', $today->month)
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        return [
            'today_status' => $todayAttendance?->status,
            'today_remarks' => $todayAttendance?->remarks,
            'month_present' => $monthStats['PRESENT'] ?? 0,
            'month_absent' => $monthStats['ABSENT'] ?? 0,
            'month_late' => $monthStats['LATE'] ?? 0,
            'month_excused' => $monthStats['EXCUSED'] ?? 0,
        ];
    }

    /**
     * Get upcoming schedule.
     */
    private function getUpcomingSchedule(Student $student): ?array
    {
        $nextSession = $student->sessionOccurrences()
            ->where('session_date', '>=', Carbon::today())
            ->where('status', 'SCHEDULED')
            ->with('sessionType')
            ->orderBy('session_date')
            ->orderBy('start_time')
            ->first();

        if (!$nextSession) {
            return null;
        }

        return [
            'date' => $nextSession->session_date,
            'start_time' => $nextSession->start_time,
            'end_time' => $nextSession->end_time,
            'session_type' => $nextSession->sessionType->name ?? 'Session',
            'status' => $nextSession->status,
        ];
    }

    /**
     * Get payment summary.
     */
    private function getPaymentSummary(Student $student): array
    {
        $activeEnrollment = $student->enrollments()
            ->where('status', 'ACTIVE')
            ->first();

        if (!$activeEnrollment) {
            return [
                'outstanding_balance' => 0,
                'last_payment' => null,
                'next_due_date' => null,
                'next_due_amount' => 0,
            ];
        }

        $lastPayment = $activeEnrollment->paymentTransactions()
            ->where('type', 'PAYMENT')
            ->orderBy('transaction_date', 'desc')
            ->first();

        $nextSchedule = $activeEnrollment->paymentSchedules()
            ->where('status', 'UNPAID')
            ->orderBy('due_date')
            ->first();

        return [
            'outstanding_balance' => $activeEnrollment->remaining_balance_computed,
            'last_payment' => $lastPayment ? [
                'amount' => $lastPayment->amount,
                'date' => $lastPayment->transaction_date,
                'method' => $lastPayment->payment_method,
            ] : null,
            'next_due_date' => $nextSchedule?->due_date,
            'next_due_amount' => $nextSchedule?->amount_due ?? 0,
        ];
    }

    /**
     * Get session balance.
     */
    private function getSessionBalance(Student $student): array
    {
        $activeEnrollment = $student->enrollments()
            ->where('status', 'ACTIVE')
            ->with('package')
            ->first();

        if (!$activeEnrollment || !$activeEnrollment->package) {
            return [
                'total_sessions' => 0,
                'sessions_used' => 0,
                'sessions_remaining' => 0,
                'validity_end' => null,
            ];
        }

        $totalSessions = $activeEnrollment->package->total_sessions ?? 0;
        
        // Count completed sessions
        $sessionsUsed = $student->sessionOccurrences()
            ->where('status', 'COMPLETED')
            ->count();

        return [
            'total_sessions' => $totalSessions,
            'sessions_used' => $sessionsUsed,
            'sessions_remaining' => max(0, $totalSessions - $sessionsUsed),
            'validity_end' => $activeEnrollment->package_end_date ?? null,
        ];
    }

    /**
     * Get recent attendance records (last 5).
     */
    private function getRecentAttendance(Student $student): array
    {
        return $student->attendanceRecords()
            ->orderBy('attendance_date', 'desc')
            ->limit(5)
            ->get()
            ->map(fn($record) => [
                'date' => $record->attendance_date,
                'status' => $record->status,
                'remarks' => $record->remarks,
            ])
            ->toArray();
    }

    /**
     * Get recent payments (last 5).
     */
    private function getRecentPayments(Student $student): array
    {
        return DB::table('payment_transactions')
            ->join('enrollments', 'payment_transactions.enrollment_id', '=', 'enrollments.id')
            ->where('enrollments.student_id', $student->id)
            ->where('payment_transactions.type', 'PAYMENT')
            ->select(
                'payment_transactions.transaction_date',
                'payment_transactions.amount',
                'payment_transactions.payment_method',
                'payment_transactions.reference_no'
            )
            ->orderBy('payment_transactions.transaction_date', 'desc')
            ->limit(5)
            ->get()
            ->map(fn($payment) => [
                'date' => Carbon::parse($payment->transaction_date),
                'amount' => $payment->amount,
                'method' => $payment->payment_method,
                'reference' => $payment->reference_no,
            ])
            ->toArray();
    }

    /**
     * Clear cache for a specific student.
     */
    public function clearStudentCache(int $studentId): void
    {
        Cache::forget("parent_dashboard_student_{$studentId}");
    }

    /**
     * Clear cache for all students of a guardian.
     */
    public function clearGuardianCache(Guardian $guardian): void
    {
        foreach ($guardian->students as $student) {
            $this->clearStudentCache($student->id);
        }
    }
}
