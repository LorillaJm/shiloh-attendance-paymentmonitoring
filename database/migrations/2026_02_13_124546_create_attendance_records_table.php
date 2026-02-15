<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->date('attendance_date');
            $table->enum('status', ['PRESENT', 'ABSENT', 'LATE', 'EXCUSED'])->default('PRESENT');
            $table->text('remarks')->nullable();
            $table->foreignId('encoded_by_user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
            
            // Unique constraint: one attendance record per student per date
            $table->unique(['student_id', 'attendance_date']);
            
            // Indexes for performance
            $table->index('attendance_date');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_records');
    }
};
