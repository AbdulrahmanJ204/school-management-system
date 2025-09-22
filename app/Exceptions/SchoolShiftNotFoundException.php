<?php

namespace App\Exceptions;

use App\Helpers\ResponseHelper;
use Exception;

class SchoolShiftNotFoundException extends Exception
{
    public function render()
    {
        return ResponseHelper::jsonResponse ([],
            __('messages.school_shift.not_found'),
            404,
            false
        );
    }
}
