<?php

namespace App\Services;

use App\Enums\PermissionEnum;
use App\Exceptions\PermissionException;
use App\Helpers\ResponseHelper;
use App\Http\Resources\BehaviorNoteResource;
use App\Models\BehaviorNote;
use App\Models\SchoolDay;
use App\Models\StudentEnrollment;
use App\Traits\HasPermissionChecks;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class BehaviorNoteService
{
    use HasPermissionChecks;

    /**
     * @throws PermissionException
     */
    public function listBehaviorNotes(): JsonResponse
    {
        $this->checkPermission(PermissionEnum::VIEW_BEHAVIOR_NOTES);

        $behaviorNotes = BehaviorNote::with([
           'student',
           'schoolDay',
        ])
            ->orderBy('id', 'desc')
            ->paginate(50);

        return ResponseHelper::jsonResponse(
            BehaviorNoteResource::collection($behaviorNotes),
            __('messages.behavior_note.listed'),
            200,
            true,
            $behaviorNotes->lastPage()
        );
    }

    /**
     * @throws PermissionException
     */
    public function listTrashedBehaviorNotes(): JsonResponse
    {
        $this->checkPermission(PermissionEnum::MANAGE_DELETED_BEHAVIOR_NOTES);

        $behaviorNotes = BehaviorNote::onlyTrashed()
            ->with([
                'student',
                'schoolDay',
            ])
            ->orderBy('id', 'desc')
            ->paginate(50);

        return ResponseHelper::jsonResponse(
            BehaviorNoteResource::collection($behaviorNotes),
            __('messages.behavior_note.listed'),
            200,
            true,
            $behaviorNotes->lastPage()
        );
    }

    /**
     * @throws PermissionException
     */
    public function createBehaviorNote($request): JsonResponse
    {
        $this->checkPermission(PermissionEnum::CREATE_BEHAVIOR_NOTE);

        $data = $request->validated();
        $data['created_by'] = auth()->id();

        $behaviorNote = BehaviorNote::create($data);

        return ResponseHelper::jsonResponse(
            new BehaviorNoteResource($behaviorNote->load([
                'student',
                'schoolDay',
            ])),
            __('messages.behavior_note.created'),
            ResponseAlias::HTTP_CREATED,
        );
    }

    /**
     * @throws PermissionException
     */
    public function showBehaviorNote($id): JsonResponse
    {
        $this->checkPermission(PermissionEnum::VIEW_BEHAVIOR_NOTE);

        $behaviorNote = BehaviorNote::with([
            'student',
            'schoolDay',
        ])
            ->findOrFail($id);

        return ResponseHelper::jsonResponse(
            new BehaviorNoteResource($behaviorNote),
        );
    }

    /**
     * @throws PermissionException
     */
    public function updateBehaviorNote($request, $id): JsonResponse
    {
        $this->checkPermission(PermissionEnum::UPDATE_BEHAVIOR_NOTE);

        $behaviorNote = BehaviorNote::findOrFail($id);
        $data = $request->validated();

        $behaviorNote->update($data);

        return ResponseHelper::jsonResponse(
            new BehaviorNoteResource($behaviorNote->load([
                'student',
                'schoolDay',
            ])),
            __('messages.behavior_note.updated'),
        );
    }

    /**
     * @throws PermissionException
     */
    public function deleteBehaviorNote($id): JsonResponse
    {
        $this->checkPermission(PermissionEnum::DELETE_BEHAVIOR_NOTE);

        $behaviorNote = BehaviorNote::findOrFail($id);
        $behaviorNote->delete();

        return ResponseHelper::jsonResponse(
            null,
            __('messages.behavior_note.deleted'),
        );
    }

    /**
     * @throws PermissionException
     */
    public function restoreBehaviorNote($id): JsonResponse
    {
        $this->checkPermission(PermissionEnum::MANAGE_DELETED_BEHAVIOR_NOTES);

        $behaviorNote = BehaviorNote::onlyTrashed()->findOrFail($id);
        $behaviorNote->restore();

        return ResponseHelper::jsonResponse(
            new BehaviorNoteResource($behaviorNote->load([
                'student',
                'schoolDay',
            ])),
            __('messages.behavior_note.restore'),
        );
    }

    /**
     * @throws PermissionException
     */
    public function forceDeleteBehaviorNote($id): JsonResponse
    {
        $this->checkPermission(PermissionEnum::MANAGE_DELETED_BEHAVIOR_NOTES);

//        $behaviorNote = BehaviorNote::onlyTrashed()->findOrFail($id);
        $behaviorNote = BehaviorNote::findOrFail($id);
        $behaviorNote->forceDelete();

        return ResponseHelper::jsonResponse(
            null,
            __('messages.behavior_note.force_deleted'),
        );
    }

    /**
     * @throws PermissionException
     */
    public function getByStudent($studentId): JsonResponse
    {
        $this->checkPermission(PermissionEnum::VIEW_BEHAVIOR_NOTES);

        $behaviorNotes = BehaviorNote::where('student_id', $studentId)
            ->with([
                'student',
                'schoolDay',
            ])
            ->orderBy('id', 'desc')
            ->paginate(50);

        return ResponseHelper::jsonResponse(
            BehaviorNoteResource::collection($behaviorNotes),
            __('messages.behavior_note.listed'),
            200,
            true,
            $behaviorNotes->lastPage()
        );
    }

    /**
     * @throws PermissionException
     */
    public function getBySchoolDay($schoolDayId): JsonResponse
    {
        $this->checkPermission(PermissionEnum::VIEW_BEHAVIOR_NOTES);

        $behaviorNotes = BehaviorNote::where('school_day_id', $schoolDayId)
            ->with([
                'student',
                'schoolDay',
            ])
            ->orderBy('id', 'desc')
            ->paginate(50);

        return ResponseHelper::jsonResponse(
            BehaviorNoteResource::collection($behaviorNotes),
            __('messages.behavior_note.listed'),
            200,
            true,
            $behaviorNotes->lastPage()
        );
    }

    /**
     * Get behavior notes for student (no pagination, with filters)
     * @throws PermissionException
     */
    public function getStudentBehaviorNotes($request): JsonResponse
    {
        $this->checkPermission(PermissionEnum::VIEW_BEHAVIOR_NOTES);

        $user = auth()->user();
        $student = $user->student;

        if (!$student) {
            throw new PermissionException('Student not found');
        }

        $query = BehaviorNote::where('student_id', $student->id)
            ->with([
                'schoolDay',
            ])
            ->orderBy('id', 'desc');

        // Apply filters
        if ($request->filled('behavior_type')) {
            $query->where('behavior_type', $request->behavior_type);
        }

        if ($request->filled('date_from')) {
            $query->whereHas('schoolDay', function ($q) use ($request) {
                $q->where('date', '>=', $request->date_from);
            });
        }

        if ($request->filled('date_to')) {
            $query->whereHas('schoolDay', function ($q) use ($request) {
                $q->where('date', '<=', $request->date_to);
            });
        }

        $behaviorNotes = $query->get();

        return ResponseHelper::jsonResponse(
            BehaviorNoteResource::collection($behaviorNotes),
            __('messages.behavior_note.listed'),
        );
    }

    /**
     * Get behavior notes for teacher (no pagination, with filters)
     * @throws PermissionException
     */
    public function getTeacherBehaviorNotes($request): JsonResponse
    {
        $this->checkPermission(PermissionEnum::VIEW_BEHAVIOR_NOTES);

        $user = auth()->user();
        $teacher = $user->teacher;

        if (!$teacher) {
            throw new PermissionException('Teacher not found');
        }

        // Get teacher's assigned sections
        $teacherAssignments = $teacher->teacherSectionSubjects()
            ->where('is_active', true)
            ->get();

        if ($teacherAssignments->isEmpty()) {
            return ResponseHelper::jsonResponse(
                [],
                __('messages.behavior_note.listed'),
            );
        }

        $assignedSectionIds = $teacherAssignments->pluck('section_id')->unique();

        $query = BehaviorNote::whereHas('student.studentEnrollments', function ($enrollmentQuery) use ($assignedSectionIds) {
            $enrollmentQuery->whereIn('section_id', $assignedSectionIds);
        })
        ->with([
            'student',
            'schoolDay',
        ])
        ->orderBy('id', 'desc');

        // Apply additional filters
        if ($request->filled('student_id')) {
            $query->where('student_id', $request->student_id);
        }

        if ($request->filled('section_id') && $assignedSectionIds->contains($request->section_id)) {
            $query->whereHas('student.studentEnrollments', function ($enrollmentQuery) use ($request) {
                $enrollmentQuery->where('section_id', $request->section_id);
            });
        }

        if ($request->filled('behavior_type')) {
            $query->where('behavior_type', $request->behavior_type);
        }

        if ($request->filled('date_from')) {
            $query->whereHas('schoolDay', function ($q) use ($request) {
                $q->where('date', '>=', $request->date_from);
            });
        }

        if ($request->filled('date_to')) {
            $query->whereHas('schoolDay', function ($q) use ($request) {
                $q->where('date', '<=', $request->date_to);
            });
        }

        $behaviorNotes = $query->get();

        return ResponseHelper::jsonResponse(
            BehaviorNoteResource::collection($behaviorNotes),
            __('messages.behavior_note.listed'),
        );
    }

    /**
     * Create behavior note by teacher
     * @throws PermissionException
     */
    public function createTeacherBehaviorNote($request): JsonResponse
    {
        $this->checkPermission(PermissionEnum::CREATE_BEHAVIOR_NOTE);

        $user = auth()->user();
        $teacher = $user->teacher;

        if (!$teacher) {
            throw new PermissionException('Teacher not found');
        }

        $data = $request->validated();
        $data['created_by'] = $user->id;

        // Check if teacher can create behavior note for this student
        $teacherAssignments = $teacher->teacherSectionSubjects()
            ->where('is_active', true)
            ->get();

        if ($teacherAssignments->isEmpty()) {
            throw new PermissionException('You have no assigned sections');
        }

        $assignedSectionIds = $teacherAssignments->pluck('section_id')->unique();

        // Check if the student is enrolled in one of the teacher's assigned sections
        $studentEnrollment = StudentEnrollment::where('student_id', $data['student_id'])
            ->whereIn('section_id', $assignedSectionIds)
            ->first();

        if (!$studentEnrollment) {
            throw new PermissionException('You can only create behavior notes for students in your assigned sections');
        }

        // Set current school day if not provided
        if (!isset($data['school_day_id'])) {
            $currentSchoolDay = SchoolDay::where('date', now()->format('Y-m-d'))->first();
            if ($currentSchoolDay) {
                $data['school_day_id'] = $currentSchoolDay->id;
            } else {
                throw new \Exception('No school day found for today');
            }
        }

        $behaviorNote = BehaviorNote::create($data);

        return ResponseHelper::jsonResponse(
            new BehaviorNoteResource($behaviorNote->load([
                'student',
                'schoolDay',
            ])),
            __('messages.behavior_note.created'),
            ResponseAlias::HTTP_CREATED,
        );
    }

    /**
     * Update behavior note by teacher
     * @throws PermissionException
     */
    public function updateTeacherBehaviorNote($request, $id): JsonResponse
    {
        $this->checkPermission(PermissionEnum::UPDATE_BEHAVIOR_NOTE);

        $user = auth()->user();
        $teacher = $user->teacher;

        if (!$teacher) {
            throw new PermissionException('Teacher not found');
        }

        // Get teacher's assigned sections
        $teacherAssignments = $teacher->teacherSectionSubjects()
            ->where('is_active', true)
            ->get();

        if ($teacherAssignments->isEmpty()) {
            throw new PermissionException('You have no assigned sections');
        }

        $assignedSectionIds = $teacherAssignments->pluck('section_id')->unique();

        // Find behavior note that belongs to teacher's sections
        $behaviorNote = BehaviorNote::where('id', $id)
            ->whereHas('student.studentEnrollments', function ($enrollmentQuery) use ($assignedSectionIds) {
                $enrollmentQuery->whereIn('section_id', $assignedSectionIds);
            })
            ->firstOrFail();

        $data = $request->validated();

        $behaviorNote->update($data);

        return ResponseHelper::jsonResponse(
            new BehaviorNoteResource($behaviorNote->load([
                'student',
                'schoolDay',
            ])),
            __('messages.behavior_note.updated')
        );
    }

    /**
     * Delete behavior note by teacher
     * @throws PermissionException
     */
    public function deleteTeacherBehaviorNote($id): JsonResponse
    {
        $this->checkPermission(PermissionEnum::DELETE_BEHAVIOR_NOTE);

        $user = auth()->user();
        $teacher = $user->teacher;

        if (!$teacher) {
            throw new PermissionException('Teacher not found');
        }

        // Get teacher's assigned sections
        $teacherAssignments = $teacher->teacherSectionSubjects()
            ->where('is_active', true)
            ->get();

        if ($teacherAssignments->isEmpty()) {
            throw new PermissionException('You have no assigned sections');
        }

        $assignedSectionIds = $teacherAssignments->pluck('section_id')->unique();

        // Find behavior note that belongs to teacher's sections
        $behaviorNote = BehaviorNote::where('id', $id)
            ->whereHas('student.studentEnrollments', function ($enrollmentQuery) use ($assignedSectionIds) {
                $enrollmentQuery->whereIn('section_id', $assignedSectionIds);
            })
            ->firstOrFail();

        $behaviorNote->delete();

        return ResponseHelper::jsonResponse(
            null,
            __('messages.behavior_note.deleted')
        );
    }
}
