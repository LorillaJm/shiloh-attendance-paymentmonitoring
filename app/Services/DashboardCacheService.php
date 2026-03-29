<?php

namespace App\Services;

use App\Models\Student;
use App\Models\AttendanceRecord;
use App\Models\PaymentTransaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class DashboardCacheService
{
    const CACHE_TTL = 300; // 5 minutes
    const CACHE_TAG = 'dashboard';

    /**
     * Get student counts with caching.
     *
     * @return array
     */
    public static function getStudentCounts(): array
    {
        return Cache::remember(
            'dashboard.student_counts',
            self::CACHE_TTL,
            function () {
                return [
                    'total' => Student::count(),
                    'active' => Student::where('status', 'ACTIVE')->count(),
                    'inactive' => Student::where('status', 'INACTIVE')->count(),
                    'dropped' => Student::where('status', 'DROPPED')->count(),
                ];
            }
        );
    }

    /**
     * Get attendance summary for a specific date with caching.
     *
     * @param Carbon $date
     * @return array
     */
    public static function getAttendanceSummary(Carbon $date): array
    {
        $cacheKey = "dashboard.attendance.{$date->format('Y-m-d')}";

        return Cache::remember(
            $cacheKey,
            self::CACHE_TTL,
            function () use ($date) {
                $summary = AttendanceRecord::where('attendance_date', $date)
                    ->selectRaw('status, COUNT(*) as count')
                    ->groupBy('status')
                    ->pluck('count', 'status')
                    ->toArray();

                // Ensure all statuses are present
                return [
                    'PRESENT' => $summary['PRESENT'] ?? 0,
                    'ABSENT' => $summary['ABSENT'] ?? 0,
                    'LATE' => $summary['LATE'] ?? 0,
                    'EXCUSED' => $summary['EXCUSED'] ?? 0,
                    'total' => array_sum($summary),
                ];
            }
        );
    }

    /**
     * Get payments summary for a specific month with caching.
     *
     * @param Carbon $date
     * @return array
     */
    public static function getPaymentsSummary(Carbon $date): array
    {
        $cacheKey = "dashboard.payments.{$date->format('Y-m')}";

        return Cache::remember(
            $cacheKey,
            self::CACHE_TTL,
            function () use ($date) {
                $today = $date->format('Y-m-d');
                $startOfMonth = $date->copy()->startOfMonth();
                $endOfMonth = $date->copy()->endOfMonth();

                // Get payment schedules data
                $dueToday = \DB::table('payment_schedules')
                    ->where('status', 'UNPAID')
                    ->where('due_date', $today)
                    ->count();

                $overdue = \DB::table('payment_schedules')
                    ->where('status', 'UNPAID')
                    ->where('due_date', '<', $today)
                    ->count();

                $collectedToday = \DB::table('payment_schedules')
                    ->where('status', 'PAID')
                    ->whereDate('paid_at', $today)
                    ->sum('amount_due');

                $collectedThisMonth = \DB::table('payment_schedules')
                    ->where('status', 'PAID')
                    ->whereBetween('paid_at', [$startOfMonth, $endOfMonth])
                    ->sum('amount_due');

                $outstandingBalance = \DB::table('enrollments')
                    ->where('status', 'ACTIVE')
                    ->sum('remaining_balance');

                return [
                    'due_today' => $dueToday,
                    'overdue' => $overdue,
                    'collected_today' => $collectedToday ?? 0,
                    'collected_this_month' => $collectedThisMonth ?? 0,
                    'outstanding_balance' => $outstandingBalance ?? 0,
                ];
            }
        );
    }

    /**
     * Clear all dashboard caches.
     *
     * @return void
     */
    public static function clearAll(): void
    {
        Cache::forget('dashboard.student_counts');
        // Clear other specific cache keys as needed
    }

    /**
     * Clear student counts cache.
     *
     * @return void
     */
    public static function clearStudentCounts(): void
    {
        Cache::forget('dashboard.student_counts');
    }

    /**
     * Clear attendance cache for a specific date.
     *
     * @param Carbon|null $date
     * @return void
     */
    public static function clearAttendanceSummary(?Carbon $date = null): void
    {
        if ($date) {
            $cacheKey = "dashboard.attendance.{$date->format('Y-m-d')}";
            Cache::forget($cacheKey);
        } else {
            // Clear all attendance caches
            self::clearAll();
        }
    }

    /**
     * Clear payments cache for a specific month.
     *
     * @param Carbon|null $date
     * @return void
     */
    public static function clearPaymentsSummary(?Carbon $date = null): void
    {
        if ($date) {
            $cacheKey = "dashboard.payments.{$date->format('Y-m')}";
            Cache::forget($cacheKey);
        } else {
            // Clear all payment caches
            self::clearAll();
        }
    }
}
