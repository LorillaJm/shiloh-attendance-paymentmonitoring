<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('session_types', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // One-on-One, Group Play, Writing, Practice Speech
            $table->string('code')->unique(); // ONE_ON_ONE, GROUP_PLAY, etc.
            $table->text('description')->nullable();
            $table->integer('default_duration_minutes')->default(60);
            $table->boolean('requires_monitoring')->default(false); // For age 10 and below
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index('code');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('session_types');
    }
};
