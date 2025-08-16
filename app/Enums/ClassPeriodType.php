<?php

namespace App\Enums;

enum ClassPeriodType: string
{
    case STUDY = 'study';
    case OPPORTUNITY = 'opportunity';

    public function label(): string
    {
        return match ($this) {
            self::STUDY => 'Study',
            self::OPPORTUNITY => 'Opportunity',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
