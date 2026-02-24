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
        // Composite indexes for dashboard queries
        Schema::table('payment_schedules', function (Blueprint $table) {
            // For collections trend queries
            if (!$this->indexExists('payment_schedules', 'idx_ps_status_paid_at')) {
                $table->index(['status', 'paid_at'], 'idx_ps_status_paid_at');
            }
            
            // For due/overdue queries
            if (!$this->indexExists('payment_schedules', 'idx_ps_status_due_date')) {
                $table->index(['status', 'due_date'], 'idx_ps_status_due_date');
            }
        });

        Schema::table('enrollments', function (Blueprint $table) {
            // For active enrollments with balance queries
            if (!$this->indexExists('enrollments', 'idx_enrollments_status_balance')) {
                $table->index(['status', 'remaining_balance'], 'idx_enrollments_status_balance');
            }
        });

        Schema::table('attendance_records', function (Blueprint $table) {
            // For attendance summary queries
            if (!$this->indexExists('attendance_records', 'idx_attendance_date_status')) {
                $table->index(['attendance_date', 'status'], 'idx_attendance_date_status');
            }
        });

        Schema::table('students', function (Blueprint $table) {
            // Ensure status index exists
            if (!$this->indexExists('students', 'idx_students_status')) {
                $table->index('status', 'idx_students_status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_schedules', function (Blueprint $table) {
            $table->dropIndex('idx_ps_status_paid_at');
            $table->dropIndex('idx_ps_status_due_date');
        });

        Schema::table('enrollments', function (Blueprint $table) {
            $table->dropIndex('idx_enrollments_status_balance');
        });

        Schema::table('attendance_records', function (Blueprint $table) {
            $table->dropIndex('idx_attendance_date_status');
        });
    }

    /**
     * Check if an index exists using raw SQL
     */
    private function indexExists(string $table, string $index): bool
    {
        $result = DB::select("
            SELECT 1 
            FROM pg_indexes 
            WHERE tablename = ? 
            AND indexname = ?
        ", [$table, $index]);
        
        return count($result) > 0;
    }
};
