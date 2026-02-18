<?php

namespace App\Enums;

enum SessionType: string
{
    case ONE_ON_ONE = 'ONE_ON_ONE';
    case GROUP_PLAY = 'GROUP_PLAY';
    case WRITING = 'WRITING';
    case PRACTICE_SPEECH = 'PRACTICE_SPEECH';

    public function label(): string
    {
        return match($this) {
            self::ONE_ON_ONE => 'One-on-One',
            self::GROUP_PLAY => 'Group Play',
            self::WRITING => 'Writing',
            self::PRACTICE_SPEECH => 'Practice Speech',
        };
    }
}
