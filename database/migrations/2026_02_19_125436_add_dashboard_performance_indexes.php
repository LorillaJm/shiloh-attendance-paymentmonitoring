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
        // Use raw SQL to create indexes only if they don't exist
        // This is safer for PostgreSQL
        
        // Payment Schedules indexes
        DB::statement('CREATE INDEX IF NOT EXISTS idx_status_due_date ON payment_schedules (status, due_date)');
        DB::statement('CREATE INDEX IF NOT EXISTS idx_status_paid_at ON payment_schedules (status, paid_at)');
        DB::statement('CREATE INDEX IF NOT EXISTS idx_enrollment_id ON payment_schedules (enrollment_id)');
        
        // Students indexes
        DB::statement('CREATE INDEX IF NOT EXISTS idx_status ON students (status)');
        
        // Enrollments indexes
        DB::statement('CREATE INDEX IF NOT EXISTS idx_status_balance ON enrollments (status, remaining_balance)');
        DB::statement('CREATE INDEX IF NOT EXISTS idx_student_id_enrollments ON enrollments (student_id)');
        
        // Attendance Records indexes
        DB::statement('CREATE INDEX IF NOT EXISTS idx_date_status ON attendance_records (attendance_date, status)');
        DB::statement('CREATE INDEX IF NOT EXISTS idx_student_id_attendance ON attendance_records (student_id)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop indexes if they exist
        DB::statement('DROP INDEX IF EXISTS idx_status_due_date');
        DB::statement('DROP INDEX IF EXISTS idx_status_paid_at');
        DB::statement('DROP INDEX IF EXISTS idx_enrollment_id');
        DB::statement('DROP INDEX IF EXISTS idx_status');
        DB::statement('DROP INDEX IF EXISTS idx_status_balance');
        DB::statement('DROP INDEX IF EXISTS idx_student_id_enrollments');
        DB::statement('DROP INDEX IF EXISTS idx_date_status');
        DB::statement('DROP INDEX IF EXISTS idx_student_id_attendance');
    }
};
