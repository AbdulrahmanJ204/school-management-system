<?php

namespace App\Services;

use App\Enums\PermissionEnum;
use App\Exceptions\PermissionException;
use App\Helpers\ResponseHelper;
use App\Http\Resources\BehaviorNoteResource;
use App\Http\Resources\StudyNoteResource;
use App\Models\BehaviorNote;
use App\Models\StudyNote;
use App\Traits\HasPermissionChecks;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CombinedNotesService
{
    use HasPermissionChecks;

    /**
     * @throws PermissionException
     */
    public function getCombinedNotes(Request $request): JsonResponse
    {
        // Check permissions for both study notes and behavior notes
        $this->checkPermission(PermissionEnum::VIEW_STUDY_NOTES);
        $this->checkPermission(PermissionEnum::VIEW_BEHAVIOR_NOTES);

        $filters = $request->only(['section_id', 'grade_id', 'subject_id', 'student_id']);
        $perPage = $request->get('per_page', 50); // Changed to 50 like StudentService

        // Build study notes query
        $studyNotesQuery = StudyNote::with([
            'student.user',
            'student.studentEnrollments.section.grade',
            'schoolDay',
            'subject.mainSubject.grade',
        ]);

        // Build behavior notes query
        $behaviorNotesQuery = BehaviorNote::with([
            'student.user',
            'student.studentEnrollments.section.grade',
            'schoolDay',
        ]);

        // Apply filters to study notes
        $this->applyFilters($studyNotesQuery, $filters);

        // Apply filters to behavior notes
        $this->applyFilters($behaviorNotesQuery, $filters);

        // Get paginated results
        $studyNotes = $studyNotesQuery->orderBy('created_at', 'desc')->paginate($perPage);
        $behaviorNotes = $behaviorNotesQuery->orderBy('created_at', 'desc')->paginate($perPage);

        // Transform the data
        $studyNotesData = StudyNoteResource::collection($studyNotes);
        $behaviorNotesData = BehaviorNoteResource::collection($behaviorNotes);

        // Combine the results with pagination info
        $combinedData = [
            'study_notes' => $studyNotesData,
            'behavior_notes' => $behaviorNotesData,
        ];

        // Get the maximum page count between both note types
        $maxPageCount = max($studyNotes->lastPage(), $behaviorNotes->lastPage());

        return ResponseHelper::jsonResponse(
            $combinedData,
            __('messages.combined_notes.listed'),
            200,
            true,
            $maxPageCount
        );
    }

    /**
     * Apply filters to the query
     */
    private function applyFilters($query, array $filters): void
    {
        // Filter by student
        if (!empty($filters['student_id'])) {
            $query->where('student_id', $filters['student_id']);
        }

        // Filter by section
        if (!empty($filters['section_id'])) {
            $query->whereHas('student.studentEnrollments', function ($q) use ($filters) {
                $q->where('section_id', $filters['section_id']);
            });
        }

        // Filter by grade
        if (!empty($filters['grade_id'])) {
            $query->whereHas('student.studentEnrollments.section', function ($q) use ($filters) {
                $q->where('grade_id', $filters['grade_id']);
            });
        }

        // Filter by subject (only for study notes)
        if (!empty($filters['subject_id']) && $query->getModel() instanceof StudyNote) {
            $query->where('subject_id', $filters['subject_id']);
        }
    }
}
