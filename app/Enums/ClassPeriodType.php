<?php

namespace App\Enums;

enum ClassPeriodType: string
{
    case STUDY = 'study';
    case BREAK = 'break';

    public function label(): string
    {
        return match ($this) {
            self::STUDY => 'Study',
            self::BREAK => 'Break',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
