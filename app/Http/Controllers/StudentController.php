<?php

namespace App\Http\Controllers;

use App\Services\StudentService;

class StudentController extends Controller
{
    protected $studentService;
    public function __construct(StudentService $studentService)
    {
        $this->studentService = $studentService;
    }

    public function show()
    {
        return $this->studentService->listStudents();
    }
}
