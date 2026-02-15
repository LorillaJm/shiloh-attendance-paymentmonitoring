<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'enrollment_id',
        'installment_no',
        'due_date',
        'amount_due',
        'status',
        'paid_at',
        'payment_method',
        'receipt_no',
        'remarks',
    ];

    protected function casts(): array
    {
        return [
            'due_date' => 'date',
            'amount_due' => 'decimal:2',
            'paid_at' => 'datetime',
        ];
    }

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class);
    }

    /**
     * Scope to get overdue schedules (dynamically computed).
     */
    public function scopeOverdue($query)
    {
        return $query->where('status', 'UNPAID')
            ->whereNotNull('due_date')
            ->where('due_date', '<', now()->format('Y-m-d'));
    }

    /**
     * Scope to get due schedules (upcoming 15th).
     */
    public function scopeDueOnNext15th($query)
    {
        $today = now();
        $next15th = $today->copy();
        
        // If today is before or on 15th, next 15th is this month
        if ($today->day <= 15) {
            $next15th->day(15);
        } else {
            // Otherwise, next 15th is next month
            $next15th->addMonth()->day(15);
        }

        return $query->where('status', 'UNPAID')
            ->whereDate('due_date', $next15th->format('Y-m-d'));
    }

    /**
     * Check if this schedule is overdue (computed).
     */
    public function getIsOverdueAttribute(): bool
    {
        if ($this->status === 'PAID') {
            return false;
        }

        if (!$this->due_date) {
            return false;
        }

        return $this->due_date->isPast();
    }

    /**
     * Get computed status (includes dynamic overdue check).
     */
    public function getComputedStatusAttribute(): string
    {
        if ($this->status === 'PAID') {
            return 'PAID';
        }

        if ($this->is_overdue) {
            return 'OVERDUE';
        }

        return 'UNPAID';
    }
}
