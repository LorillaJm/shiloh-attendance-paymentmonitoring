<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== STUDENTS WITH ENROLLMENTS ===\n\n";

$students = App\Models\Student::withCount('enrollments')
    ->get()
    ->filter(fn($s) => $s->enrollments_count > 0);

if ($students->isEmpty()) {
    echo "No students with enrollments found.\n";
    exit;
}

foreach ($students as $student) {
    echo "{$student->student_no} - {$student->full_name} ({$student->enrollments_count} enrollment(s))\n";
}

echo "\n✅ Total: {$students->count()} student(s) with enrollments\n";
