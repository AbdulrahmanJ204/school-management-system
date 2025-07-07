<?php

namespace App\Exceptions;

use App\Helpers\ResponseHelper;
use Exception;

class DeviceAlreadyExistsException extends Exception
{
    public function render()
    {
        return ResponseHelper::jsonResponse ([],
            __('messages.user.device_exists'),
            400,
            false
        );
    }
}
