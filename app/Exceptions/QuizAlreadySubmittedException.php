<?php

namespace App\Exceptions;

use App\Helpers\ResponseHelper;
use Exception;

class QuizAlreadySubmittedException extends Exception
{
    public function render()
    {
        return ResponseHelper::jsonResponse ([],
            __('messages.quiz.score_already_submitted'),
            400,
            false
        );
    }
}
