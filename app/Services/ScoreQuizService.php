<?php

namespace App\Services;

use App\Exceptions\PermissionException;
use App\Exceptions\QuizAlreadySubmittedException;
use App\Exceptions\StudentNotFoundException;
use App\Helpers\ResponseHelper;
use App\Models\ScoreQuiz;

class ScoreQuizService
{
    public function create($request)
    {
        $user = auth()->user();

        if (!$user->hasPermissionTo('create_score_quiz')) {
            throw new PermissionException();
        }

        $student_id = auth()->user()->student->id;

        if (!$student_id) {
            throw new StudentNotFoundException();
        }

        $credentials = $request->validated();

        $credentials['student_id'] = $student_id;

        $existing = ScoreQuiz::where('quiz_id', $credentials['quiz_id'])
            ->where('student_id', $student_id)
            ->first();

        if ($existing) {
            throw new QuizAlreadySubmittedException();
        }

        if ($credentials['score'] > $credentials['full_score']) {
            ResponseHelper::jsonResponse([
                null,
                __('messages.quiz.score_must_not_exceed {$credentials->full_score}.'),
                400,
                false
            ]);
        }

        ScoreQuiz::create($credentials);

        return ResponseHelper::jsonResponse(
            null,
            __('messages.quiz.score_created'),
            200
        );
    }
}
