<?php
namespace App\Enums;

enum WeekDay: string
{
    case SUNDAY = 'sunday';
    case MONDAY = 'monday';
    case TUESDAY = 'tuesday';
    case WEDNESDAY = 'wednesday';
    case THURSDAY = 'thursday';
    case FRIDAY = 'friday';
    case SATURDAY = 'saturday';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function arabic(): array
    {
        return [
            self::SUNDAY->value => 'الأحد',
            self::MONDAY->value => 'الإثنين',
            self::TUESDAY->value => 'الثلاثاء',
            self::WEDNESDAY->value => 'الأربعاء',
            self::THURSDAY->value => 'الخميس',
            self::FRIDAY->value => 'الجمعة',
            self::SATURDAY->value => 'السبت',
        ];
    }
}
