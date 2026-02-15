<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * This migration adds critical indexes identified during performance audit.
     * These indexes significantly improve query performance for:
     * - Enrollment lookups by student/package
     * - Payment schedule filtering by enrollment/status/due_date
     * - Student searches by last_name
     */
    public function up(): void
    {
        // ENROLLMENTS TABLE - Critical for enrollment queries
        Schema::table('enrollments', function (Blueprint $table) {
            // Foreign key indexes for joins (if not already indexed by FK)
            if (!$this->indexExists('enrollments', 'idx_enrollments_student_id')) {
                $table->index('student_id', 'idx_enrollments_student_id');
            }
            if (!$this->indexExists('enrollments', 'idx_enrollments_package_id')) {
                $table->index('package_id', 'idx_enrollments_package_id');
            }
            
            // Enrollment date for date range queries
            if (!$this->indexExists('enrollments', 'idx_enrollments_enrollment_date')) {
                $table->index('enrollment_date', 'idx_enrollments_enrollment_date');
            }
            
            // Status for filtering active enrollments
            if (!$this->indexExists('enrollments', 'idx_enrollments_status')) {
                $table->index('status', 'idx_enrollments_status');
            }
            
            // Composite index for common query pattern: student + status
            if (!$this->indexExists('enrollments', 'idx_enrollments_student_status')) {
                $table->index(['student_id', 'status'], 'idx_enrollments_student_status');
            }
        });

        // PAYMENT_SCHEDULES TABLE - Critical for payment reports
        Schema::table('payment_schedules', function (Blueprint $table) {
            // Foreign key for enrollment joins
            if (!$this->indexExists('payment_schedules', 'idx_payment_schedules_enrollment_id')) {
                $table->index('enrollment_id', 'idx_payment_schedules_enrollment_id');
            }
            
            // Due date for overdue/due queries
            if (!$this->indexExists('payment_schedules', 'idx_payment_schedules_due_date')) {
                $table->index('due_date', 'idx_payment_schedules_due_date');
            }
            
            // Status for filtering unpaid/paid
            if (!$this->indexExists('payment_schedules', 'idx_payment_schedules_status')) {
                $table->index('status', 'idx_payment_schedules_status');
            }
            
            // Composite index for most common query: status + due_date (overdue queries)
            if (!$this->indexExists('payment_schedules', 'idx_payment_schedules_status_due_date')) {
                $table->index(['status', 'due_date'], 'idx_payment_schedules_status_due_date');
            }
            
            // Composite index for enrollment + status (ledger queries)
            if (!$this->indexExists('payment_schedules', 'idx_payment_schedules_enrollment_status')) {
                $table->index(['enrollment_id', 'status'], 'idx_payment_schedules_enrollment_status');
            }
        });

        // STUDENTS TABLE - Additional index for last_name searches
        Schema::table('students', function (Blueprint $table) {
            // Last name index for sorting and searching
            if (!$this->indexExists('students', 'idx_students_last_name')) {
                $table->index('last_name', 'idx_students_last_name');
            }
        });

        // ATTENDANCE_RECORDS TABLE - Additional index for date queries
        Schema::table('attendance_records', function (Blueprint $table) {
            // Attendance date index for date range queries (if not exists)
            if (!$this->indexExists('attendance_records', 'idx_attendance_records_attendance_date')) {
                $table->index('attendance_date', 'idx_attendance_records_attendance_date');
            }
            
            // Status index for filtering by attendance status
            if (!$this->indexExists('attendance_records', 'idx_attendance_records_status')) {
                $table->index('status', 'idx_attendance_records_status');
            }
        });
    }

    /**
     * Check if index exists in PostgreSQL.
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
        Schema::table('enrollments', function (Blueprint $table) {
            $table->dropIndex('idx_enrollments_student_id');
            $table->dropIndex('idx_enrollments_package_id');
            $table->dropIndex('idx_enrollments_enrollment_date');
            $table->dropIndex('idx_enrollments_status');
            $table->dropIndex('idx_enrollments_student_status');
        });

        Schema::table('payment_schedules', function (Blueprint $table) {
            $table->dropIndex('idx_payment_schedules_enrollment_id');
            $table->dropIndex('idx_payment_schedules_due_date');
            $table->dropIndex('idx_payment_schedules_status');
            $table->dropIndex('idx_payment_schedules_status_due_date');
            $table->dropIndex('idx_payment_schedules_enrollment_status');
        });

        Schema::table('students', function (Blueprint $table) {
            $table->dropIndex('idx_students_last_name');
        });

        Schema::table('attendance_records', function (Blueprint $table) {
            $table->dropIndex('idx_attendance_records_attendance_date');
            $table->dropIndex('idx_attendance_records_status');
        });
    }
};
