<?php

namespace App\Services;

use App\Exceptions\PermissionException;
use App\Exceptions\QuizNotFoundException;
use App\Helpers\ResponseHelper;
use App\Http\Resources\DetailedQuizResource;
use App\Http\Resources\QuizResource;
use App\Models\Quiz;
use App\Models\QuizTarget;
use Illuminate\Support\Facades\DB;

class QuizService
{
    public function listQuizzes($request)
    {
        $user = auth()->user();
        $credentials = $request->validated();

        /*if (!$user->hasPermissionTo('list_quizzes') || $quiz->created_by !== $user->id) {
            throw new PermissionException();
        }*/

        $query = Quiz::withCount('questions')
            ->with(['targets.subject', 'targets.section.grade', 'targets.semester'])
            ->where('created_by', $user->id);

        if (isset($credentials['grade_id'])) {
            $query->whereHas('targets.section.grade', fn($q) => $q->where('id', $credentials['grade_id']));
        }

        if (isset($credentials['section_id'])) {
            $query->whereHas('targets', fn($q) => $q->where('section_id', $credentials['section_id']));
        }

        $quizzes = $query->get();

        return ResponseHelper::jsonResponse(
            QuizResource::collection($quizzes),
            __('messages.quiz.listed')
        );
    }
    public function showQuiz($id)
    {
        $user = auth()->user();

        $quiz = Quiz::with([
            'questions',
            'targets.subject',
            'targets.section.grade',
            'targets.semester',
        ])->find($id);

        if (!$quiz) {
            throw new QuizNotFoundException();
        }

        if (!$user->hasPermissionTo('get_quiz') || $quiz->created_by !== $user->id) {
            throw new PermissionException();
        }

        return ResponseHelper::jsonResponse(
            new DetailedQuizResource($quiz),
            __('messages.quiz.fetched')
        );
    }
    public function create($request)
    {
        $user = auth()->user();

        if (!$user->hasPermissionTo('create_quiz')) {
            throw new PermissionException();
        }

        $credentials = $request->validated();

        DB::beginTransaction();

        try {
            $quiz = Quiz::create([
                'name'       => $credentials['name'],
                'created_by' => $user->id,
            ]);

            foreach ($credentials['targets'] as $target) {
                QuizTarget::create([
                    'quiz_id'     => $quiz->id,
                    'subject_id'  => $target['subject_id'],
                    'section_id'  => $target['section_id'],
                    'semester_id' => $target['semester_id'],
                ]);
            }

            DB::commit();

            return ResponseHelper::jsonResponse(
                null,
                __('messages.quiz.created'),
                201
            );

        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
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

        DB::beginTransaction();

        try {
            $quiz->update([
                'name' => $credentials['name'],
            ]);

            $existingIds = [];

            foreach ($credentials['targets'] as $target) {
                if (isset($target['id'])) {
                    $quizTarget = QuizTarget::where('id', $target['id'])
                        ->where('quiz_id', $quiz->id)
                        ->first();

                    if ($quizTarget) {
                        $quizTarget->update([
                            'subject_id'  => $target['subject_id'],
                            'section_id'  => $target['section_id'],
                            'semester_id' => $target['semester_id'],
                        ]);

                        $existingIds[] = $quizTarget->id;
                    }
                } else {
                    $new = $quiz->targets()->create([
                        'subject_id'  => $target['subject_id'],
                        'section_id'  => $target['section_id'],
                        'semester_id' => $target['semester_id'],
                    ]);

                    $existingIds[] = $new->id;
                }
            }

            $quiz->targets()->whereNotIn('id', $existingIds)->delete();

            DB::commit();

            return ResponseHelper::jsonResponse(
                null,
                __("messages.quiz.updated"),
                201
            );

        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
    public function destroy($id)
    {
        $user = auth()->user();

        $quiz = Quiz::with('questions', 'targets')->find($id);

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
