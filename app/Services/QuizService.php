<?php

namespace App\Services;

use App\Exceptions\InvalidTargetException;
use App\Exceptions\PermissionException;
use App\Exceptions\QuizNotFoundException;
use App\Helpers\ResponseHelper;
use App\Http\Resources\DetailedQuizResource;
use App\Http\Resources\QuizResource;
use App\Models\Quiz;
use App\Models\QuizTarget;
use App\Models\Section;
use App\Models\Subject;
use Illuminate\Support\Facades\DB;

class QuizService
{
    public function listQuizzes($request)
    {
        $user = auth()->user();

        // Check if authenticated and has the correct user_type
        if ($user->user_type !== 'teacher' && !($user->user_type === 'admin' && $user->hasPermissionTo('عرض الاختبارات المؤتمتة'))) {
            throw new PermissionException(); // or return error response
        }

        $credentials = $request->validated();

        $query = Quiz::withCount('questions')
            ->with(['targets.subject', 'targets.section.grade', 'targets.semester'])
            ->where('created_by', $user->id);

        if (isset($credentials['grade_id'])) {
            $query->whereHas('targets.section.grade', fn($q) => $q->where('id', $credentials['grade_id']));
        }

        if (isset($credentials['section_id'])) {
            $query->whereHas('targets', fn($q) => $q->where('section_id', $credentials['section_id']));
        }

        if (!empty($credentials['subject_id'])) {
            $query->whereHas('targets.subject', function ($q) use ($credentials) {$q->where('id', $credentials['subject_id']);});
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

        if ($user->user_type !== 'teacher' && !($user->user_type === 'admin' && $user->hasPermissionTo('عرض الاختبار المؤتمت')) || $quiz->created_by !== $user->id) {
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

        if ($user->user_type !== 'teacher' && !($user->user_type === 'admin' && $user->hasPermissionTo('انشاء اختبار مؤتمت'))) {
            throw new PermissionException();
        }

        $credentials = $request->validated();
        $credentials['created_by'] = $user->id;

        DB::beginTransaction();

        try {

            $quiz = Quiz::create([
                'name'       => $credentials['name'],
                'full_score' => $credentials['full_score'],
                'created_by' => $user->id,
            ]);

            $subject = Subject::findOrFail($credentials['subject_id']);

            if ($subject->getGrade()->id != $credentials['grade_id']) {
                throw new InvalidTargetException(__('messages.quiz.subject_grade_mismatch'));
            }

            if (!empty($credentials['section_ids'])) {
                foreach ($credentials['section_ids'] as $sectionId) {
                    $section = Section::findOrFail($sectionId);

                    if ($section->grade_id != $credentials['grade_id']) {
                        throw new InvalidTargetException(__('messages.quiz.section_grade_mismatch'));
                    }

                    $quiz->targets()->create([
                        'grade_id'    => $credentials['grade_id'],
                        'subject_id'  => $credentials['subject_id'],
                        'semester_id' => $credentials['semester_id'],
                        'section_id'  => $sectionId,
                    ]);
                }
            } else {
                $sections = Section::where('grade_id', $credentials['grade_id'])->pluck('id');
                foreach ($sections as $sectionId) {
                    $quiz->targets()->create([
                        'grade_id'    => $credentials['grade_id'],
                        'subject_id'  => $credentials['subject_id'],
                        'semester_id' => $credentials['semester_id'],
                        'section_id'  => $sectionId,
                    ]);
                }
            }

            DB::commit();

            return ResponseHelper::jsonResponse(
                new QuizResource($quiz->load('targets')),
                __('messages.quiz.created'),
                200
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

        if ($user->user_type !== 'teacher' && !($user->user_type === 'admin' && $user->hasPermissionTo('تفعيل اختبار مؤتمت')) || $quiz->created_by !== $user->id) {
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

        if ($user->user_type !== 'teacher' && !($user->user_type === 'admin' && $user->hasPermissionTo('تعطيل اختبار مؤتمت')) || $quiz->created_by !== $user->id) {
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
        $quiz = Quiz::with('targets')->find($id);

        if (!$quiz) {
            throw new QuizNotFoundException();
        }

        if ($user->user_type !== 'teacher' && !($user->user_type === 'admin' && $user->hasPermissionTo('تعديل اختبار مؤتمت')) || $quiz->created_by !== $user->id) {
            throw new PermissionException();
        }

        $credentials = $request->validated();

        DB::beginTransaction();

        try {
            $quiz->update([
                'name'       => $credentials['name']       ?? $quiz->name,
                'full_score' => $credentials['full_score'] ?? $quiz->full_score,
            ]);

            // Only rebuild targets if user passed grade/subject/semester/sections
            if (isset($credentials['grade_id'], $credentials['subject_id'], $credentials['semester_id'])) {

                $quiz->targets()->delete();

                $sectionIds = $credentials['section_ids'] ?? Section::where('grade_id', $credentials['grade_id'])->pluck('id')->toArray();

                foreach ($sectionIds as $sectionId) {
                    $quiz->targets()->create([
                        'grade_id'    => $credentials['grade_id'],
                        'subject_id'  => $credentials['subject_id'],
                        'semester_id' => $credentials['semester_id'],
                        'section_id'  => $sectionId,
                    ]);
                }
            }

            DB::commit();

            return ResponseHelper::jsonResponse(
                new QuizResource($quiz->load('targets')),
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

        if ($user->user_type !== 'teacher' && !($user->user_type === 'admin' && $user->hasPermissionTo('حذف اختبار مؤتمت')) || $quiz->created_by !== $user->id) {
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
