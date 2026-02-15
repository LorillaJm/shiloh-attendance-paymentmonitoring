<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        // Insert default settings
        DB::table('system_settings')->insert([
            ['key' => 'school_name', 'value' => 'Shiloh Christian School', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'school_address', 'value' => '', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'school_phone', 'value' => '', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'school_email', 'value' => '', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'school_logo', 'value' => '', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('system_settings');
    }
};
