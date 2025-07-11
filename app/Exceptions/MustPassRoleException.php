<?php

namespace App\Exceptions;

use App\Helpers\ResponseHelper;
use Exception;

class MustPassRoleException extends Exception
{
    public function render()
    {
        return ResponseHelper::jsonResponse(
            null,
            __('messages.auth.pass_role'),
            400,
            false
        );
    }
}
