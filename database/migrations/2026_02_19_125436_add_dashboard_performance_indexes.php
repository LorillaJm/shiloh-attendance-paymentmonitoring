<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('payment_schedules', function (Blueprint $table) {
            // Critical indexes for dashboard queries
            $table->index(['status', 'due_date'], 'idx_status_due_date');
            $table->index(['status', 'paid_at'], 'idx_status_paid_at');
            $table->index('enrollment_id', 'idx_enrollment_id');
        });

        Schema::table('students', function (Blueprint $table) {
            $table->index('status', 'idx_status');
        });

        Schema::table('enrollments', function (Blueprint $table) {
            $table->index(['status', 'remaining_balance'], 'idx_status_balance');
            $table->index('student_id', 'idx_student_id');
        });

        Schema::table('attendance_records', function (Blueprint $table) {
            $table->index(['attendance_date', 'status'], 'idx_date_status');
            $table->index('student_id', 'idx_student_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_schedules', function (Blueprint $table) {
            $table->dropIndex('idx_status_due_date');
            $table->dropIndex('idx_status_paid_at');
            $table->dropIndex('idx_enrollment_id');
        });

        Schema::table('students', function (Blueprint $table) {
            $table->dropIndex('idx_status');
        });

        Schema::table('enrollments', function (Blueprint $table) {
            $table->dropIndex('idx_status_balance');
            $table->dropIndex('idx_student_id');
        });

        Schema::table('attendance_records', function (Blueprint $table) {
            $table->dropIndex('idx_date_status');
            $table->dropIndex('idx_student_id');
        });
    }
};
