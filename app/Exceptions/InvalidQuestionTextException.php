<?php

namespace App\Exceptions;

use App\Helpers\ResponseHelper;
use Exception;

class InvalidQuestionTextException extends Exception
{
    public function render()
    {
        return ResponseHelper::jsonResponse ([],
            __('messages.question.invalid_text'),
            400,
            false
        );
    }
}
