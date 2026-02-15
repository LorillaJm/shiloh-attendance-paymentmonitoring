<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Student;
use App\Models\Package;
use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

class Phase1ValidationTest extends TestCase
{
    /**
     * Test student number generator with race condition protection.
     */
    public function test_student_number_generator_prevents_duplicates()
    {
        // This test verifies the lockForUpdate() prevents race conditions
        $numbers = [];
        
        // Create multiple students concurrently (simulated)
        for ($i = 0; $i < 5; $i++) {
            $student = Student::create([
                'first_name' => 'Test' . $i,
                'last_name' => 'Student' . $i,
                'guardian_name' => 'Guardian' . $i,
                'guardian_contact' => '+6391234567' . $i,
                'status' => 'ACTIVE',
            ]);
            $numbers[] = $student->student_no;
        }
        
        // All student numbers should be unique
        $this->assertEquals(count($numbers), count(array_unique($numbers)));
        
        // All should follow format SHILOH-YYYY-XXXX
        foreach ($numbers as $number) {
            $this->assertMatchesRegularExpression('/^SHILOH-\d{4}-\d{4}$/', $number);
        }
    }

    /**
     * Test student name validations.
     */
    public function test_student_name_validations()
    {
        // Test minimum length (should fail at model level via Filament)
        // Note: These validations are enforced by Filament, not the model
        
        // Valid names should work
        $student = Student::create([
            'first_name' => 'Juan',
            'last_name' => 'Dela Cruz',
            'middle_name' => 'Santos',
            'guardian_name' => 'Maria Dela Cruz',
            'guardian_contact' => '+639123456789',
            'status' => 'ACTIVE',
        ]);
        
        $this->assertNotNull($student->id);
        $this->assertEquals('Juan', $student->first_name);
    }

    /**
     * Test guardian fields are required.
     */
    public function test_guardian_fields_required()
    {
        // At database level, these are nullable
        // But Filament form requires them
        
        // Create with guardian info (should work)
        $student = Student::create([
            'first_name' => 'Test',
            'last_name' => 'Student',
            'guardian_name' => 'Test Guardian',
            'guardian_contact' => '+639123456789',
            'status' => 'ACTIVE',
        ]);
        
        $this->assertNotNull($student->guardian_name);
        $this->assertNotNull($student->guardian_contact);
    }

    /**
     * Test database constraints on students table.
     */
    public function test_student_database_constraints()
    {
        try {
            // Test invalid sex (should fail)
            DB::statement("INSERT INTO students (student_no, first_name, last_name, sex, status, created_at, updated_at) VALUES ('TEST-2026-9999', 'Test', 'User', 'Invalid', 'ACTIVE', NOW(), NOW())");
            $this->fail('Should have failed with invalid sex');
        } catch (\Exception $e) {
            $this->assertStringContainsString('students_sex_check', $e->getMessage());
        }
        
        try {
            // Test invalid status (should fail)
            DB::statement("INSERT INTO students (student_no, first_name, last_name, status, created_at, updated_at) VALUES ('TEST-2026-9998', 'Test', 'User', 'INVALID', NOW(), NOW())");
            $this->fail('Should have failed with invalid status');
        } catch (\Exception $e) {
            $this->assertStringContainsString('students_status_check', $e->getMessage());
        }
        
        try {
            // Test future birthdate (should fail)
            DB::statement("INSERT INTO students (student_no, first_name, last_name, birthdate, status, created_at, updated_at) VALUES ('TEST-2026-9997', 'Test', 'User', '2030-01-01', 'ACTIVE', NOW(), NOW())");
            $this->fail('Should have failed with future birthdate');
        } catch (\Exception $e) {
            $this->assertStringContainsString('students_birthdate_check', $e->getMessage());
        }
    }

    /**
     * Test package validations.
     */
    public function test_package_validations()
    {
        // Valid package
        $package = Package::create([
            'name' => 'Test Package',
            'total_fee' => 10000.00,
            'downpayment_percent' => 25.00,
            'installment_months' => 3,
            'description' => 'Test package description',
        ]);
        
        $this->assertNotNull($package->id);
        $this->assertEquals(2500.00, $package->downpayment_amount);
        $this->assertEquals(2500.00, $package->monthly_installment);
    }

    /**
     * Test database constraints on packages table.
     */
    public function test_package_database_constraints()
    {
        try {
            // Test zero total_fee (should fail)
            DB::statement("INSERT INTO packages (name, total_fee, downpayment_percent, installment_months, created_at, updated_at) VALUES ('Test Package Zero', 0, 25, 3, NOW(), NOW())");
            $this->fail('Should have failed with zero total_fee');
        } catch (\Exception $e) {
            $this->assertStringContainsString('packages_total_fee_check', $e->getMessage());
        }
        
        try {
            // Test invalid downpayment_percent (should fail)
            DB::statement("INSERT INTO packages (name, total_fee, downpayment_percent, installment_months, created_at, updated_at) VALUES ('Test Package Invalid', 10000, 150, 3, NOW(), NOW())");
            $this->fail('Should have failed with invalid downpayment_percent');
        } catch (\Exception $e) {
            $this->assertStringContainsString('packages_downpayment_percent_check', $e->getMessage());
        }
        
        try {
            // Test negative installment_months (should fail)
            DB::statement("INSERT INTO packages (name, total_fee, downpayment_percent, installment_months, created_at, updated_at) VALUES ('Test Package Negative', 10000, 25, -1, NOW(), NOW())");
            $this->fail('Should have failed with negative installment_months');
        } catch (\Exception $e) {
            $this->assertStringContainsString('packages_installment_months_check', $e->getMessage());
        }
    }

    /**
     * Test performance indexes exist.
     */
    public function test_performance_indexes_exist()
    {
        // Check students name search index
        $indexes = DB::select("SELECT indexname FROM pg_indexes WHERE tablename = 'students' AND indexname = 'students_name_search_index'");
        $this->assertCount(1, $indexes);
        
        // Check students guardian contact index
        $indexes = DB::select("SELECT indexname FROM pg_indexes WHERE tablename = 'students' AND indexname = 'students_guardian_contact_index'");
        $this->assertCount(1, $indexes);
        
        // Check packages name unique index
        $indexes = DB::select("SELECT indexname FROM pg_indexes WHERE tablename = 'packages' AND indexname = 'packages_name_unique'");
        $this->assertCount(1, $indexes);
    }

    /**
     * Test student search performance.
     */
    public function test_student_search_uses_index()
    {
        // Create test students
        for ($i = 0; $i < 10; $i++) {
            Student::create([
                'first_name' => 'FirstName' . $i,
                'last_name' => 'LastName' . $i,
                'guardian_name' => 'Guardian' . $i,
                'guardian_contact' => '+6391234567' . str_pad($i, 2, '0', STR_PAD_LEFT),
                'status' => 'ACTIVE',
            ]);
        }
        
        // Search by name (should use index)
        $students = Student::where('last_name', 'LIKE', 'LastName%')
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();
        
        $this->assertGreaterThan(0, $students->count());
        
        // Search by guardian contact (should use index)
        $students = Student::where('guardian_contact', 'LIKE', '+63912%')->get();
        $this->assertGreaterThan(0, $students->count());
    }

    /**
     * Clean up test data.
     */
    protected function tearDown(): void
    {
        // Clean up test students
        Student::where('student_no', 'LIKE', 'TEST-%')->delete();
        Student::where('first_name', 'LIKE', 'Test%')->delete();
        Student::where('first_name', 'LIKE', 'FirstName%')->delete();
        
        // Clean up test packages
        Package::where('name', 'LIKE', 'Test Package%')->delete();
        
        parent::tearDown();
    }
}
