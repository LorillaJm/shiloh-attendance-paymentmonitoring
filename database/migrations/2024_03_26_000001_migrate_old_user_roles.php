<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Migrate old role values to new enum values
        // TEACHER -> ADMIN (teachers are now managed separately in teachers table)
        // USER -> PARENT (generic users become parents)
        // Keep ADMIN and PARENT as is
        // SUPERADMIN already exists in new enum
        
        DB::table('users')
            ->where('role', 'TEACHER')
            ->update(['role' => 'ADMIN']);
            
        DB::table('users')
            ->where('role', 'USER')
            ->update(['role' => 'PARENT']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverse the migration if needed
        DB::table('users')
            ->where('role', 'ADMIN')
            ->whereNotNull('email')
            ->update(['role' => 'TEACHER']);
            
        DB::table('users')
            ->where('role', 'PARENT')
            ->update(['role' => 'USER']);
    }
};
