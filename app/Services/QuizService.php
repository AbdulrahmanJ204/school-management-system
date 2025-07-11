<?php

namespace App\Services;

use App\Exceptions\PermissionException;
use App\Exceptions\QuizNotFoundException;
use App\Helpers\ResponseHelper;
use App\Models\Quiz;

class QuizService
{
    public function create($request)
    {
        $user = auth()->user();

        if (!$user->hasPermissionTo('create_quiz')) {
            throw new PermissionException();
        }

        $credentials = $request->validated();

        $credentials['created_by'] = $user->id;

        Quiz::create($credentials);

        return ResponseHelper::jsonResponse
        (
            null
            ,__("messages.quiz.created")
        );
    }

    public function activate($id)
    {
        $user = auth()->user();
        $quiz = Quiz::find($id);

        if (!$quiz) {
            throw new QuizNotFoundException();
        }

        if (!$user->hasPermissionTo('activate_quiz') || $quiz->created_by !== $user->id) {
            throw new PermissionException();
        }

        if($quiz->is_active){
            return ResponseHelper::jsonResponse(
                null,
                __("messages.quiz.already_active"),
                422,
                false
            );
        }

        $quiz->update([
            'is_active' => 1,
            'taken_at'  => now(),
        ]);

        return ResponseHelper::jsonResponse (
            null,
            __("messages.quiz.activated")
        );
    }

    public function deactivate(int $id)
    {
        $user = auth()->user();
        $quiz = Quiz::find($id);

        if (!$quiz) {
            throw new QuizNotFoundException();
        }

        if (!$user->hasPermissionTo('deactivate_quiz') || $quiz->created_by !== $user->id) {
            throw new PermissionException();
        }

        if (!$quiz->is_active) {
            return ResponseHelper::jsonResponse(
                null,
                __("messages.quiz.already_inactive"),
                422,
                false
            );
        }

        $quiz->update([
            'is_active' => false
        ]);

        return ResponseHelper::jsonResponse(
            null,
            __("messages.quiz.deactivated")
        );
    }

    public function update($request, $id)
    {
        $user = auth()->user();
        $quiz = Quiz::find($id);


        if (!$quiz) {
            throw new QuizNotFoundException();
        }

        if (!$user->hasPermissionTo('update_quiz') || $quiz->created_by !== $user->id) {
            throw new PermissionException();
        }

        $credentials = $request->validated();

        $quiz->update($credentials);

        return ResponseHelper::jsonResponse(
            null,
            __("messages.quiz.updated"),
            201
        );
    }

    public function destroy($id)
    {
        $user = auth()->user();

        $quiz = Quiz::with('questions')->find($id);

        if (!$user->hasPermissionTo('delete_quiz') || $quiz->created_by !== $user->id) {
            throw new PermissionException();
        }

        if (!$quiz) {
            throw new QuizNotFoundException();
        }

        $quiz->delete();

        return ResponseHelper::jsonResponse(
            null,
            __("messages.quiz.deleted")
        );
    }
}
