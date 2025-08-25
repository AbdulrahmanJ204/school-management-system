<?php

namespace App\Http\Controllers;

use App\Services\StudentExamService;
use Illuminate\Http\JsonResponse;

class StudentExamController extends Controller
{
    protected StudentExamService $studentExamService;

    public function __construct(StudentExamService $studentExamService)
    {
        $this->studentExamService = $studentExamService;
    }

    /**
     * Get all exams and quizzes for authenticated student
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $userId = auth()->id();
        
        return $this->studentExamService->getStudentExams($userId);
    }
}
