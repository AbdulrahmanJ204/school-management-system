<?php

namespace App\Http\Controllers;

use App\Services\TeacherService;

class TeacherController extends Controller
{
    protected $teacherService;
    public function __construct(TeacherService $teacherService)
    {
        $this->teacherService = $teacherService;
    }

    public function show()
    {
        return $this->teacherService->listTeachers();
    }
}
