<?php

namespace App\Exceptions;

use App\Helpers\ResponseHelper;
use Exception;

class MustPassUserTypeException extends Exception
{
    public function render()
    {
        return ResponseHelper::jsonResponse(
            null,
            __('messages.auth.pass_user_type'),
            400,
            false
        );
    }
}
