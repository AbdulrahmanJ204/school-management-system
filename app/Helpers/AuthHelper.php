<?php

namespace App\Helpers;
use App\Exceptions\PermissionException;
use Illuminate\Support\Facades\Auth;

class AuthHelper
{
    /**
     * @throws PermissionException
     */
    public static function authorize($permission){
        if (!Auth::user()->hasPermissionTo($permission->value))
            throw new PermissionException();
    }
}
