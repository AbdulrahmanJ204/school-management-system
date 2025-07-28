<?php

namespace App\Exceptions;

use App\Helpers\ResponseHelper;
use Exception;

class QuizNotFoundException extends Exception
{
    public function render()
    {
        return ResponseHelper::jsonResponse ([],
            __('messages.quiz.not-found'),
            404,
            false
        );
    }
}
