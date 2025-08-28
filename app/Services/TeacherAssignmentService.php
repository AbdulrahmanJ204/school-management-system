<?php

namespace App\Services;

use App\Helpers\ResponseHelper;
use App\Http\Resources\TeacherAssignmentResource;
use App\Models\Assignment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class TeacherAssignmentService
{
    /**
     * Create a new assignment
     */
    public function createAssignment(Request $request): JsonResponse
    {
        $data = $request->validated();
        $data['created_by'] = auth()->id();

        // Handle photo upload if provided (base64)
        if ($request->has('photo') && $request->photo) {
            $data['photo'] = $this->handleBase64Image($request->photo);
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

    /**
     * Update an existing assignment
     */
    public function updateAssignment(Request $request, int $id): JsonResponse
    {
        $assignment = Assignment::findOrFail($id);
        
        // Check if teacher owns this assignment
        $this->checkAssignmentOwnership($assignment);
        
        $data = $request->validated();

        if ($request->has('photo') && $request->photo) {
            // Delete old photo if exists
            if ($assignment->photo) {
                Storage::disk('public')->delete($assignment->photo);
            }
            $data['photo'] = $this->handleBase64Image($request->photo);
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
     * Check if the authenticated teacher owns the assignment
     */
    private function checkAssignmentOwnership(Assignment $assignment): void
    {
        $user = auth()->user();
        if ($assignment->created_by !== $user->id) {
            abort(403, 'غير مصرح لك بالوصول لهذا التكليف');
        }
    }

    /**
     * Handle base64 image upload
     */
    private function handleBase64Image(string $base64String): string
    {
        // Remove data:image/...;base64, prefix if present
        if (strpos($base64String, 'data:image/') === 0) {
            $base64String = substr($base64String, strpos($base64String, ',') + 1);
        }

        // Decode base64
        $imageData = base64_decode($base64String);
        
        if ($imageData === false) {
            abort(400, 'صورة غير صالحة');
        }

        // Generate unique filename
        $filename = 'assignment_' . time() . '_' . Str::random(10) . '.jpg';
        $path = 'assignments/' . $filename;

        // Store the image
        Storage::disk('public')->put($path, $imageData);

        return $path;
    }
}

