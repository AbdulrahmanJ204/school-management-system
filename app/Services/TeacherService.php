<?php

namespace App\Services;

use App\Exceptions\PermissionException;
use App\Helpers\ResponseHelper;
use App\Http\Resources\UserResource;
use App\Models\User;

class TeacherService
{
    public function listTeachers()
    {
        if (!auth()->user()->hasPermissionTo('عرض الاساتذة')) {
            throw new PermissionException();
        }

        $teachers = User::select('id', 'first_name', 'father_name', 'last_name', 'gender', 'birth_date', 'email', 'phone', 'user_type', 'image')
            ->where('user_type', 'teacher')
            ->with(['teacher'])
            ->orderBy('id', 'asc')
            ->paginate(15);

        return ResponseHelper::jsonResponse(
            UserResource::collection($teachers),
        );
    }
}
