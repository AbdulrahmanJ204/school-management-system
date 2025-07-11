<?php

namespace App\Exceptions;

use App\Helpers\ResponseHelper;
use Exception;

class QuestionNotFoundException extends Exception
{
    public function render()
    {
        return ResponseHelper::jsonResponse ([],
            __('messages.question.not-found'),
            404,
            false
        );
    }
}
