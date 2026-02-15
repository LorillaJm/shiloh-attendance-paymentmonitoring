<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Payment Schedules - Critical for dashboard queries
        Schema::table('payment_schedules', function (Blueprint $table) {
            // Individual indexes
            if (!$this->indexExists('payment_schedules', 'payment_schedules_enrollment_id_index')) {
                $table->index('enrollment_id');
            }
            if (!$this->indexExists('payment_schedules', 'payment_schedules_status_index')) {
                $table->index('status');
            }
            if (!$this->indexExists('payment_schedules', 'payment_schedules_due_date_index')) {
                $table->index('due_date');
            }
            if (!$this->indexExists('payment_schedules', 'payment_schedules_paid_at_index')) {
                $table->index('paid_at');
            }
            
            // Composite indexes for common queries
            if (!$this->indexExists('payment_schedules', 'payment_schedules_status_due_date_index')) {
                $table->index(['status', 'due_date'], 'payment_schedules_status_due_date_index');
            }
            if (!$this->indexExists('payment_schedules', 'payment_schedules_status_paid_at_index')) {
                $table->index(['status', 'paid_at'], 'payment_schedules_status_paid_at_index');
            }
            if (!$this->indexExists('payment_schedules', 'payment_schedules_enrollment_status_index')) {
                $table->index(['enrollment_id', 'status'], 'payment_schedules_enrollment_status_index');
            }
        });

        // Students - For lookups and filters
        Schema::table('students', function (Blueprint $table) {
            if (!$this->indexExists('students', 'students_status_index')) {
                $table->index('status');
            }
            if (!$this->indexExists('students', 'students_student_no_index')) {
                $table->index('student_no');
            }
            if (!$this->indexExists('students', 'students_last_name_index')) {
                $table->index('last_name');
            }
        });

        // Enrollments - For joins and filters
        Schema::table('enrollments', function (Blueprint $table) {
            if (!$this->indexExists('enrollments', 'enrollments_student_id_index')) {
                $table->index('student_id');
            }
            if (!$this->indexExists('enrollments', 'enrollments_package_id_index')) {
                $table->index('package_id');
            }
            if (!$this->indexExists('enrollments', 'enrollments_status_index')) {
                $table->index('status');
            }
            if (!$this->indexExists('enrollments', 'enrollments_enrollment_date_index')) {
                $table->index('enrollment_date');
            }
            if (!$this->indexExists('enrollments', 'enrollments_status_remaining_balance_index')) {
                $table->index(['status', 'remaining_balance'], 'enrollments_status_remaining_balance_index');
            }
        });

        // Attendance Records - For date range queries
        Schema::table('attendance_records', function (Blueprint $table) {
            if (!$this->indexExists('attendance_records', 'attendance_records_student_id_index')) {
                $table->index('student_id');
            }
            if (!$this->indexExists('attendance_records', 'attendance_records_attendance_date_index')) {
                $table->index('attendance_date');
            }
            if (!$this->indexExists('attendance_records', 'attendance_records_status_index')) {
                $table->index('status');
            }
            if (!$this->indexExists('attendance_records', 'attendance_records_encoded_by_user_id_index')) {
                $table->index('encoded_by_user_id');
            }
            
            // Composite indexes
            if (!$this->indexExists('attendance_records', 'attendance_records_date_status_index')) {
                $table->index(['attendance_date', 'status'], 'attendance_records_date_status_index');
            }
            if (!$this->indexExists('attendance_records', 'attendance_records_student_date_index')) {
                $table->index(['student_id', 'attendance_date'], 'attendance_records_student_date_index');
            }
            
            // Unique constraint to prevent duplicates
            if (!$this->indexExists('attendance_records', 'attendance_records_student_date_unique')) {
                $table->unique(['student_id', 'attendance_date'], 'attendance_records_student_date_unique');
            }
        });

        // Packages - For lookups
        Schema::table('packages', function (Blueprint $table) {
            if (!$this->indexExists('packages', 'packages_name_index')) {
                $table->index('name');
            }
        });
    }

    public function down(): void
    {
        Schema::table('payment_schedules', function (Blueprint $table) {
            $table->dropIndex(['enrollment_id']);
            $table->dropIndex(['status']);
            $table->dropIndex(['due_date']);
            $table->dropIndex(['paid_at']);
            $table->dropIndex('payment_schedules_status_due_date_index');
            $table->dropIndex('payment_schedules_status_paid_at_index');
            $table->dropIndex('payment_schedules_enrollment_status_index');
        });

        Schema::table('students', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['student_no']);
            $table->dropIndex(['last_name']);
        });

        Schema::table('enrollments', function (Blueprint $table) {
            $table->dropIndex(['student_id']);
            $table->dropIndex(['package_id']);
            $table->dropIndex(['status']);
            $table->dropIndex(['enrollment_date']);
            $table->dropIndex('enrollments_status_remaining_balance_index');
        });

        Schema::table('attendance_records', function (Blueprint $table) {
            $table->dropIndex(['student_id']);
            $table->dropIndex(['attendance_date']);
            $table->dropIndex(['status']);
            $table->dropIndex(['encoded_by_user_id']);
            $table->dropIndex('attendance_records_date_status_index');
            $table->dropIndex('attendance_records_student_date_index');
            $table->dropUnique('attendance_records_student_date_unique');
        });

        Schema::table('packages', function (Blueprint $table) {
            $table->dropIndex(['name']);
        });
    }

    private function indexExists(string $table, string $index): bool
    {
        try {
            // For PostgreSQL
            $result = DB::select("
                SELECT 1 
                FROM pg_indexes 
                WHERE tablename = ? AND indexname = ?
            ", [$table, $index]);
            
            return count($result) > 0;
        } catch (\Exception $e) {
            return false;
        }
    }
};
