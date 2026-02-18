<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // For PostgreSQL, we need to alter the enum type
        if (DB::getDriverName() === 'pgsql') {
            DB::statement("ALTER TABLE users DROP CONSTRAINT IF EXISTS users_role_check");
            DB::statement("ALTER TABLE users ADD CONSTRAINT users_role_check CHECK (role IN ('ADMIN', 'USER', 'TEACHER', 'PARENT'))");
        } else {
            // For MySQL
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('ADMIN', 'USER', 'TEACHER', 'PARENT') DEFAULT 'USER'");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            DB::statement("ALTER TABLE users DROP CONSTRAINT IF EXISTS users_role_check");
            DB::statement("ALTER TABLE users ADD CONSTRAINT users_role_check CHECK (role IN ('ADMIN', 'USER'))");
        } else {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('ADMIN', 'USER') DEFAULT 'USER'");
        }
    }
};
