<?php

namespace App\Enums;

enum Platform: string
{
    case Android = 'android';
    case IOS = 'ios';
    // case Web = 'web';

    /**
     * Get all platform values
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Check if a platform value is valid
     */
    public static function isValid(string $value): bool
    {
        return in_array($value, self::values());
    }
}
