<?php

namespace App\Http\Controllers;

use App\Exceptions\PermissionException;
use App\Helpers\ResponseHelper;
use App\Http\Requests\SchoolDayRequest;
use App\Models\SchoolDay;
use App\Models\Semester;
use App\Services\SchoolDayService;
use Illuminate\Http\JsonResponse;

class SchoolDayController extends Controller
{
    protected SchoolDayService $schoolDayService;

    public function __construct(SchoolDayService $schoolDayService)
    {
        $this->schoolDayService = $schoolDayService;
    }

    /**
     * Display a listing of the resource.
     * @throws PermissionException
     */
    public function index(Semester $semester): JsonResponse
    {
        return $this->schoolDayService->listSchoolDay($semester);
    }

    /**
     * Display a listing of trashed resources.
     * @throws PermissionException
     */
    public function trashed(Semester $semester): JsonResponse
    {
        return $this->schoolDayService->listTrashedSchoolDays($semester);
    }

    /**
     * Store a newly created resource in storage.
     * @throws PermissionException
     */
    public function store(SchoolDayRequest $request): JsonResponse
    {
        return $this->schoolDayService->createSchoolDay($request);
    }

//    /**
//     * @throws PermissionException
//     */
//    public function show(SchoolDay $schoolDay): JsonResponse
//    {
//        return $this->schoolDayService->showSchoolDay($schoolDay);
//    }

    /**
     * Update the specified resource in storage.
     * @throws PermissionException
     */
    public function update(SchoolDayRequest $request, SchoolDay $schoolDay): JsonResponse
    {
        return $this->schoolDayService->updateSchoolDay($request, $schoolDay);
    }

    /**
     * Remove the specified resource from storage.
     * @throws PermissionException
     */
    public function destroy(SchoolDay $schoolDay): JsonResponse
    {
        return $this->schoolDayService->destroySchoolDay($schoolDay);
    }

    /**
     * Force delete the specified resource from storage.
     * @throws PermissionException
     */
    public function forceDelete($id): JsonResponse
    {
        return $this->schoolDayService->forceDeleteSchoolDay($id);
    }

    /**
     * Restore the specified resource from storage.
     * @throws PermissionException
     */
    public function restore($id): JsonResponse
    {
        return $this->schoolDayService->restoreSchoolDay($id);
    }
}
