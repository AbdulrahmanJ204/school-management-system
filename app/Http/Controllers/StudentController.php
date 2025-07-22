<?php

namespace App\Http\Controllers;

use App\Services\YearService;
/**
 * @OA\Get(
 *     path="/students",
 *     tags={"Students"},
 *     summary="Get list of students",
 *     @OA\Response(
 *         response=200,
 *         description="Successful response"
 *     )
 * )
 */
class StudentController extends Controller
{
    protected $studentService;
    public function __construct(YearService $studentService)
    {
        $this->studentService = $studentService;
    }

    public function show()
    {
        return $this->studentService->listStudents();
    }
}
