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
}
