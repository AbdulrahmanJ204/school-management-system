<?php

namespace App\Exceptions;

use App\Helpers\ResponseHelper;
use Exception;

class ScheduleNotFoundException extends Exception
{
    public function render()
    {
        return ResponseHelper::jsonResponse ([],
            __('messages.schedule.not_found'),
            404,
            false
        );
    }
}
