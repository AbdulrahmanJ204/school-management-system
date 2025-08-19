<?php

namespace App\Http\Controllers;

use App\Exceptions\PermissionException;
use App\Services\TeacherService;
use Illuminate\Http\JsonResponse;

class TeacherController extends Controller
{
    protected TeacherService $teacherService;
    public function __construct(TeacherService $teacherService)
    {
        $this->teacherService = $teacherService;
    }

    /**
     * @throws PermissionException
     */
    public function show(): JsonResponse
    {
        return $this->teacherService->listTeachers();
    }
}
