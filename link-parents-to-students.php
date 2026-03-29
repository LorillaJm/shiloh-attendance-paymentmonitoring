<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Guardian;
use App\Models\Student;
use App\Enums\UserRole;
use Illuminate\Support\Facades\DB;

echo "\n";
echo "╔════════════════════════════════════════════════════════════════╗\n";
echo "║         LINK PARENTS TO STUDENTS                               ║\n";
echo "╚════════════════════════════════════════════════════════════════╝\n";
echo "\n";

// Get all parent users with guardians
$parentUsers = User::where('role', UserRole::PARENT)
    ->with('guardian')
    ->get();

if ($parentUsers->isEmpty()) {
    echo "❌ No parent users found. Please run:\n";
    echo "   php artisan db:seed --class=DefaultUsersSeeder\n\n";
    exit(1);
}

echo "Found " . $parentUsers->count() . " parent user(s)\n\n";

// Get all students
$students = Student::orderBy('created_at')->get();

if ($students->isEmpty()) {
    echo "❌ No students found in the database.\n";
    echo "   Please create students first via the admin panel.\n\n";
    exit(1);
}

echo "Found " . $students->count() . " student(s)\n\n";

// Link parents to students
$linkedCount = 0;

foreach ($parentUsers as $index => $user) {
    if (!$user->guardian) {
        echo "⚠️  User {$user->email} has no guardian record. Skipping...\n";
        continue;
    }

    $guardian = $user->guardian;
    
    // Get students for this guardian (distribute students among parents)
    $studentsToLink = $students->slice($index * 2, 2); // Each parent gets 2 students
    
    if ($studentsToLink->isEmpty()) {
        // If no more students, link to first student
        $studentsToLink = $students->take(1);
    }

    foreach ($studentsToLink as $student) {
        // Check if already linked
        $exists = DB::table('guardian_student')
            ->where('guardian_id', $guardian->id)
            ->where('student_id', $student->id)
            ->exists();

        if (!$exists) {
            DB::table('guardian_student')->insert([
                'guardian_id' => $guardian->id,
                'student_id' => $student->id,
                'is_primary' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            echo "✓ Linked {$student->full_name} (#{$student->student_no}) to {$user->name} ({$user->email})\n";
            $linkedCount++;
        } else {
            echo "  Already linked: {$student->full_name} to {$user->name}\n";
        }
    }
    
    echo "\n";
}

echo "╔════════════════════════════════════════════════════════════════╗\n";
echo "║                         SUMMARY                                ║\n";
echo "╚════════════════════════════════════════════════════════════════╝\n";
echo "\n";
echo "✅ Successfully linked {$linkedCount} student(s) to parent(s)\n\n";

// Show summary
echo "Parent-Student Relationships:\n";
echo "─────────────────────────────────────────────────────────────────\n";

foreach ($parentUsers as $user) {
    if ($user->guardian) {
        $guardian = $user->guardian;
        $linkedStudents = $guardian->students;
        
        echo "\n👤 {$user->name} ({$user->email})\n";
        if ($linkedStudents->count() > 0) {
            foreach ($linkedStudents as $student) {
                echo "   └─ {$student->full_name} (#{$student->student_no})\n";
            }
        } else {
            echo "   └─ No students linked\n";
        }
    }
}

echo "\n";
echo "You can now login as any parent to see their children's data:\n";
echo "  URL: /parent/login\n";
echo "  Password: password (for all test accounts)\n";
echo "\n";
