<?php

namespace App\Exceptions;

use App\Helpers\ResponseHelper;
use Exception;

class InvalidPasswordException extends Exception
{
    public function render()
    {
        return ResponseHelper::jsonResponse ([],
            __('messages.auth.invalid_password'),
            401,
            false
        );
    }
}
