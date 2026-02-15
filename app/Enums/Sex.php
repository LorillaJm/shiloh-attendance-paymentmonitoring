<?php

namespace App\Enums;

enum Sex: string
{
    case MALE = 'Male';
    case FEMALE = 'Female';

    public function label(): string
    {
        return $this->value;
    }

    public static function options(): array
    {
        return [
            self::MALE->value => self::MALE->label(),
            self::FEMALE->value => self::FEMALE->label(),
        ];
    }
}
