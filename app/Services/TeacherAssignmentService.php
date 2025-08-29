<?php

namespace App\Services;

use App\Helpers\ResponseHelper;
use App\Http\Resources\TeacherAssignmentResource;
use App\Models\Assignment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class TeacherAssignmentService
{
    public function createAssignment(Request $request): JsonResponse
    {
        $data = $request->validated();
        $data['created_by'] = auth()->id();

        // Handle direct file upload
        if ($request->hasFile('photo')) {
            $data['photo'] = $this->handleFileUpload($request->file('photo'));
        }

        $assignment = Assignment::create($data);

        return ResponseHelper::jsonResponse(
            [
                'assignment' => new TeacherAssignmentResource($assignment->load([
                    'assignedSession.schoolDay',
                    'assignedSession.classPeriod',
                    'dueSession.schoolDay',
                    'dueSession.classPeriod',
                    'subject',
                    'createdBy'
                ]))
            ],
            'تم إنشاء التكليف بنجاح',
            ResponseAlias::HTTP_CREATED
        );
    }

    public function updateAssignment(Request $request, int $id): JsonResponse
    {
        $assignment = Assignment::findOrFail($id);
        $this->checkAssignmentOwnership($assignment);

        $data = $request->validated();

        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($assignment->photo) {
                Storage::disk('public')->delete($assignment->photo);
            }
            $data['photo'] = $this->handleFileUpload($request->file('photo'));
        }

        $assignment->update($data);

        return ResponseHelper::jsonResponse(
            [
                'assignment' => new TeacherAssignmentResource($assignment->load([
                    'assignedSession.schoolDay',
                    'assignedSession.classPeriod',
                    'dueSession.schoolDay',
                    'dueSession.classPeriod',
                    'subject',
                    'createdBy'
                ]))
            ],
            'تم تحديث التكليف بنجاح'
        );
    }

    /**
     * List all assignments for teacher
     */
    public function listAssignments(Request $request): JsonResponse
    {
        $user = auth()->user();
        $teacher = $user->teacher;

        $query = Assignment::with([
            'assignedSession.schoolDay',
            'assignedSession.classPeriod',
            'dueSession.schoolDay',
            'dueSession.classPeriod',
            'subject',
            'createdBy'
        ])
            ->where('created_by', $user->id);

        // Apply filters
        if ($request->has('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }

        if ($request->has('section_id')) {
            $query->where('section_id', $request->section_id);
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
                'assignments' => TeacherAssignmentResource::collection($assignments)
            ],
            'قائمة التكليفات'
        );
    }

    /**
     * Delete an assignment
     */
    public function deleteAssignment(int $id): JsonResponse
    {
        $assignment = Assignment::findOrFail($id);

        // Check if teacher owns this assignment
        $this->checkAssignmentOwnership($assignment);

        // Delete photo if exists
        if ($assignment->photo) {
            Storage::disk('public')->delete($assignment->photo);
        }

        $assignment->delete();

        return ResponseHelper::jsonResponse(
            null,
            'تم حذف التكليف بنجاح'
        );
    }

    /**
     * Handle direct file upload
     */
    private function handleFileUpload(UploadedFile $file): string
    {
        return $file->store('assignments', 'public');
    }

    private function checkAssignmentOwnership(Assignment $assignment): void
    {
        $user = auth()->user();
        if ($assignment->created_by !== $user->id) {
            abort(403, 'غير مصرح لك بالوصول لهذا التكليف');
        }
    }
}
