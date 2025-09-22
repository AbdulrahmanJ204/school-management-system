<?php

namespace App\Exceptions;

use App\Helpers\ResponseHelper;
use Exception;

class TimetableNotFoundException extends Exception
{
    public function render()
    {
        return ResponseHelper::jsonResponse ([],
            __('messages.timetable.not_found'),
            404,
            false
        );
    }
}
