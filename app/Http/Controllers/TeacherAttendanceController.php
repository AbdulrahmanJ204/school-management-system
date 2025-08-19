<?php

namespace App\Http\Controllers;

use App\Http\Requests\TeacherAttendance\ListTeacherAttendanceRequest;
use App\Http\Requests\TeacherAttendance\StoreTeacherAttendanceRequest;
use App\Http\Requests\TeacherAttendance\UpdateTeacherAttendanceRequest;
use App\Models\TeacherAttendance;
use App\Services\TeacherAttendanceService;
use Illuminate\Http\JsonResponse;

class TeacherAttendanceController extends Controller
{
    protected $teacherAttendanceService;

    public function __construct(TeacherAttendanceService $teacherAttendanceService)
    {
        $this->teacherAttendanceService = $teacherAttendanceService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(ListTeacherAttendanceRequest $request): ?JsonResponse
    {
        return $this->teacherAttendanceService->listTeacherAttendances($request);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTeacherAttendanceRequest $request)
    {
        return $this->teacherAttendanceService->createTeacherAttendance($request);
    }

    /**
     * Display the specified resource.
     */
    public function show($teacherAttendanceId): JsonResponse
    {
        return $this->teacherAttendanceService->showTeacherAttendance($teacherAttendanceId);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTeacherAttendanceRequest $request, TeacherAttendance $teacherAttendance)
    {
        return $this->teacherAttendanceService->updateTeacherAttendance($request, $teacherAttendance->id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TeacherAttendance $teacherAttendance): JsonResponse
    {
        return $this->teacherAttendanceService->deleteTeacherAttendance($teacherAttendance->id);
    }
}
