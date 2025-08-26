<?php

namespace App\Enums;

enum ClassSessionStatus: string
{
    case SCHEDULED = 'scheduled';
    case IN_PROGRESS = 'in_progress';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::SCHEDULED => 'Scheduled',
            self::IN_PROGRESS => 'In Progress',
            self::COMPLETED => 'Completed',
            self::CANCELLED => 'Cancelled',
        };
    }

    public function labelAr(): string
    {
        return match ($this) {
            self::SCHEDULED => 'مجدولة',
            self::IN_PROGRESS => 'قيد التنفيذ',
            self::COMPLETED => 'مكتملة',
            self::CANCELLED => 'ملغية',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
