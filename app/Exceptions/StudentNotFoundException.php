<?php

namespace App\Exceptions;

use App\Helpers\ResponseHelper;
use Exception;

class StudentNotFoundException extends Exception
{
    public function render()
    {
        return ResponseHelper::jsonResponse ([],
            __('messages.student.not_found'),
            400,
            false
        );
    }
}
