<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== LINK PARENT TO STUDENT ===\n\n";

// Find the parent user
$parentEmail = 'parent1@shiloh.test';
$parent = App\Models\User::where('email', $parentEmail)->first();

if (!$parent) {
    echo "❌ Parent user not found: {$parentEmail}\n";
    echo "Available parent users:\n";
    App\Models\User::where('role', 'PARENT')->get()->each(function($u) {
        echo "  - {$u->email}\n";
    });
    exit;
}

echo "Found parent user:\n";
echo "  Name: {$parent->name}\n";
echo "  Email: {$parent->email}\n";
echo "  Role: {$parent->role->value}\n\n";

// Get or create guardian profile
$guardian = App\Models\Guardian::where('user_id', $parent->id)->first();

if (!$guardian) {
    echo "Creating guardian profile...\n";
    
    // Split name
    $nameParts = explode(' ', trim($parent->name));
    $firstName = $nameParts[0] ?? 'Parent';
    $lastName = end($nameParts);
    $middleName = count($nameParts) > 2 ? $nameParts[1] : null;
    
    $guardian = App\Models\Guardian::create([
        'user_id' => $parent->id,
        'first_name' => $firstName,
        'last_name' => $lastName,
        'middle_name' => $middleName,
        'relationship' => 'Parent',
        'contact_number' => '09123456789',
        'email' => $parent->email,
    ]);
    
    echo "✓ Created guardian profile\n\n";
} else {
    echo "Guardian profile exists (ID: {$guardian->id})\n\n";
}

// Show available students
echo "Available students:\n";
$students = App\Models\Student::orderBy('student_no')->get();
foreach ($students as $student) {
    echo "  {$student->id}. {$student->student_no} - {$student->full_name}\n";
}

echo "\nWhich student(s) do you want to link? (Enter student IDs separated by comma, or 'all' for all students)\n";
echo "Example: 1,2,3 or just press Enter to link first student with enrollment\n";

// For automation, let's link to a student with enrollment
$studentWithEnrollment = App\Models\Student::whereHas('enrollments')->first();

if (!$studentWithEnrollment) {
    echo "\n❌ No students with enrollments found.\n";
    echo "Linking to first available student...\n";
    $studentWithEnrollment = App\Models\Student::first();
}

if (!$studentWithEnrollment) {
    echo "\n❌ No students found in database.\n";
    exit;
}

echo "\nLinking to: {$studentWithEnrollment->student_no} - {$studentWithEnrollment->full_name}\n";

// Check if already linked
$alreadyLinked = \DB::table('guardian_student')
    ->where('guardian_id', $guardian->id)
    ->where('student_id', $studentWithEnrollment->id)
    ->exists();

if ($alreadyLinked) {
    echo "⚠️  Already linked to this student\n";
} else {
    // Link guardian to student
    \DB::table('guardian_student')->insert([
        'guardian_id' => $guardian->id,
        'student_id' => $studentWithEnrollment->id,
        'is_primary' => true,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    
    echo "✓ Successfully linked!\n";
}

// Show all linked students
echo "\n=== LINKED STUDENTS ===\n";
$linkedStudents = \DB::table('guardian_student')
    ->join('students', 'guardian_student.student_id', '=', 'students.id')
    ->where('guardian_student.guardian_id', $guardian->id)
    ->select('students.*')
    ->get();

foreach ($linkedStudents as $student) {
    echo "  ✓ {$student->student_no} - {$student->first_name} {$student->last_name}\n";
}

echo "\n=== LOGIN CREDENTIALS ===\n";
echo "URL: http://127.0.0.1:8000/parent\n";
echo "Email: {$parent->email}\n";
echo "Password: (use existing password)\n\n";

echo "✅ Done!\n";
