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
        Schema::table('attendance_records', function (Blueprint $table) {
            // Composite index for monthly summary queries
            // Improves performance when filtering by student_id and attendance_date
            $table->index(['student_id', 'attendance_date'], 'idx_student_attendance_date');
            
            // Index for filtering by encoder
            // Useful for queries like "show all attendance I encoded"
            $table->index('encoded_by_user_id', 'idx_encoded_by_user');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendance_records', function (Blueprint $table) {
            $table->dropIndex('idx_student_attendance_date');
            $table->dropIndex('idx_encoded_by_user');
        });
    }
};
