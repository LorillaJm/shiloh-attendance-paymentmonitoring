<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== CREATING PARENT USER ===\n\n";

// Find a student to link to parent
$student = App\Models\Student::where('student_no', 'SHILOH-2026-0063')->first();

if (!$student) {
    echo "Student not found. Using first available student.\n";
    $student = App\Models\Student::first();
}

if (!$student) {
    echo "No students found. Please create a student first.\n";
    exit;
}

echo "Student: {$student->student_no} - {$student->full_name}\n";
echo "Guardian: {$student->guardian_name}\n";
echo "Contact: {$student->guardian_contact}\n\n";

// Check if parent user already exists
$existingUser = App\Models\User::where('email', 'parent@shiloh.com')->first();

if ($existingUser) {
    echo "Parent user already exists!\n";
    echo "Email: parent@shiloh.com\n";
    echo "Password: password\n\n";
    
    // Check if guardian profile exists
    $guardian = App\Models\Guardian::where('user_id', $existingUser->id)->first();
    
    if (!$guardian) {
        // Split guardian name
        $nameParts = explode(' ', trim($student->guardian_name));
        $firstName = $nameParts[0] ?? '';
        $lastName = end($nameParts);
        $middleName = count($nameParts) > 2 ? $nameParts[1] : null;
        
        // Create guardian profile
        $guardian = App\Models\Guardian::create([
            'user_id' => $existingUser->id,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'middle_name' => $middleName,
            'relationship' => 'Parent',
            'contact_number' => $student->guardian_contact,
            'email' => 'parent@shiloh.com',
        ]);
        echo "✓ Created guardian profile\n";
    }
    
    // Link to student if not already linked
    $linked = \DB::table('guardian_student')
        ->where('guardian_id', $guardian->id)
        ->where('student_id', $student->id)
        ->exists();
    
    if (!$linked) {
        \DB::table('guardian_student')->insert([
            'guardian_id' => $guardian->id,
            'student_id' => $student->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        echo "✓ Linked to student: {$student->full_name}\n";
    } else {
        echo "✓ Already linked to student\n";
    }
    
    echo "\n=== LOGIN CREDENTIALS ===\n";
    echo "URL: http://127.0.0.1:8000/parent\n";
    echo "Email: parent@shiloh.com\n";
    echo "Password: password\n";
    
    exit;
}

// Create parent user
$parent = App\Models\User::create([
    'name' => $student->guardian_name,
    'email' => 'parent@shiloh.com',
    'password' => bcrypt('password'),
    'role' => 'PARENT',
    'email_verified_at' => now(),
]);

echo "✓ Created parent user\n";
echo "  Name: {$parent->name}\n";
echo "  Email: {$parent->email}\n";
echo "  Password: password\n";
echo "  Role: PARENT\n\n";

// Split guardian name
$nameParts = explode(' ', trim($student->guardian_name));
$firstName = $nameParts[0] ?? '';
$lastName = end($nameParts);
$middleName = count($nameParts) > 2 ? $nameParts[1] : null;

// Create guardian profile
$guardian = App\Models\Guardian::create([
    'user_id' => $parent->id,
    'first_name' => $firstName,
    'last_name' => $lastName,
    'middle_name' => $middleName,
    'relationship' => 'Parent',
    'contact_number' => $student->guardian_contact,
    'email' => 'parent@shiloh.com',
]);

echo "✓ Created guardian profile\n\n";

// Link guardian to student
\DB::table('guardian_student')->insert([
    'guardian_id' => $guardian->id,
    'student_id' => $student->id,
    'created_at' => now(),
    'updated_at' => now(),
]);

echo "✓ Linked guardian to student: {$student->full_name}\n\n";

echo "=== LOGIN CREDENTIALS ===\n";
echo "URL: http://127.0.0.1:8000/parent\n";
echo "Email: parent@shiloh.com\n";
echo "Password: password\n\n";

echo "✅ Parent user created successfully!\n";
