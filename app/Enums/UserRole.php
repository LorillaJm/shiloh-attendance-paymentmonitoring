<?php

namespace App\Enums;

enum UserRole: string
{
    case SUPERADMIN = 'SUPERADMIN';
    case ADMIN = 'ADMIN';
    case PARENT = 'PARENT';

    public function label(): string
    {
        return match($this) {
            self::SUPERADMIN => 'Super Administrator',
            self::ADMIN => 'Administrator',
            self::PARENT => 'Parent/Guardian',
        };
    }

    public function isAdmin(): bool
    {
        return $this === self::ADMIN;
    }

    public function isParent(): bool
    {
        return $this === self::PARENT;
    }

    /**
     * Check if this role can manage users.
     *
     * @return bool
     */
    public function canManageUsers(): bool
    {
        return $this === self::SUPERADMIN;
    }

    /**
     * Check if this role can manage students.
     *
     * @return bool
     */
    public function canManageStudents(): bool
    {
        return in_array($this, [self::SUPERADMIN, self::ADMIN]);
    }

    /**
     * Check if this role is read-only.
     *
     * @return bool
     */
    public function isReadOnly(): bool
    {
        return $this === self::PARENT;
    }
}
