<?php

namespace App\Helpers;

class RoleHelper
{
    public static function getGuardForRole(string $role): string
    {
        return match ($role) {
            'admin'   => 'api',
            'student' => 'api',
            'teacher' => 'api',
        };
    }
}
