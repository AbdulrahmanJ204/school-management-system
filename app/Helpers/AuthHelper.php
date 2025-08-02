<?php

namespace App\Helpers;
use App\Exceptions\PermissionException;

class AuthHelper
{
    /**
     * @throws PermissionException
     */
    public static function authorize($permissionName){
        if (!auth()->user()->hasPermissionTo($permissionName))
            throw new PermissionException();
    }
}
