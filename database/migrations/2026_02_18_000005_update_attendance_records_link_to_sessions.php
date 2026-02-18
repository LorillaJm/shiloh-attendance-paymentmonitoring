<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendance_records', function (Blueprint $table) {
            $table->foreignId('session_occurrence_id')->nullable()->after('student_id')->constrained()->nullOnDelete();
            $table->index('session_occurrence_id');
        });
    }

    public function down(): void
    {
        Schema::table('attendance_records', function (Blueprint $table) {
            $table->dropForeign(['session_occurrence_id']);
            $table->dropIndex(['session_occurrence_id']);
            $table->dropColumn('session_occurrence_id');
        });
    }
};
