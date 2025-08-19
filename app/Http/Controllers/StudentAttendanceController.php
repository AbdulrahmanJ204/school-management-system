<?php

namespace App\Http\Controllers;

use App\Http\Requests\StudentAttendance\ListStudentAttendanceRequest;
use App\Http\Requests\StudentAttendance\StoreStudentAttendanceRequest;
use App\Http\Requests\StudentAttendance\UpdateStudentAttendanceRequest;
use App\Models\StudentAttendance;
use App\Services\StudentAttendanceService;
use Illuminate\Http\JsonResponse;

class StudentAttendanceController extends Controller
{
    protected $studentAttendanceService;

    public function __construct(StudentAttendanceService $studentAttendanceService)
    {
        $this->studentAttendanceService = $studentAttendanceService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(ListStudentAttendanceRequest $request): ?JsonResponse
    {
        return $this->studentAttendanceService->listStudentAttendances($request);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreStudentAttendanceRequest $request)
    {
        return $this->studentAttendanceService->createStudentAttendance($request);
    }

    /**
     * Display the specified resource.
     */
    public function show($studentAttendanceId): JsonResponse
    {
        return $this->studentAttendanceService->showStudentAttendance($studentAttendanceId);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateStudentAttendanceRequest $request, StudentAttendance $studentAttendance)
    {
        return $this->studentAttendanceService->updateStudentAttendance($request, $studentAttendance->id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StudentAttendance $studentAttendance): JsonResponse
    {
        return $this->studentAttendanceService->deleteStudentAttendance($studentAttendance->id);
    }
}
