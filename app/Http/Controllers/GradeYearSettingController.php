<?php

namespace App\Http\Controllers;

use App\Exceptions\PermissionException;
use App\Http\Requests\GradeYearSettingRequest;
use App\Models\GradeYearSetting;
use App\Services\GradeYearSettingService;
use Illuminate\Http\JsonResponse;

class GradeYearSettingController extends Controller
{
    protected GradeYearSettingService $gradeYearSettingService;

    public function __construct(GradeYearSettingService $gradeYearSettingService)
    {
        $this->gradeYearSettingService = $gradeYearSettingService;
    }

    /**
     * Display a listing of the resource.
     * @throws PermissionException
     */
    public function index(): JsonResponse
    {
        return $this->gradeYearSettingService->listGradeYearSettings();
    }

    /**
     * Store a newly created resource in storage.
     * @throws PermissionException
     */
    public function store(GradeYearSettingRequest $request): JsonResponse
    {
        return $this->gradeYearSettingService->createGradeYearSetting($request);
    }

    /**
     * Display the specified resource.
     * @throws PermissionException
     */
    public function show(GradeYearSetting $gradeYearSetting): JsonResponse
    {
        return $this->gradeYearSettingService->showGradeYearSetting($gradeYearSetting);
    }

    /**
     * Update the specified resource in storage.
     * @throws PermissionException
     */
    public function update(GradeYearSettingRequest $request, GradeYearSetting $gradeYearSetting): JsonResponse
    {
        return $this->gradeYearSettingService->updateGradeYearSetting($request, $gradeYearSetting);
    }

    /**
     * Remove the specified resource from storage.
     * @throws PermissionException
     */
    public function destroy(GradeYearSetting $gradeYearSetting): JsonResponse
    {
        return $this->gradeYearSettingService->destroyGradeYearSetting($gradeYearSetting);
    }

    /**
     * Display a listing of trashed resources.
     * @throws PermissionException
     */
    public function trashed(): JsonResponse
    {
        return $this->gradeYearSettingService->listTrashedGradeYearSettings();
    }

    /**
     * Restore the specified resource from storage.
     * @throws PermissionException
     */
    public function restore($id): JsonResponse
    {
        return $this->gradeYearSettingService->restoreGradeYearSetting($id);
    }

    /**
     * Force delete the specified resource from storage.
     * @throws PermissionException
     */
    public function forceDelete($id): JsonResponse
    {
        return $this->gradeYearSettingService->forceDeleteGradeYearSetting($id);
    }

    /**
     * Get settings by grade.
     * @throws PermissionException
     */
    public function getByGrade($gradeId): JsonResponse
    {
        return $this->gradeYearSettingService->getSettingsByGrade($gradeId);
    }

    /**
     * Get settings by year.
     * @throws PermissionException
     */
    public function getByYear($yearId): JsonResponse
    {
        return $this->gradeYearSettingService->getSettingsByYear($yearId);
    }
}
