<?php

namespace App\Helpers;

class UserTypeHelper
{
    public static function getGuardForUserType(string $user_type): string
    {
        return match ($user_type) {
            'admin'   => 'api',
            'student' => 'api',
            'teacher' => 'api',
        };
    }
}
