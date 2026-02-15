<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Services\StudentNumberGenerator;

class Student extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'student_no',
        'first_name',
        'last_name',
        'middle_name',
        'birthdate',
        'sex',
        'address',
        'guardian_name',
        'guardian_contact',
        'status',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'birthdate' => 'date',
        ];
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($student) {
            if (empty($student->student_no)) {
                $student->student_no = StudentNumberGenerator::generate();
            }
        });
    }

    /**
     * Get the student's full name.
     */
    public function getFullNameAttribute(): string
    {
        $parts = array_filter([
            $this->first_name,
            $this->middle_name,
            $this->last_name,
        ]);
        
        return implode(' ', $parts);
    }

    /**
     * Scope a query to only include active students.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'ACTIVE');
    }

    /**
     * Scope a query to only include inactive students.
     */
    public function scopeInactive($query)
    {
        return $query->where('status', 'INACTIVE');
    }

    /**
     * Scope a query to only include dropped students.
     */
    public function scopeDropped($query)
    {
        return $query->where('status', 'DROPPED');
    }

    /**
     * Get the enrollments for the student.
     */
    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    /**
     * Get the attendance records for the student.
     */
    public function attendanceRecords()
    {
        return $this->hasMany(AttendanceRecord::class);
    }
}
