<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'total_fee',
        'downpayment_percent',
        'installment_months',
        'description',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'total_fee' => 'decimal:2',
            'downpayment_percent' => 'decimal:2',
            'installment_months' => 'integer',
        ];
    }

    /**
     * Calculate downpayment amount.
     */
    public function getDownpaymentAmountAttribute(): float
    {
        return ($this->total_fee * $this->downpayment_percent) / 100;
    }

    /**
     * Calculate monthly installment amount.
     */
    public function getMonthlyInstallmentAttribute(): float
    {
        $balance = $this->total_fee - $this->downpayment_amount;
        return $this->installment_months > 0 ? $balance / $this->installment_months : 0;
    }

    /**
     * Get the enrollments for the package.
     */
    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }
}
