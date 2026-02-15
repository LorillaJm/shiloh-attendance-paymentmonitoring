<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add composite index for student name searches
        Schema::table('students', function (Blueprint $table) {
            // Check if index doesn't exist before creating
            if (!$this->indexExists('students', 'students_name_search_index')) {
                $table->index(['last_name', 'first_name'], 'students_name_search_index');
            }
            if (!$this->indexExists('students', 'students_guardian_contact_index')) {
                $table->index('guardian_contact', 'students_guardian_contact_index');
            }
        });

        // Add CHECK constraints for students table (if not exists)
        $this->addConstraintIfNotExists('students', 'students_sex_check', 
            "ALTER TABLE students ADD CONSTRAINT students_sex_check CHECK (sex IN ('Male', 'Female') OR sex IS NULL)");
        
        $this->addConstraintIfNotExists('students', 'students_status_check',
            "ALTER TABLE students ADD CONSTRAINT students_status_check CHECK (status IN ('ACTIVE', 'INACTIVE', 'DROPPED'))");
        
        $this->addConstraintIfNotExists('students', 'students_birthdate_check',
            "ALTER TABLE students ADD CONSTRAINT students_birthdate_check CHECK (birthdate IS NULL OR birthdate < CURRENT_DATE)");
        
        // Add CHECK constraints for packages table (if not exists)
        $this->addConstraintIfNotExists('packages', 'packages_total_fee_check',
            "ALTER TABLE packages ADD CONSTRAINT packages_total_fee_check CHECK (total_fee > 0)");
        
        $this->addConstraintIfNotExists('packages', 'packages_downpayment_percent_check',
            "ALTER TABLE packages ADD CONSTRAINT packages_downpayment_percent_check CHECK (downpayment_percent >= 0 AND downpayment_percent <= 100)");
        
        $this->addConstraintIfNotExists('packages', 'packages_installment_months_check',
            "ALTER TABLE packages ADD CONSTRAINT packages_installment_months_check CHECK (installment_months >= 0)");
    }

    /**
     * Check if constraint exists.
     */
    private function constraintExists(string $table, string $constraint): bool
    {
        $result = DB::select("
            SELECT constraint_name 
            FROM information_schema.table_constraints 
            WHERE table_name = ? AND constraint_name = ?
        ", [$table, $constraint]);
        
        return count($result) > 0;
    }

    /**
     * Add constraint if it doesn't exist.
     */
    private function addConstraintIfNotExists(string $table, string $constraint, string $sql): void
    {
        if (!$this->constraintExists($table, $constraint)) {
            DB::statement($sql);
        }
    }

    /**
     * Check if index exists.
     */
    private function indexExists(string $table, string $index): bool
    {
        $result = DB::select("
            SELECT indexname 
            FROM pg_indexes 
            WHERE tablename = ? AND indexname = ?
        ", [$table, $index]);
        
        return count($result) > 0;
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop CHECK constraints
        DB::statement("ALTER TABLE students DROP CONSTRAINT IF EXISTS students_sex_check");
        DB::statement("ALTER TABLE students DROP CONSTRAINT IF EXISTS students_status_check");
        DB::statement("ALTER TABLE students DROP CONSTRAINT IF EXISTS students_birthdate_check");
        
        DB::statement("ALTER TABLE packages DROP CONSTRAINT IF EXISTS packages_total_fee_check");
        DB::statement("ALTER TABLE packages DROP CONSTRAINT IF EXISTS packages_downpayment_percent_check");
        DB::statement("ALTER TABLE packages DROP CONSTRAINT IF EXISTS packages_installment_months_check");
        
        // Drop indexes
        Schema::table('students', function (Blueprint $table) {
            $table->dropIndex('students_name_search_index');
            $table->dropIndex('students_guardian_contact_index');
        });
    }
};
