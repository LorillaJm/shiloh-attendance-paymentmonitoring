<?php

namespace App\Services;

use App\Models\Guardian;
use App\Models\Student;
use App\Models\AttendanceRecord;
use App\Models\PaymentTransaction;
use App\Models\SessionOccurrence;
use App\Models\Enrollment;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ParentPortalService
{
    /**
     * Get dashboard data for a guardian with caching
     */
    public function getDashboardData(Guardian $guardian): array
    {
        return Cache::remember("parent_dashboard_{$guardian->id}", 60, function () use ($guardian) {
            $studentIds = $guardian->students->pluck('id')->toArray();

            if (empty($studentIds)) {
                return $this->getEmptyDashboardData();
            }

            return [
                'students' => $this->getStudentsWithData($studentIds),
                'summary' => $this->getSummaryStats($studentIds),
                'recent_attendance' => $this->getRecentAttendance($studentIds, 5),
                'recent_payments' => $this->getRecentPayments($studentIds, 5),
                'upcoming_sessions' => $this->getUpcomingSessions($studentIds, 5),
                'alerts' => $this->getAlerts($studentIds),
            ];
        });
    }

    /**
     * Get students with their enrollment and package data
     */
    private function getStudentsWithData(array $studentIds): \Illuminate\Support\Collection
    {
        return Student::whereIn('id', $studentIds)
            ->with([
                'enrollments' => function ($query) {
                    $query->where('status', 'ACTIVE')
                        ->with('package')
                        ->latest();
                }
            ])
            ->get()
            ->map(function ($student) {
                $activeEnrollment = $student->enrollments->first();
                
                $student->active_enrollment = $activeEnrollment;
                $student->package_name = $activeEnrollment?->package?->name ?? 'No Package';
                $student->total_fee = $activeEnrollment?->total_fee ?? 0;
                $student->remaining_balance = $activeEnrollment?->remaining_balance_computed ?? 0;
                
                // Calculate sessions
                if ($activeEnrollment && $activeEnrollment->package) {
                    $totalSessions = $activeEnrollment->package->total_sessions ?? 0;
                    $completedSessions = SessionOccurrence::where('student_id', $student->id)
                        ->where('status', 'COMPLETED')
                        ->count();
                    
                    $student->total_sessions = $totalSessions;
                    $student->completed_sessions = $completedSessions;
                    $student->remaining_sessions = max(0, $totalSessions - $completedSessions);
                } else {
                    $student->total_sessions = 0;
                    $student->completed_sessions = 0;
                    $student->remaining_sessions = 0;
                }
                
                // Today's attendance
                $student->today_attendance = AttendanceRecord::where('student_id', $student->id)
                    ->whereDate('attendance_date', today())
                    ->first();
                
                return $student;
            });
    }

    /**
     * Get summary statistics
     */
    private function getSummaryStats(array $studentIds): array
    {
        // Attendance stats for current month
        $attendanceStats = AttendanceRecord::whereIn('student_id', $studentIds)
            ->whereYear('attendance_date', now()->year)
            ->whereMonth('attendance_date', now()->month)
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // Payment stats
        $paymentStats = DB::table('enrollments')
            ->whereIn('student_id', $studentIds)
            ->where('status', 'ACTIVE')
            ->select(
                DB::raw('SUM(total_fee) as total_fees'),
                DB::raw('SUM(total_fee - remaining_balance) as total_paid'),
                DB::raw('SUM(remaining_balance) as total_balance')
            )
            ->first();

        // Session stats
        $sessionStats = SessionOccurrence::whereIn('student_id', $studentIds)
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        return [
            'attendance' => [
                'present' => $attendanceStats['PRESENT'] ?? 0,
                'absent' => $attendanceStats['ABSENT'] ?? 0,
                'late' => $attendanceStats['LATE'] ?? 0,
                'excused' => $attendanceStats['EXCUSED'] ?? 0,
            ],
            'payments' => [
                'total_fees' => $paymentStats->total_fees ?? 0,
                'total_paid' => $paymentStats->total_paid ?? 0,
                'total_balance' => $paymentStats->total_balance ?? 0,
            ],
            'sessions' => [
                'completed' => $sessionStats['COMPLETED'] ?? 0,
                'scheduled' => $sessionStats['SCHEDULED'] ?? 0,
                'cancelled' => $sessionStats['CANCELLED'] ?? 0,
            ],
        ];
    }

    /**
     * Get recent attendance records
     */
    private function getRecentAttendance(array $studentIds, int $limit = 5): \Illuminate\Support\Collection
    {
        return AttendanceRecord::whereIn('student_id', $studentIds)
            ->with('student:id,first_name,last_name,student_no')
            ->orderBy('attendance_date', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get recent payment transactions
     */
    private function getRecentPayments(array $studentIds, int $limit = 5): \Illuminate\Support\Collection
    {
        return PaymentTransaction::whereHas('enrollment', function ($query) use ($studentIds) {
                $query->whereIn('student_id', $studentIds);
            })
            ->with(['enrollment.student:id,first_name,last_name,student_no'])
            ->where('type', 'PAYMENT')
            ->orderBy('transaction_date', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get upcoming sessions
     */
    private function getUpcomingSessions(array $studentIds, int $limit = 5): \Illuminate\Support\Collection
    {
        return SessionOccurrence::whereIn('student_id', $studentIds)
            ->where('session_date', '>=', today())
            ->where('status', 'SCHEDULED')
            ->with([
                'student:id,first_name,last_name,student_no',
                'sessionType:id,name'
            ])
            ->orderBy('session_date')
            ->orderBy('start_time')
            ->limit($limit)
            ->get();
    }

    /**
     * Get alerts and notifications
     */
    private function getAlerts(array $studentIds): array
    {
        // Overdue payments count
        $overdueCount = DB::table('payment_schedules')
            ->join('enrollments', 'payment_schedules.enrollment_id', '=', 'enrollments.id')
            ->whereIn('enrollments.student_id', $studentIds)
            ->where('payment_schedules.status', 'OVERDUE')
            ->count();

        // Upcoming payments (within 7 days)
        $upcomingCount = DB::table('payment_schedules')
            ->join('enrollments', 'payment_schedules.enrollment_id', '=', 'enrollments.id')
            ->whereIn('enrollments.student_id', $studentIds)
            ->where('payment_schedules.status', 'UNPAID')
            ->whereBetween('payment_schedules.due_date', [now(), now()->addDays(7)])
            ->count();

        // Low session balance (less than 5 remaining)
        $lowSessionsCount = Enrollment::whereIn('student_id', $studentIds)
            ->where('status', 'ACTIVE')
            ->with(['student', 'package'])
            ->get()
            ->filter(function ($enrollment) {
                if (!$enrollment->package || !$enrollment->package->total_sessions) {
                    return false;
                }
                $used = SessionOccurrence::where('student_id', $enrollment->student_id)
                    ->where('status', 'COMPLETED')
                    ->count();
                $remaining = $enrollment->package->total_sessions - $used;
                return $remaining > 0 && $remaining <= 5;
            })
            ->count();

        return [
            'overdue_payments' => $overdueCount,
            'upcoming_payments' => $upcomingCount,
            'low_sessions' => $lowSessionsCount,
            'total' => $overdueCount + $upcomingCount + $lowSessionsCount,
        ];
    }

    /**
     * Get empty dashboard data structure
     */
    private function getEmptyDashboardData(): array
    {
        return [
            'students' => collect(),
            'summary' => [
                'attendance' => ['present' => 0, 'absent' => 0, 'late' => 0, 'excused' => 0],
                'payments' => ['total_fees' => 0, 'total_paid' => 0, 'total_balance' => 0],
                'sessions' => ['completed' => 0, 'scheduled' => 0, 'cancelled' => 0],
            ],
            'recent_attendance' => collect(),
            'recent_payments' => collect(),
            'upcoming_sessions' => collect(),
            'alerts' => ['overdue_payments' => 0, 'upcoming_payments' => 0, 'low_sessions' => 0, 'total' => 0],
        ];
    }

    /**
     * Clear cache for a guardian
     */
    public function clearCache(Guardian $guardian): void
    {
        Cache::forget("parent_dashboard_{$guardian->id}");
        Cache::forget("parent_notifications_{$guardian->id}");
    }
}
