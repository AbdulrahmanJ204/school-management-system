<?php

namespace App\Exceptions;

use App\Helpers\ResponseHelper;
use Exception;

class UserNotFoundException extends Exception
{
    public function render()
    {
        return ResponseHelper::jsonResponse ([],
            __('messages.user.not-found'),
            404,
            false
        );
    }
}
