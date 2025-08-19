<?php

namespace App\Services;

use App\Exceptions\PermissionException;
use App\Helpers\ResponseHelper;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class TeacherService
{
    /**
     * @throws PermissionException
     */
    public function listTeachers(): JsonResponse
    {
        if (!auth()->user()->hasPermissionTo('عرض الاساتذة')) {
            throw new PermissionException();
        }

        $teachers = User::where('user_type', 'teacher')
            ->with(['teacher'])
            ->orderBy('id', 'asc')
            ->paginate(15);

        return ResponseHelper::jsonResponse(
            UserResource::collection($teachers),
        );
    }
}
