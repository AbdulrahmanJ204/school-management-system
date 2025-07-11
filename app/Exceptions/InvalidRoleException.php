<?php

namespace App\Exceptions;

use App\Helpers\ResponseHelper;
use Exception;

class InvalidRoleException extends Exception
{
    public function render()
    {
        return ResponseHelper::jsonResponse(
            null,
            __('messages.auth.invalid_role'),
            403,
            false
        );
    }
}
