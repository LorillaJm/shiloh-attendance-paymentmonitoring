<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Enrollment extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'package_id',
        'enrollment_date',
        'total_fee',
        'downpayment_percent',
        'downpayment_amount',
        'remaining_balance',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'enrollment_date' => 'date',
            'total_fee' => 'decimal:2',
            'downpayment_percent' => 'decimal:2',
            'downpayment_amount' => 'decimal:2',
            'remaining_balance' => 'decimal:2',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    public function paymentSchedules(): HasMany
    {
        return $this->hasMany(PaymentSchedule::class);
    }

    public function paymentTransactions(): HasMany
    {
        return $this->hasMany(PaymentTransaction::class);
    }

    /**
     * Get total amount paid from transactions ledger.
     */
    public function getTotalPaidAttribute(): float
    {
        return $this->paymentTransactions()
            ->where('type', 'PAYMENT')
            ->sum('amount');
    }

    /**
     * Get remaining balance (unpaid amount).
     */
    public function getRemainingBalanceComputedAttribute(): float
    {
        return $this->total_fee - $this->total_paid;
    }

    /**
     * Get count of paid schedules.
     */
    public function getPaidCountAttribute(): int
    {
        return $this->paymentSchedules()
            ->where('status', 'PAID')
            ->count();
    }

    /**
     * Get count of unpaid schedules.
     */
    public function getUnpaidCountAttribute(): int
    {
        return $this->paymentSchedules()
            ->where('status', 'UNPAID')
            ->count();
    }

    /**
     * Get count of overdue schedules.
     */
    public function getOverdueCountAttribute(): int
    {
        return $this->paymentSchedules()
            ->where('status', 'OVERDUE')
            ->count();
    }
}
