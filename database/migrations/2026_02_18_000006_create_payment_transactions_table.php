<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enrollment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('payment_schedule_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('amount', 10, 2);
            $table->enum('type', ['PAYMENT', 'ADJUSTMENT', 'REFUND'])->default('PAYMENT');
            $table->date('transaction_date');
            $table->string('payment_method')->nullable(); // Cash, Bank Transfer, etc.
            $table->string('reference_no')->nullable(); // Receipt or reference number
            $table->text('remarks')->nullable();
            $table->foreignId('processed_by_user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
            
            $table->index('enrollment_id');
            $table->index('payment_schedule_id');
            $table->index('transaction_date');
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_transactions');
    }
};
