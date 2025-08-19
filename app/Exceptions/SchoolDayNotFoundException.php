<?php

namespace App\Exceptions;

use App\Helpers\ResponseHelper;
use Exception;

class SchoolDayNotFoundException extends Exception
{
    public function render()
    {
        return ResponseHelper::jsonResponse ([],
            __('messages.school_day.not_found'),
            404,
            false
        );
    }
}
