<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Update existing TEACHER and USER roles to ADMIN
        // This preserves admin functionality for existing non-parent users
        DB::table('users')
            ->whereIn('role', ['TEACHER', 'USER'])
            ->update(['role' => 'ADMIN']);

        // Update the role constraint to only allow ADMIN and PARENT
        if (DB::getDriverName() === 'pgsql') {
            DB::statement("ALTER TABLE users DROP CONSTRAINT IF EXISTS users_role_check");
            DB::statement("ALTER TABLE users ADD CONSTRAINT users_role_check CHECK (role IN ('ADMIN', 'PARENT'))");
        } else {
            // For MySQL
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('ADMIN', 'PARENT') DEFAULT 'ADMIN'");
        }
    }

    public function down(): void
    {
        // Restore the previous role enum
        if (DB::getDriverName() === 'pgsql') {
            DB::statement("ALTER TABLE users DROP CONSTRAINT IF EXISTS users_role_check");
            DB::statement("ALTER TABLE users ADD CONSTRAINT users_role_check CHECK (role IN ('ADMIN', 'USER', 'TEACHER', 'PARENT'))");
        } else {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('ADMIN', 'USER', 'TEACHER', 'PARENT') DEFAULT 'USER'");
        }
    }
};
