<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('session_type_id')->constrained()->cascadeOnDelete();
            $table->foreignId('teacher_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('recurrence_type', ['DAILY', 'WEEKLY', 'CUSTOM'])->default('WEEKLY');
            $table->json('recurrence_days')->nullable(); // [1,3,5] for Mon, Wed, Fri
            $table->time('start_time');
            $table->time('end_time');
            $table->date('effective_from');
            $table->date('effective_until')->nullable();
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index('student_id');
            $table->index('teacher_id');
            $table->index(['effective_from', 'effective_until']);
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_schedules');
    }
};
