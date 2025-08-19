<?php

namespace App\Services;

use App\Exceptions\PermissionException;
use App\Helpers\ResponseHelper;
use App\Http\Resources\UserResource;
use App\Models\User;

class StudentService
{
    public function listStudents()
    {
        if (!auth()->user()->hasPermissionTo('عرض الطلاب')) {
            throw new PermissionException();
        }

        $students = User::select('id', 'first_name', 'father_name', 'last_name', 'gender', 'birth_date', 'email', 'phone', 'user_type', 'image')
            ->where('user_type', 'student')
            ->with(['devices', 'student'])
            ->orderBy('id', 'asc')
            ->paginate(15);

        return ResponseHelper::jsonResponse(
            UserResource::collection($students),
        );
    }

    public function getStudentsBySectionAndSemester($sectionId, $semesterId)
    {
        if (!auth()->user()->hasPermissionTo('عرض الطلاب')) {
            throw new PermissionException();
        }

        $students = User::select('id', 'first_name', 'father_name', 'last_name', 'gender', 'birth_date', 'email', 'phone', 'user_type', 'image')
            ->where('user_type', 'student')
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
