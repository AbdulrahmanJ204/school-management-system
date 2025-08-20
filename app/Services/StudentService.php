<?php

namespace App\Services;

use App\Exceptions\PermissionException;
use App\Helpers\ResponseHelper;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class StudentService
{
    /**
     * @throws PermissionException
     */
    public function listStudents(): JsonResponse
    {
        if (!auth()->user()->hasPermissionTo('عرض الطلاب')) {
            throw new PermissionException();
        }

        $students = User::where('user_type', 'student')
            ->with(['devices', 'student'])
            ->orderBy('first_name', 'asc')
            ->paginate(50);

        $responseData = [
            'users' => UserResource::collection($students),
            'page_count' => $students->lastPage(),
        ];

        return ResponseHelper::jsonResponse($responseData);
    }

    /**
     * @throws PermissionException
     */
    public function getStudentsBySectionAndSemester($sectionId, $semesterId): JsonResponse
    {
        if (!auth()->user()->hasPermissionTo('عرض الطلاب')) {
            throw new PermissionException();
        }

        $students = User::where('user_type', 'student')
            ->whereHas('student.studentEnrollments', function ($query) use ($sectionId, $semesterId) {
                $query->where('section_id', $sectionId)
                      ->where('semester_id', $semesterId);
            })
            ->with(['devices', 'student.studentEnrollments' => function ($query) use ($sectionId, $semesterId) {
                $query->where('section_id', $sectionId)
                      ->where('semester_id', $semesterId)
                      ->with(['section', 'semester', 'year']);
            }])
            ->orderBy('first_name', 'asc')
            ->get();

        return ResponseHelper::jsonResponse(
            UserResource::collection($students),
        );
    }
}
