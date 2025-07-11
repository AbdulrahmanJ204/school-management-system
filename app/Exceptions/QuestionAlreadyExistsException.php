<?php

namespace App\Exceptions;

use App\Helpers\ResponseHelper;
use Exception;

class QuestionAlreadyExistsException extends Exception
{
    public function render()
    {
        return ResponseHelper::jsonResponse ([],
            __('messages.question.already_exists'),
            400,
            false
        );
    }
}
