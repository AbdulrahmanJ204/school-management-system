<?php

namespace App\Http\Controllers;

use App\Exceptions\PermissionException;
use App\Http\Requests\SemesterRequest;
use App\Models\Semester;
use App\Services\SemesterService;
use Illuminate\Http\JsonResponse;

class SemesterController extends Controller
{
    protected SemesterService $semesterService;
    public function __construct(SemesterService $semesterService)
    {
        $this->semesterService = $semesterService;
    }

    /**
     * @throws PermissionException
     */
    public function store(SemesterRequest $request): JsonResponse
    {
        return $this->semesterService->createSemester($request);
    }

    /**
     * @throws PermissionException
     */
    public function update(SemesterRequest $request, Semester $semester): JsonResponse
    {
        return $this->semesterService->updateSemester($request, $semester);
    }

    /**
     * @throws PermissionException
     */
    public function destroy(Semester $semester): JsonResponse
    {
        return $this->semesterService->destroySemester($semester);
    }

    /**
     * @throws PermissionException
     */
    public function forceDelete($id): JsonResponse
    {
        return $this->semesterService->forceDeleteSemester($id);
    }

    /**
     * @throws PermissionException
     */
    public function restore($id): JsonResponse
    {
        return $this->semesterService->restoreSemester($id);
    }

    /**
     * @throws PermissionException
     */
    public function Active(Semester $semester): JsonResponse
    {
        return $this->semesterService->ActiveSemester($semester);
    }
}
