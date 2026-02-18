<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StudentSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'session_type_id',
        'teacher_id',
        'recurrence_type',
        'recurrence_days',
        'start_time',
        'end_time',
        'effective_from',
        'effective_until',
        'is_active',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'recurrence_days' => 'array',
            'start_time' => 'datetime:H:i',
            'end_time' => 'datetime:H:i',
            'effective_from' => 'date',
            'effective_until' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function sessionType(): BelongsTo
    {
        return $this->belongsTo(SessionType::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
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
