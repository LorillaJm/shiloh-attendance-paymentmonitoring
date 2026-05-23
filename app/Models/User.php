<?php

namespace App\Models;

use App\Enums\UserRole;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use NotificationChannels\WebPush\HasPushSubscriptions;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable, HasPushSubscriptions;

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
        'theme',
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
        // SUPERADMIN, ADMIN and PARENT roles can access panels
        return $this->isSuperadmin() || $this->isAdmin() || $this->isParent();
    }

    /**
     * Check if user is a superadmin.
     */
    public function isSuperadmin(): bool
    {
        return $this->role === UserRole::SUPERADMIN || $this->role?->value === 'SUPERADMIN';
    }

    /**
     * Check if user is an admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === UserRole::ADMIN || $this->role?->value === 'ADMIN';
    }

    /**
     * Check if user is a parent.
     */
    public function isParent(): bool
    {
        return $this->role === UserRole::PARENT || $this->role?->value === 'PARENT';
    }

    /**
     * Check if user can manage other users.
     */
    public function canManageUsers(): bool
    {
        return $this->role?->canManageUsers() ?? false;
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
}
