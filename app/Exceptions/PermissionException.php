<?php

namespace App\Exceptions;

use App\Helpers\ResponseHelper;
use Exception;

class PermissionException extends Exception
{
    public function render()
    {
        return ResponseHelper::jsonResponse ([],
            __('messages.auth.permission_denied'),
            403,
            false
        );
    }
}
