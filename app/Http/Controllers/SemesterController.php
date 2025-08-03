<?php

namespace App\Http\Controllers;

use App\Http\Requests\SemesterRequest;
use App\Models\Semester;
use App\Services\SemesterService;
use Illuminate\Http\Request;

class SemesterController extends Controller
{
    protected SemesterService $semesterService;
    public function __construct(SemesterService $semesterService)
    {
        $this->semesterService = $semesterService;
    }

    public function store(SemesterRequest $request)
    {
        return $this->semesterService->createSemester($request);
    }

    public function update(Request $request, Semester $semester)
    {
        return $this->semesterService->updateSemester($request, $semester);
    }

    public function destroy(Semester $semester)
    {
        return $this->semesterService->destroySemester($semester);
    }

    public function forceDelete($id)
    {
        return $this->semesterService->forceDeleteSemester($id);
    }

    public function restore($id)
    {
        return $this->semesterService->restoreSemester($id);
    }

    public function Active(Semester $semester)
    {
        return $this->semesterService->ActiveSemester($semester);
    }
}
