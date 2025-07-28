<?php

namespace App\Exceptions;

use App\Helpers\ResponseHelper;
use Exception;

class RoleNotFoundException extends Exception
{
    public function render()
    {
        return ResponseHelper::jsonResponse ([],
            __('messages.role.not_found'),
            400,
            false
        );
    }
}
