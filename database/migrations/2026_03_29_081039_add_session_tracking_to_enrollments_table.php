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
        Schema::table('enrollments', function (Blueprint $table) {
            $table->integer('total_sessions')->default(0)->after('status')
                ->comment('Total number of sessions included in the package');
            $table->integer('sessions_used')->default(0)->after('total_sessions')
                ->comment('Number of sessions already attended');
            
            // Add index for quick queries
            $table->index(['student_id', 'sessions_used']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            $table->dropIndex(['student_id', 'sessions_used']);
            $table->dropColumn(['total_sessions', 'sessions_used']);
        });
    }
};
