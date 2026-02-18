<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('session_occurrences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_schedule_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('session_type_id')->constrained()->cascadeOnDelete();
            $table->foreignId('teacher_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('session_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->enum('status', ['SCHEDULED', 'COMPLETED', 'CANCELLED', 'NO_SHOW'])->default('SCHEDULED');
            $table->text('notes')->nullable();
            $table->text('monitoring_notes')->nullable(); // For age 10 and below
            $table->timestamps();
            
            $table->index('student_id');
            $table->index('session_date');
            $table->index('teacher_id');
            $table->index('status');
            $table->index(['student_id', 'session_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('session_occurrences');
    }
};
