<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SessionType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'default_duration_minutes',
        'requires_monitoring',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'requires_monitoring' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function studentSchedules(): HasMany
    {
        return $this->hasMany(StudentSchedule::class);
    }

    public function sessionOccurrences(): HasMany
    {
        return $this->hasMany(SessionOccurrence::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
