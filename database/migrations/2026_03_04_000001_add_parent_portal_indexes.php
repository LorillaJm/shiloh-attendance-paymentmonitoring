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
        // Add indexes using raw SQL to avoid duplicate index errors
        try {
            // Index on guardian_student for parent queries
            DB::statement('CREATE INDEX IF NOT EXISTS guardian_student_guardian_id_idx ON guardian_student(guardian_id)');
            DB::statement('CREATE INDEX IF NOT EXISTS guardian_student_student_id_idx ON guardian_student(student_id)');
            
            // Index on attendance_records for parent queries
            DB::statement('CREATE INDEX IF NOT EXISTS attendance_records_student_date_idx ON attendance_records(student_id, attendance_date)');
            
            // Index on payment_transactions for parent queries
            DB::statement('CREATE INDEX IF NOT EXISTS payment_transactions_enrollment_date_idx ON payment_transactions(enrollment_id, transaction_date)');
            
            // Index on session_occurrences for parent queries
            DB::statement('CREATE INDEX IF NOT EXISTS session_occurrences_student_date_idx ON session_occurrences(student_id, session_date)');
        } catch (\Exception $e) {
            // Indexes may already exist, that's okay
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop indexes if they exist
        try {
            DB::statement('DROP INDEX IF EXISTS session_occurrences_student_date_idx');
            DB::statement('DROP INDEX IF EXISTS payment_transactions_enrollment_date_idx');
            DB::statement('DROP INDEX IF EXISTS attendance_records_student_date_idx');
            DB::statement('DROP INDEX IF EXISTS guardian_student_student_id_idx');
            DB::statement('DROP INDEX IF EXISTS guardian_student_guardian_id_idx');
        } catch (\Exception $e) {
            // Ignore errors if indexes don't exist
        }
    }
};
