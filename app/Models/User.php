<?php

namespace App\Models;

use App\Enums\UserRole;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
        ];
    }

    /**
     * Determine if the user can access the Filament panel.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        // Parents have limited access - only to their portal pages
        if ($this->isParent()) {
            return true;
        }

        // All other roles can access panel
        return in_array($this->role?->value, ['ADMIN', 'TEACHER', 'USER']);
    }

    /**
     * Check if user is an admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === UserRole::ADMIN || $this->role?->value === 'ADMIN';
    }

    /**
     * Check if user is a regular user.
     */
    public function isUser(): bool
    {
        return $this->role === UserRole::USER || $this->role?->value === 'USER';
    }

    /**
     * Get attendance records encoded by this user.
     */
    public function attendanceRecords()
    {
        return $this->hasMany(AttendanceRecord::class, 'encoded_by_user_id');
    }

    /**
     * Get guardian profile if user is a parent.
     */
    public function guardian()
    {
        return $this->hasOne(Guardian::class);
    }

    /**
     * Get assigned schedules if user is a teacher.
     */
    public function assignedSchedules()
    {
        return $this->hasMany(StudentSchedule::class, 'teacher_id');
    }

    /**
     * Get assigned session occurrences if user is a teacher.
     */
    public function assignedSessions()
    {
        return $this->hasMany(SessionOccurrence::class, 'teacher_id');
    }

    /**
     * Check if user is a teacher.
     */
    public function isTeacher(): bool
    {
        return $this->role === UserRole::TEACHER || $this->role?->value === 'TEACHER';
    }

    /**
     * Check if user is a parent.
     */
    public function isParent(): bool
    {
        return $this->role === UserRole::PARENT || $this->role?->value === 'PARENT';
    }
}
