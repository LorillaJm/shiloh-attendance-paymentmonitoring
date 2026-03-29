<?php

/**
 * Dashboard Performance Testing Script
 * 
 * Run this script to test dashboard query performance
 * Usage: php test-dashboard-performance.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

echo "\n";
echo "========================================\n";
echo "DASHBOARD PERFORMANCE TEST\n";
echo "========================================\n\n";

// Test 1: Database Connection
echo "1. Testing database connection...\n";
try {
    DB::connection()->getPdo();
    echo "   ✓ Database connected\n\n";
} catch (Exception $e) {
    echo "   ✗ Database connection failed: " . $e->getMessage() . "\n\n";
    exit(1);
}

// Test 2: Cache Connection
echo "2. Testing cache connection...\n";
try {
    Cache::put('test_key', 'test_value', 60);
    $value = Cache::get('test_key');
    Cache::forget('test_key');
    
    if ($value === 'test_value') {
        echo "   ✓ Cache working (Driver: " . config('cache.default') . ")\n\n";
    } else {
        echo "   ✗ Cache not working properly\n\n";
    }
} catch (Exception $e) {
    echo "   ✗ Cache error: " . $e->getMessage() . "\n\n";
}

// Test 3: KPI Stats Query
echo "3. Testing KPI stats query...\n";
$start = microtime(true);
try {
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
    
    $time = round((microtime(true) - $start) * 1000, 2);
    echo "   ✓ Query executed in {$time}ms\n";
    echo "   - Total Students: {$kpis->total_students}\n";
    echo "   - Active Students: {$kpis->active_students}\n";
    echo "   - Overdue: {$kpis->overdue}\n\n";
    
    if ($time > 1000) {
        echo "   ⚠ Warning: Query took longer than 1 second\n\n";
    }
} catch (Exception $e) {
    echo "   ✗ Query failed: " . $e->getMessage() . "\n\n";
}

// Test 4: Collections Trend Query
echo "4. Testing collections trend query...\n";
$start = microtime(true);
try {
    $today = now('Asia/Manila');
    $startDate = $today->copy()->subDays(29);
    
    $collections = DB::table('payment_schedules')
        ->select(
            DB::raw('DATE(paid_at) as date'),
            DB::raw('SUM(amount_due) as total')
        )
        ->where('status', 'PAID')
        ->whereBetween('paid_at', [$startDate->startOfDay(), $today->endOfDay()])
        ->groupBy('date')
        ->orderBy('date')
        ->get();
    
    $time = round((microtime(true) - $start) * 1000, 2);
    echo "   ✓ Query executed in {$time}ms\n";
    echo "   - Days with data: " . $collections->count() . "\n\n";
    
    if ($time > 500) {
        echo "   ⚠ Warning: Query took longer than 500ms\n\n";
    }
} catch (Exception $e) {
    echo "   ✗ Query failed: " . $e->getMessage() . "\n\n";
}

// Test 5: Alerts Query
echo "5. Testing alerts query...\n";
$start = microtime(true);
try {
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
    
    $time = round((microtime(true) - $start) * 1000, 2);
    echo "   ✓ Query executed in {$time}ms\n";
    echo "   - Overdue: {$alerts->overdue_count}\n";
    echo "   - Due Soon: {$alerts->due_soon_count}\n";
    echo "   - Missing Attendance: {$alerts->missing_attendance_count}\n\n";
    
    if ($time > 500) {
        echo "   ⚠ Warning: Query took longer than 500ms\n\n";
    }
} catch (Exception $e) {
    echo "   ✗ Query failed: " . $e->getMessage() . "\n\n";
}

// Test 6: Recent Payments Query
echo "6. Testing recent payments query...\n";
$start = microtime(true);
try {
    $payments = DB::table('payment_schedules as ps')
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
        ->limit(10)
        ->get();
    
    $time = round((microtime(true) - $start) * 1000, 2);
    echo "   ✓ Query executed in {$time}ms\n";
    echo "   - Recent payments: " . $payments->count() . "\n\n";
    
    if ($time > 500) {
        echo "   ⚠ Warning: Query took longer than 500ms\n\n";
    }
} catch (Exception $e) {
    echo "   ✗ Query failed: " . $e->getMessage() . "\n\n";
}

// Test 7: Check Indexes
echo "7. Checking database indexes...\n";
try {
    $indexes = DB::select("
        SELECT 
            tablename,
            indexname
        FROM pg_indexes
        WHERE schemaname = 'public'
        AND (
            indexname LIKE 'idx_ps_%' OR
            indexname LIKE 'idx_students_%' OR
            indexname LIKE 'idx_enrollments_%' OR
            indexname LIKE 'idx_attendance_%'
        )
        ORDER BY tablename, indexname
    ");
    
    echo "   ✓ Found " . count($indexes) . " dashboard indexes\n";
    foreach ($indexes as $index) {
        echo "   - {$index->tablename}.{$index->indexname}\n";
    }
    echo "\n";
} catch (Exception $e) {
    echo "   ✗ Failed to check indexes: " . $e->getMessage() . "\n\n";
}

// Test 8: Cache Warmup
echo "8. Testing cache warmup...\n";
$start = microtime(true);
try {
    \App\Services\DashboardCacheService::clearAll();
    \App\Services\DashboardCacheService::warmUp();
    
    $time = round((microtime(true) - $start) * 1000, 2);
    echo "   ✓ Cache warmed up in {$time}ms\n\n";
    
    if ($time > 3000) {
        echo "   ⚠ Warning: Cache warmup took longer than 3 seconds\n\n";
    }
} catch (Exception $e) {
    echo "   ✗ Cache warmup failed: " . $e->getMessage() . "\n\n";
}

// Summary
echo "========================================\n";
echo "PERFORMANCE TEST COMPLETE\n";
echo "========================================\n\n";

echo "Recommendations:\n";
echo "- All queries should complete in < 500ms\n";
echo "- Total dashboard load should be < 1 second\n";
echo "- Cache hit rate should be > 90%\n";
echo "- Monitor logs for slow queries\n\n";

echo "Next steps:\n";
echo "1. Run: php artisan dashboard:warm-cache\n";
echo "2. Test dashboard in browser\n";
echo "3. Check DevTools Network tab\n";
echo "4. Monitor logs: tail -f storage/logs/laravel.log\n\n";
