<?php

namespace App\Services;

use App\Helpers\ResponseHelper;
use App\Http\Resources\StudentAssignmentResource;
use App\Models\Assignment;
use App\Models\StudentEnrollment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentAssignmentService
{
    /**
     * List assignments for student's section
     */
    public function listAssignments(Request $request): JsonResponse
    {
        $user = Auth::user();
        $student = $user->student;

        if (!$student) {
            abort(403, 'المستخدم ليس طالباً');
        }

        // Get student's current section from enrollment
        $currentEnrollment = StudentEnrollment::where('student_id', $student->id)
            ->whereHas('semester', function ($q) {
                $q->where('is_active', true);
            })
            ->first();

        if (!$currentEnrollment || !$currentEnrollment->section_id) {
            return ResponseHelper::jsonResponse(
                [
                    'assignments' => []
                ],
                'قائمة التكليفات'
            );
        }

        $query = Assignment::with([
            'assignedSession.schoolDay',
            'assignedSession.classPeriod',
            'dueSession.schoolDay',
            'dueSession.classPeriod',
            'subject',
            'createdBy'
        ])
        ->where('section_id', $currentEnrollment->section_id);

        // Apply filters
        if ($request->has('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        if ($request->has('date_from')) {
            $query->whereHas('assignedSession.schoolDay', function ($q) use ($request) {
                $q->where('date', '>=', $request->date_from);
            });
        }

        if ($request->has('date_to')) {
            $query->whereHas('assignedSession.schoolDay', function ($q) use ($request) {
                $q->where('date', '<=', $request->date_to);
            });
        }

        $assignments = $query->orderBy('created_at', 'desc')->get();

        return ResponseHelper::jsonResponse(
            [
                'assignments' => StudentAssignmentResource::collection($assignments)
            ],
            'قائمة التكليفات'
        );
    }
}

