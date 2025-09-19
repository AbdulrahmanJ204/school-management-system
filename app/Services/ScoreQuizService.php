<?php

namespace App\Services;

use App\Exceptions\PermissionException;
use App\Exceptions\QuizAlreadySubmittedException;
use App\Exceptions\StudentNotFoundException;
use App\Helpers\ResponseHelper;
use App\Http\Resources\ScoreQuizResource;
use App\Models\Quiz;
use App\Models\ScoreQuiz;
use Illuminate\Support\Facades\Auth;

class ScoreQuizService
{
    public function create($request)
    {
        $user = Auth::user();

        if ($user->user_type !== 'student') {
            throw new PermissionException();
        }

        $student_id = Auth::user()->student->id;

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

        $quiz = Quiz::select('id', 'full_score')
            ->find($credentials['quiz_id']);

        if ($credentials['score'] > $quiz->full_score) {
            return ResponseHelper::jsonResponse(
                null,
                __('messages.quiz.score_must_not_exceed', ['score' => $quiz->full_score]),
                400,
                false
            );
        }

        $scoreQuiz = ScoreQuiz::create($credentials);

        return ResponseHelper::jsonResponse(
            new ScoreQuizResource($scoreQuiz->load('quiz')),
            __('messages.quiz.score_created'),
            200
        );
    }
}
