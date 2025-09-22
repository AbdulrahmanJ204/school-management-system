<?php

namespace App\Exceptions;

use App\Helpers\ResponseHelper;
use Exception;

class InvalidUserTypeException extends Exception
{
    public function render()
    {
        return ResponseHelper::jsonResponse(
            null,
            __('messages.auth.invalid_user_type'),
            403,
            false
        );
    }
}
