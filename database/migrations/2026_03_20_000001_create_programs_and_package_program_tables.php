<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Create programs table
        Schema::create('programs', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Create pivot table for package-program relationship
        Schema::create('package_program', function (Blueprint $table) {
            $table->id();
            $table->foreignId('package_id')->constrained()->onDelete('cascade');
            $table->foreignId('program_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            
            $table->unique(['package_id', 'program_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('package_program');
        Schema::dropIfExists('programs');
    }
};
