<?php

namespace App\Enums;

enum UserRole: string
{
    case ADMIN = 'ADMIN';
    case TEACHER = 'TEACHER';
    case PARENT = 'PARENT';
    case USER = 'USER';

    public function label(): string
    {
        return match($this) {
            self::ADMIN => 'Administrator',
            self::TEACHER => 'Teacher',
            self::PARENT => 'Parent/Guardian',
            self::USER => 'User',
        };
    }

    public function isAdmin(): bool
    {
        return $this === self::ADMIN;
    }

    public function isTeacher(): bool
    {
        return $this === self::TEACHER;
    }

    public function isParent(): bool
    {
        return $this === self::PARENT;
    }

    public function isUser(): bool
    {
        return $this === self::USER;
    }
}
