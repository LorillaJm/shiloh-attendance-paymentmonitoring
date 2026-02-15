<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class AttendanceRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'attendance_date',
        'status',
        'remarks',
        'encoded_by_user_id',
    ];

    protected function casts(): array
    {
        return [
            'attendance_date' => 'date',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function encodedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'encoded_by_user_id');
    }

    /**
     * Check if this record can be edited (within 7 days).
     */
    public function canBeEdited(): bool
    {
        $editWindowDays = config('attendance.edit_window_days', 7);
        
        if ($editWindowDays === null) {
            return true; // No restriction
        }

        $daysSinceAttendance = Carbon::parse($this->attendance_date)->diffInDays(now(), false);
        
        return $daysSinceAttendance <= $editWindowDays;
    }

    /**
     * Scope to filter by date range.
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('attendance_date', [$startDate, $endDate]);
    }

    /**
     * Scope to filter by month.
     */
    public function scopeMonth($query, $year, $month)
    {
        return $query->whereYear('attendance_date', $year)
            ->whereMonth('attendance_date', $month);
    }

    /**
     * Scope to filter by status.
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }
}
