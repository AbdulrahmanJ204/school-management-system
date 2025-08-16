<?php

namespace App\Exceptions;

use App\Helpers\ResponseHelper;
use Exception;

class ClassPeriodNotFoundException extends Exception
{
    public function render()
    {
        return ResponseHelper::jsonResponse ([],
            __('messages.class_period.not_found'),
            404,
            false
        );
    }
}
