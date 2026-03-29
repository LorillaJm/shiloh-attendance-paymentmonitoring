<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add theme column
        Schema::table('users', function (Blueprint $table) {
            $table->string('theme', 20)->default('light')->after('role');
        });

        // Update the role enum to include SUPERADMIN
        if (DB::getDriverName() === 'pgsql') {
            // PostgreSQL: Drop and recreate constraint
            DB::statement("ALTER TABLE users DROP CONSTRAINT IF EXISTS users_role_check");
            DB::statement("ALTER TABLE users ADD CONSTRAINT users_role_check CHECK (role IN ('SUPERADMIN', 'ADMIN', 'PARENT'))");
        } else {
            // MySQL: Modify enum column
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('SUPERADMIN', 'ADMIN', 'PARENT') DEFAULT 'ADMIN'");
        }

        // Add index on role column for performance
        Schema::table('users', function (Blueprint $table) {
            $table->index('role');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove theme column
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('theme');
            $table->dropIndex(['role']);
        });

        // Revert role enum to previous state
        // Convert any SUPERADMIN users to ADMIN before reverting
        DB::table('users')
            ->where('role', 'SUPERADMIN')
            ->update(['role' => 'ADMIN']);

        if (DB::getDriverName() === 'pgsql') {
            DB::statement("ALTER TABLE users DROP CONSTRAINT IF EXISTS users_role_check");
            DB::statement("ALTER TABLE users ADD CONSTRAINT users_role_check CHECK (role IN ('ADMIN', 'PARENT'))");
        } else {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('ADMIN', 'PARENT') DEFAULT 'ADMIN'");
        }
    }
};
