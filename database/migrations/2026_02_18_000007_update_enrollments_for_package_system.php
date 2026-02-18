<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            $table->date('package_start_date')->nullable()->after('enrollment_date');
            $table->date('package_end_date')->nullable()->after('package_start_date'); // 3 months from start
            $table->integer('monthly_installments')->default(3)->after('package_end_date');
            $table->boolean('is_non_refundable')->default(true)->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            $table->dropColumn(['package_start_date', 'package_end_date', 'monthly_installments', 'is_non_refundable']);
        });
    }
};
