<?php

namespace App\Exceptions;

use App\Helpers\ResponseHelper;
use Exception;

class ClassSessionNotFoundException extends Exception
{
    public function render()
    {
        return ResponseHelper::jsonResponse ([],
            __('messages.class_session.not_found'),
            404,
            false
        );
    }
}
