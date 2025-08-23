<?php

namespace App\Services;

use App\Enums\PermissionEnum;
use App\Exceptions\PermissionException;
use App\Helpers\ResponseHelper;
use App\Http\Resources\StudyNoteResource;
use App\Models\StudyNote;
use App\Models\SchoolDay;
use App\Models\StudentEnrollment;
use App\Traits\HasPermissionChecks;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class StudyNoteService
{
    use HasPermissionChecks;

    /**
     * @throws PermissionException
     */
    public function listStudyNotes($request = null): JsonResponse
    {
        $this->checkPermission(PermissionEnum::VIEW_STUDY_NOTES);

        $query = StudyNote::with([
            'student',
            'schoolDay',
            'subject',
        ]);

        // Apply filters if request is provided
        if ($request) {
            $filters = $request->validated();

            // Filter by student_id
            if (isset($filters['student_id'])) {
                $query->where('student_id', $filters['student_id']);
            }

            // Filter by school_day_id
            if (isset($filters['school_day_id'])) {
                $query->where('school_day_id', $filters['school_day_id']);
            }

            // Filter by subject_id
            if (isset($filters['subject_id'])) {
                $query->where('subject_id', $filters['subject_id']);
            }

            // Filter by note_type
            if (isset($filters['note_type'])) {
                $query->where('note_type', $filters['note_type']);
            }

            // Filter by date range
            if (isset($filters['date_from'])) {
                $query->whereHas('schoolDay', function ($q) use ($filters) {
                    $q->where('date', '>=', $filters['date_from']);
                });
            }

            if (isset($filters['date_to'])) {
                $query->whereHas('schoolDay', function ($q) use ($filters) {
                    $q->where('date', '<=', $filters['date_to']);
                });
            }
        }

        $studyNotes = $query->orderBy('id', 'desc')->paginate(50);

        return ResponseHelper::jsonResponse(
            StudyNoteResource::collection($studyNotes),
            __('messages.study_note.listed'),
            200,
            true,
            $studyNotes->lastPage()
        );
    }

    /**
     * @throws PermissionException
     */
    public function listTrashedStudyNotes(): JsonResponse
    {
        $this->checkPermission(PermissionEnum::MANAGE_DELETED_STUDY_NOTES);

        $studyNotes = StudyNote::onlyTrashed()
            ->with([
                'student',
                'schoolDay',
                'subject',
            ])
            ->orderBy('id', 'desc')
            ->paginate(50);

        return ResponseHelper::jsonResponse(
            StudyNoteResource::collection($studyNotes),
            __('messages.study_note.listed'),
            200,
            true,
            $studyNotes->lastPage()
        );
    }

    /**
     * @throws PermissionException
     */
    public function createStudyNote($request): JsonResponse
    {
        $this->checkPermission(PermissionEnum::CREATE_STUDY_NOTE);

        $data = $request->validated();
        $data['created_by'] = auth()->id();

        $studyNote = StudyNote::create($data);

        return ResponseHelper::jsonResponse(
            new StudyNoteResource($studyNote->load([
                'student',
                'schoolDay',
                'subject',
            ])),
            __('messages.study_note.created'),
            ResponseAlias::HTTP_CREATED,
        );
    }

    /**
     * @throws PermissionException
     */
    public function showStudyNote($id): JsonResponse
    {
        $this->checkPermission(PermissionEnum::VIEW_STUDY_NOTE);

        $studyNote = StudyNote::with([
            'student',
            'schoolDay',
            'subject',
        ])
            ->findOrFail($id);

        return ResponseHelper::jsonResponse(
            new StudyNoteResource($studyNote),
            __('messages.study_note.fetched')
        );
    }

    /**
     * @throws PermissionException
     */
    public function updateStudyNote($request, $id): JsonResponse
    {
        $this->checkPermission(PermissionEnum::UPDATE_STUDY_NOTE);

        $studyNote = StudyNote::findOrFail($id);
        $data = $request->validated();

        $studyNote->update($data);

        return ResponseHelper::jsonResponse(
            new StudyNoteResource($studyNote->load([
                'student',
                'schoolDay',
                'subject',
            ])),
            __('messages.study_note.updated')
        );
    }

    /**
     * @throws PermissionException
     */
    public function deleteStudyNote($id): JsonResponse
    {
        $this->checkPermission(PermissionEnum::DELETE_STUDY_NOTE);

        $studyNote = StudyNote::findOrFail($id);
        $studyNote->delete();

        return ResponseHelper::jsonResponse(
            null,
            __('messages.study_note.deleted')
        );
    }

    /**
     * @throws PermissionException
     */
    public function restoreStudyNote($id): JsonResponse
    {
        $this->checkPermission(PermissionEnum::MANAGE_DELETED_STUDY_NOTES);

        $studyNote = StudyNote::onlyTrashed()->findOrFail($id);
        $studyNote->restore();

        return ResponseHelper::jsonResponse(
            new StudyNoteResource($studyNote->load([
                'student',
                'schoolDay',
                'subject',
            ])),
            __('messages.study_note.restored')
        );
    }

    /**
     * @throws PermissionException
     */
    public function forceDeleteStudyNote($id): JsonResponse
    {
        $this->checkPermission(PermissionEnum::MANAGE_DELETED_STUDY_NOTES);

//        $studyNote = StudyNote::onlyTrashed()->findOrFail($id);
        $studyNote = StudyNote::findOrFail($id);
        $studyNote->forceDelete();

        return ResponseHelper::jsonResponse(
            null,
            __('messages.study_note.force_deleted')
        );
    }



    /**
     * Get study notes for student (no pagination, with filters)
     * @throws PermissionException
     */
    public function getStudentStudyNotes($request): JsonResponse
    {
        $this->checkPermission(PermissionEnum::VIEW_STUDY_NOTES);

        $user = auth()->user();
        $student = $user->student;

        if (!$student) {
            throw new PermissionException('Student not found');
        }

        $query = StudyNote::where('student_id', $student->id)
            ->with([
                'schoolDay',
                'subject',
            ])
            ->orderBy('id', 'desc');

        // Apply filters
        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }

        if ($request->filled('note_type')) {
            $query->where('note_type', $request->note_type);
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

        $studyNotes = $query->get();

        return ResponseHelper::jsonResponse(
            StudyNoteResource::collection($studyNotes),
            __('messages.study_note.listed'),
        );
    }

    /**
     * Get study notes for teacher (no pagination, with filters)
     * @throws PermissionException
     */
    public function getTeacherStudyNotes($request): JsonResponse
    {
        $this->checkPermission(PermissionEnum::VIEW_STUDY_NOTES);

        $user = auth()->user();
        $teacher = $user->teacher;

        if (!$teacher) {
            throw new PermissionException('Teacher not found');
        }

        // Get teacher's assigned sections and subjects
        $teacherAssignments = $teacher->teacherSectionSubjects()
            ->where('is_active', true)
            ->get();

        if ($teacherAssignments->isEmpty()) {
            return ResponseHelper::jsonResponse(
                [],
                __('messages.study_note.listed'),
            );
        }

        $assignedSectionIds = $teacherAssignments->pluck('section_id')->unique();
        $assignedSubjectIds = $teacherAssignments->pluck('subject_id')->unique();

        $query = StudyNote::where(function ($q) use ($assignedSectionIds, $assignedSubjectIds) {
            $q->whereIn('subject_id', $assignedSubjectIds);

            $q->whereHas('student.studentEnrollments', function ($enrollmentQuery) use ($assignedSectionIds) {
                $enrollmentQuery->whereIn('section_id', $assignedSectionIds);
            });
        })
        ->with([
//            'student',
//            'schoolDay',
            'subject',
        ])
        ->orderBy('id', 'desc');

        // Apply additional filters
        if ($request->filled('student_id')) {
            $query->where('student_id', $request->student_id);
        }

        if ($request->filled('section_id')) {
            $query->whereHas('student.studentEnrollments', function ($enrollmentQuery) use ($request) {
                $enrollmentQuery->where('section_id', $request->section_id);
            });
        }

        if ($request->filled('subject_id') && $assignedSubjectIds->contains($request->subject_id)) {
            $query->where('subject_id', $request->subject_id);
        }

        if ($request->filled('note_type')) {
            $query->where('note_type', $request->note_type);
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

        $studyNotes = $query->get();

        return ResponseHelper::jsonResponse(
            StudyNoteResource::collection($studyNotes),
            __('messages.study_note.listed'),
        );
    }

    /**
     * Create study note by teacher
     * @throws PermissionException
     */
    public function createTeacherStudyNote($request): JsonResponse
    {
        $this->checkPermission(PermissionEnum::CREATE_STUDY_NOTE);

        $user = auth()->user();
        $teacher = $user->teacher;

        if (!$teacher) {
            throw new PermissionException('Teacher not found');
        }

        $data = $request->validated();
        $data['created_by'] = $user->id;

        // Check if teacher can create study note for this student and subject
        $teacherAssignments = $teacher->teacherSectionSubjects()
            ->where('is_active', true)
            ->where('subject_id', $data['subject_id'])
            ->get();

        if ($teacherAssignments->isEmpty()) {
            throw new PermissionException('You can only create study notes for your assigned subjects');
        }

        // Check if the student is enrolled in one of the teacher's assigned sections
        $assignedSectionIds = $teacherAssignments->pluck('section_id')->unique();
        $studentEnrollment = StudentEnrollment::where('student_id', $data['student_id'])
            ->whereIn('section_id', $assignedSectionIds)
            ->first();

        if (!$studentEnrollment) {
            throw new PermissionException('You can only create study notes for students in your assigned sections');
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

        $studyNote = StudyNote::create($data);

        return ResponseHelper::jsonResponse(
            new StudyNoteResource($studyNote->load([
//                'student',
//                'schoolDay',
                'subject',
            ])),
            __('messages.study_note.created'),
            ResponseAlias::HTTP_CREATED,
        );
    }

    /**
     * Update study note by teacher
     * @throws PermissionException
     */
    public function updateTeacherStudyNote($request, $id): JsonResponse
    {
        $this->checkPermission(PermissionEnum::UPDATE_STUDY_NOTE);

        $user = auth()->user();
        $teacher = $user->teacher;

        if (!$teacher) {
            throw new PermissionException('Teacher not found');
        }

        // Get teacher's assigned sections and subjects
        $teacherAssignments = $teacher->teacherSectionSubjects()
            ->where('is_active', true)
            ->get();

        if ($teacherAssignments->isEmpty()) {
            throw new PermissionException('You have no assigned sections or subjects');
        }

        $assignedSectionIds = $teacherAssignments->pluck('section_id')->unique();
        $assignedSubjectIds = $teacherAssignments->pluck('subject_id')->unique();

        // Find study note that belongs to teacher's sections and subjects
        $studyNote = StudyNote::where('id', $id)
            ->whereIn('subject_id', $assignedSubjectIds)
            ->whereHas('student.studentEnrollments', function ($enrollmentQuery) use ($assignedSectionIds) {
                $enrollmentQuery->whereIn('section_id', $assignedSectionIds);
            })
            ->firstOrFail();

        $data = $request->validated();

        $studyNote->update($data);

        return ResponseHelper::jsonResponse(
            new StudyNoteResource($studyNote->load([
//                'student',
//                'schoolDay',
                'subject',
            ])),
            __('messages.study_note.updated')
        );
    }

    /**
     * Delete study note by teacher
     * @throws PermissionException
     */
    public function deleteTeacherStudyNote($id): JsonResponse
    {
        $this->checkPermission(PermissionEnum::DELETE_STUDY_NOTE);

        $user = auth()->user();
        $teacher = $user->teacher;

        if (!$teacher) {
            throw new PermissionException('Teacher not found');
        }

        // Get teacher's assigned sections and subjects
        $teacherAssignments = $teacher->teacherSectionSubjects()
            ->where('is_active', true)
            ->get();

        if ($teacherAssignments->isEmpty()) {
            throw new PermissionException('You have no assigned sections or subjects');
        }

        $assignedSectionIds = $teacherAssignments->pluck('section_id')->unique();
        $assignedSubjectIds = $teacherAssignments->pluck('subject_id')->unique();

        // Find study note that belongs to teacher's sections and subjects
        $studyNote = StudyNote::where('id', $id)
            ->whereIn('subject_id', $assignedSubjectIds)
            ->whereHas('student.studentEnrollments', function ($enrollmentQuery) use ($assignedSectionIds) {
                $enrollmentQuery->whereIn('section_id', $assignedSectionIds);
            })
            ->firstOrFail();

        $studyNote->delete();

        return ResponseHelper::jsonResponse(
            null,
            __('messages.study_note.deleted')
        );
    }
}
