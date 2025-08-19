<?php

namespace App\Http\Controllers;

use App\Services\StudentService;
use App\Http\Requests\GetStudentsBySectionSemesterRequest;

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

    public function getBySectionAndSemester(GetStudentsBySectionSemesterRequest $request)
    {
        return $this->studentService->getStudentsBySectionAndSemester(
            $request->section_id,
            $request->semester_id
        );
    }
}
