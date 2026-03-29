<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('message');
            $table->enum('target_audience', ['all', 'parents', 'admins', 'specific_user'])->default('all');
            $table->foreignId('target_user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->boolean('is_published')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            
            $table->index(['target_audience', 'is_published']);
            $table->index('published_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('announcements');
    }
};
