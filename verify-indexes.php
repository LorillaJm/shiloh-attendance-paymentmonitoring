<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Verifying Database Indexes\n";
echo "==========================\n\n";

$tables = ['enrollments', 'payment_schedules', 'students', 'attendance_records'];

foreach ($tables as $table) {
    $indexes = DB::select("
        SELECT indexname 
        FROM pg_indexes 
        WHERE tablename = ? 
        AND schemaname = 'public'
        ORDER BY indexname
    ", [$table]);
    
    echo "$table (" . count($indexes) . " indexes):\n";
    foreach ($indexes as $index) {
        echo "  - {$index->indexname}\n";
    }
    echo "\n";
}

echo "Total indexes across critical tables: " . 
    DB::selectOne("
        SELECT COUNT(*) as count 
        FROM pg_indexes 
        WHERE tablename IN ('enrollments', 'payment_schedules', 'students', 'attendance_records')
        AND schemaname = 'public'
    ")->count . "\n";
