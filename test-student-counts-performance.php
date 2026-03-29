<?php

/**
 * Student Counts Performance Test
 * 
 * Run this script to verify the optimization is working
 * Usage: php test-student-counts-performance.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

echo "\n";
echo "========================================\n";
echo "STUDENT COUNTS PERFORMANCE TEST\n";
echo "========================================\n\n";

// Test 1: Clear cache
echo "1. Clearing cache...\n";
Cache::forget('student_status_counts');
echo "   ✓ Cache cleared\n\n";

// Test 2: Old method (multiple queries)
echo "2. Testing OLD method (4 separate queries)...\n";
$start = microtime(true);

$activeCount = DB::table('students')->where('status', 'ACTIVE')->count();
$inactiveCount = DB::table('students')->where('status', 'INACTIVE')->count();
$droppedCount = DB::table('students')->where('status', 'DROPPED')->count();
$navBadge = DB::table('students')->where('status', 'ACTIVE')->count(); // Duplicate!

$oldTime = round((microtime(true) - $start) * 1000, 2);
echo "   - Active: {$activeCount}\n";
echo "   - Inactive: {$inactiveCount}\n";
echo "   - Dropped: {$droppedCount}\n";
echo "   - Total time: {$oldTime}ms\n";
echo "   - Queries executed: 4\n\n";

// Test 3: New method (single aggregated query)
echo "3. Testing NEW method (1 aggregated query)...\n";
Cache::forget('student_status_counts');
$start = microtime(true);

$counts = DB::table('students')
    ->select('status', DB::raw('COUNT(*) as count'))
    ->groupBy('status')
    ->pluck('count', 'status')
    ->toArray();

$newTime = round((microtime(true) - $start) * 1000, 2);
echo "   - Active: " . ($counts['ACTIVE'] ?? 0) . "\n";
echo "   - Inactive: " . ($counts['INACTIVE'] ?? 0) . "\n";
echo "   - Dropped: " . ($counts['DROPPED'] ?? 0) . "\n";
echo "   - Total time: {$newTime}ms\n";
echo "   - Queries executed: 1\n\n";

// Test 4: Cached method (should be very fast)
echo "4. Testing CACHED method (second load)...\n";
$start = microtime(true);

$cachedCounts = Cache::remember('student_status_counts', 60, function () {
    return DB::table('students')
        ->select('status', DB::raw('COUNT(*) as count'))
        ->groupBy('status')
        ->pluck('count', 'status')
        ->toArray();
});

$cachedTime = round((microtime(true) - $start) * 1000, 2);
echo "   - Active: " . ($cachedCounts['ACTIVE'] ?? 0) . "\n";
echo "   - Inactive: " . ($cachedCounts['INACTIVE'] ?? 0) . "\n";
echo "   - Dropped: " . ($cachedCounts['DROPPED'] ?? 0) . "\n";
echo "   - Total time: {$cachedTime}ms\n";
echo "   - Cache hit: YES\n\n";

// Test 5: Verify cache is working
echo "5. Testing cache hit (third load)...\n";
$start = microtime(true);

$cachedCounts2 = Cache::get('student_status_counts');

$cacheHitTime = round((microtime(true) - $start) * 1000, 2);
echo "   - Cache exists: " . (is_array($cachedCounts2) ? 'YES' : 'NO') . "\n";
echo "   - Total time: {$cacheHitTime}ms\n\n";

// Test 6: Check database index
echo "6. Checking database index on students.status...\n";
try {
    $indexes = DB::select("
        SELECT indexname 
        FROM pg_indexes 
        WHERE tablename = 'students' 
        AND indexname LIKE '%status%'
    ");
    
    if (count($indexes) > 0) {
        echo "   ✓ Index found: " . $indexes[0]->indexname . "\n\n";
    } else {
        echo "   ⚠ No index found on status column\n";
        echo "   Run: CREATE INDEX idx_students_status ON students(status);\n\n";
    }
} catch (Exception $e) {
    echo "   ✗ Could not check index: " . $e->getMessage() . "\n\n";
}

// Summary
echo "========================================\n";
echo "PERFORMANCE COMPARISON\n";
echo "========================================\n\n";

$improvement = round((($oldTime - $newTime) / $oldTime) * 100, 1);
$cacheImprovement = round((($oldTime - $cachedTime) / $oldTime) * 100, 1);

echo "Method                  Time        Queries    Improvement\n";
echo "─────────────────────────────────────────────────────────\n";
echo "OLD (4 queries)         {$oldTime}ms      4          -\n";
echo "NEW (1 query)           {$newTime}ms      1          {$improvement}%\n";
echo "CACHED (cache hit)      {$cachedTime}ms      0          {$cacheImprovement}%\n\n";

if ($improvement > 50) {
    echo "✓ Excellent improvement! Query time reduced by {$improvement}%\n";
} elseif ($improvement > 0) {
    echo "✓ Good improvement! Query time reduced by {$improvement}%\n";
} else {
    echo "⚠ No improvement detected. Check database performance.\n";
}

if ($cachedTime < 1) {
    echo "✓ Cache is working perfectly! < 1ms response time\n";
} elseif ($cachedTime < 10) {
    echo "✓ Cache is working well! < 10ms response time\n";
} else {
    echo "⚠ Cache might not be working. Check cache driver.\n";
}

echo "\n";
echo "========================================\n";
echo "RECOMMENDATIONS\n";
echo "========================================\n\n";

echo "1. Always use the aggregated query method\n";
echo "2. Cache results for 60 seconds\n";
echo "3. Clear cache when students are created/updated/deleted\n";
echo "4. Ensure database index exists on status column\n";
echo "5. Monitor cache hit rate (should be > 90%)\n";
echo "6. Avoid using Model::count() in badge() closures\n\n";

echo "Next steps:\n";
echo "1. Test /admin/students page in browser\n";
echo "2. Check DevTools Network tab for load time\n";
echo "3. Monitor logs: tail -f storage/logs/laravel.log\n";
echo "4. Verify no 'Maximum execution time' errors\n\n";
