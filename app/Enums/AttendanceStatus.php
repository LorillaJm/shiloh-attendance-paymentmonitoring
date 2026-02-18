<?php

namespace App\Enums;

enum AttendanceStatus: string
{
    case PRESENT = 'PRESENT';
    case ABSENT = 'ABSENT';
    case EXCUSED = 'EXCUSED';
    case LATE = 'LATE';

    public function label(): string
    {
        return match($this) {
            self::PRESENT => 'Present',
            self::ABSENT => 'Absent',
            self::EXCUSED => 'Excused',
            self::LATE => 'Late',
        };
    }
}
