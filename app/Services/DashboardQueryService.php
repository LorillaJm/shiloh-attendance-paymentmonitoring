<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class DashboardQueryService
{
    /**
     * Get KPI statistics using optimized single query
     */
    public static function getKpiStats(): array
    {
        $today = now('Asia/Manila')->format('Y-m-d');
        $thisMonth = now('Asia/Manila');
        
        $kpis = DB::selectOne("
            SELECT 
                (SELECT COUNT(*) FROM students WHERE status = 'ACTIVE') as total_students,
                (SELECT COUNT(*) FROM students WHERE status = 'ACTIVE' 
                 AND EXISTS (SELECT 1 FROM enrollments WHERE student_id = students.id AND status = 'ACTIVE')) as active_students,
                (SELECT COUNT(*) FROM payment_schedules WHERE status = 'UNPAID' AND due_date = ?) as due_today,
                (SELECT COUNT(*) FROM payment_schedules WHERE status = 'UNPAID' AND due_date < ?) as overdue,
                (SELECT COALESCE(SUM(amount_due), 0) FROM payment_schedules WHERE status = 'PAID' AND DATE(paid_at) = ?) as collected_today,
                (SELECT COALESCE(SUM(amount_due), 0) FROM payment_schedules WHERE status = 'PAID' 
                 AND EXTRACT(YEAR FROM paid_at) = ? AND EXTRACT(MONTH FROM paid_at) = ?) as collected_this_month,
                (SELECT COALESCE(SUM(remaining_balance), 0) FROM enrollments WHERE status = 'ACTIVE') as outstanding_balance
        ", [$today, $today, $today, $thisMonth->year, $thisMonth->month]);

        return [
            'total_students' => $kpis->total_students ?? 0,
            'active_students' => $kpis->active_students ?? 0,
            'due_today' => $kpis->due_today ?? 0,
            'overdue' => $kpis->overdue ?? 0,
            'collected_today' => $kpis->collected_today ?? 0,
            'collected_this_month' => $kpis->collected_this_month ?? 0,
            'outstanding_balance' => $kpis->outstanding_balance ?? 0,
        ];
    }

    /**
     * Get financial summary
     */
    public static function getFinancialSummary(): array
    {
        $thisMonth = now('Asia/Manila');
        $lastMonth = now('Asia/Manila')->subMonth();
        
        $financial = DB::selectOne("
            SELECT 
                (SELECT COALESCE(SUM(amount_due), 0) FROM payment_schedules 
                 WHERE status = 'PAID' 
                 AND EXTRACT(YEAR FROM paid_at) = ? 
                 AND EXTRACT(MONTH FROM paid_at) = ?) as revenue_this_month,
                (SELECT COALESCE(SUM(amount_due), 0) FROM payment_schedules 
                 WHERE status = 'PAID' 
                 AND EXTRACT(YEAR FROM paid_at) = ? 
                 AND EXTRACT(MONTH FROM paid_at) = ?) as revenue_last_month,
                (SELECT COALESCE(SUM(remaining_balance), 0) FROM enrollments 
                 WHERE status = 'ACTIVE') as outstanding_balance
        ", [
            $thisMonth->year, $thisMonth->month,
            $lastMonth->year, $lastMonth->month
        ]);

        $revenueThisMonth = $financial->revenue_this_month ?? 0;
        $revenueLastMonth = $financial->revenue_last_month ?? 0;
        
        $growthPercent = 0;
        if ($revenueLastMonth > 0) {
            $growthPercent = (($revenueThisMonth - $revenueLastMonth) / $revenueLastMonth) * 100;
        }

        return [
            'revenue_this_month' => $revenueThisMonth,
            'revenue_last_month' => $revenueLastMonth,
            'growth_percent' => $growthPercent,
            'outstanding_balance' => $financial->outstanding_balance ?? 0,
        ];
    }

    /**
     * Get attendance summary for today
     */
    public static function getAttendanceSummary(): array
    {
        $today = now('Asia/Manila')->format('Y-m-d');
        
        $attendance = DB::selectOne("
            SELECT 
                (SELECT COUNT(*) FROM attendance_records 
                 WHERE DATE(attendance_date) = ? AND status = 'PRESENT') as present_today,
                (SELECT COUNT(*) FROM attendance_records 
                 WHERE DATE(attendance_date) = ? AND status = 'ABSENT') as absent_today,
                (SELECT COUNT(*) FROM attendance_records 
                 WHERE DATE(attendance_date) = ? AND status = 'LATE') as late_today,
                (SELECT COUNT(*) FROM attendance_records 
                 WHERE DATE(attendance_date) = ? AND status = 'EXCUSED') as excused_today
        ", [$today, $today, $today, $today]);

        return [
            'present_today' => $attendance->present_today ?? 0,
            'absent_today' => $attendance->absent_today ?? 0,
            'late_today' => $attendance->late_today ?? 0,
            'excused_today' => $attendance->excused_today ?? 0,
        ];
    }

    /**
     * Get alert counts
     */
    public static function getAlerts(): array
    {
        $today = now('Asia/Manila')->format('Y-m-d');
        $sevenDaysFromNow = now('Asia/Manila')->addDays(7)->format('Y-m-d');

        $alerts = DB::selectOne("
            SELECT 
                (SELECT COUNT(*) FROM payment_schedules WHERE status = 'UNPAID' AND due_date < ?) as overdue_count,
                (SELECT COUNT(*) FROM payment_schedules WHERE status = 'UNPAID' AND due_date BETWEEN ? AND ?) as due_soon_count,
                (SELECT COUNT(*) FROM students WHERE status = 'ACTIVE' 
                 AND NOT EXISTS (
                     SELECT 1 FROM attendance_records 
                     WHERE attendance_records.student_id = students.id 
                     AND DATE(attendance_date) = ?
                 )) as missing_attendance_count
        ", [$today, $today, $sevenDaysFromNow, $today]);

        return [
            'overdue_count' => $alerts->overdue_count ?? 0,
            'due_soon_count' => $alerts->due_soon_count ?? 0,
            'missing_attendance_count' => $alerts->missing_attendance_count ?? 0,
        ];
    }

    /**
     * Get collections trend data
     */
    public static function getCollectionsTrend(int $days = 30): array
    {
        $today = now('Asia/Manila');
        $startDate = $today->copy()->subDays($days - 1);

        $collections = DB::table('payment_schedules')
            ->select(
                DB::raw('DATE(paid_at) as date'),
                DB::raw('SUM(amount_due) as total')
            )
            ->where('status', 'PAID')
            ->whereBetween('paid_at', [$startDate->startOfDay(), $today->endOfDay()])
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        $labels = [];
        $data = [];
        
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = $today->copy()->subDays($i);
            $dateStr = $date->format('Y-m-d');
            $labels[] = $date->format('M d');
            $data[] = $collections->get($dateStr)?->total ?? 0;
        }

        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }

    /**
     * Get recent payments (limited to 10)
     */
    public static function getRecentPayments(int $limit = 10): array
    {
        return DB::table('payment_schedules as ps')
            ->join('enrollments as e', 'ps.enrollment_id', '=', 'e.id')
            ->join('students as s', 'e.student_id', '=', 's.id')
            ->join('packages as p', 'e.package_id', '=', 'p.id')
            ->select(
                'ps.paid_at',
                's.student_no',
                DB::raw("CONCAT(s.first_name, ' ', s.last_name) as student_name"),
                'p.name as package_name',
                'ps.installment_no',
                'ps.amount_due',
                'ps.payment_method'
            )
            ->where('ps.status', 'PAID')
            ->whereDate('ps.paid_at', '>=', now('Asia/Manila')->subDays(7))
            ->orderBy('ps.paid_at', 'desc')
            ->limit($limit)
            ->get()
            ->toArray();
    }
}
