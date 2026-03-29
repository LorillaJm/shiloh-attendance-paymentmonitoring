<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Scopes\ParentPaymentScope;

class PaymentTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'enrollment_id',
        'payment_schedule_id',
        'amount',
        'type',
        'transaction_date',
        'payment_method',
        'reference_no',
        'remarks',
        'processed_by_user_id',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'transaction_date' => 'date',
        ];
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Apply global scope for parent users
        static::addGlobalScope(new ParentPaymentScope());

        // Clear cache when payment is created
        static::created(function ($payment) {
            \App\Services\DashboardCacheService::clearPaymentsSummary($payment->transaction_date);
        });

        // Clear cache when payment is updated
        static::updated(function ($payment) {
            \App\Services\DashboardCacheService::clearPaymentsSummary($payment->transaction_date);
            
            // If date changed, clear old month cache too
            if ($payment->wasChanged('transaction_date')) {
                $originalDate = \Carbon\Carbon::parse($payment->getOriginal('transaction_date'));
                \App\Services\DashboardCacheService::clearPaymentsSummary($originalDate);
            }
        });

        // Clear cache when payment is deleted
        static::deleted(function ($payment) {
            \App\Services\DashboardCacheService::clearPaymentsSummary($payment->transaction_date);
        });
    }

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class);
    }

    public function paymentSchedule(): BelongsTo
    {
        return $this->belongsTo(PaymentSchedule::class);
    }

    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by_user_id');
    }
}
