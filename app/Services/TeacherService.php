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
        if (!auth()->user()->hasPermissionTo('list_teachers')) {
            throw new PermissionException();
        }

        $teachers = User::select('id', 'first_name', 'father_name', 'last_name', 'gender', 'birth_date', 'email', 'phone', 'role', 'image')
            ->where('role', 'teacher')
            ->with(['teacher'])
            ->orderBy('id', 'asc')
            ->paginate(15);

        return ResponseHelper::jsonResponse(
            UserResource::collection($teachers),
        );
    }
}
