<?php

namespace App\Http\Controllers;

use App\Http\Requests\GradeYearSettingRequest;
use App\Models\GradeYearSetting;
use App\Services\GradeYearSettingService;
use Illuminate\Http\Request;

class GradeYearSettingController extends Controller
{
    protected GradeYearSettingService $gradeYearSettingService;

    public function __construct(GradeYearSettingService $gradeYearSettingService)
    {
        $this->gradeYearSettingService = $gradeYearSettingService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return $this->gradeYearSettingService->listGradeYearSettings();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(GradeYearSettingRequest $request)
    {
        return $this->gradeYearSettingService->createGradeYearSetting($request);
    }

    /**
     * Display the specified resource.
     */
    public function show(GradeYearSetting $gradeYearSetting)
    {
        return $this->gradeYearSettingService->showGradeYearSetting($gradeYearSetting);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(GradeYearSettingRequest $request, GradeYearSetting $gradeYearSetting)
    {
        return $this->gradeYearSettingService->updateGradeYearSetting($request, $gradeYearSetting);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(GradeYearSetting $gradeYearSetting)
    {
        return $this->gradeYearSettingService->destroyGradeYearSetting($gradeYearSetting);
    }

    /**
     * Display a listing of trashed resources.
     */
    public function trashed()
    {
        return $this->gradeYearSettingService->listTrashedGradeYearSettings();
    }

    /**
     * Restore the specified resource from storage.
     */
    public function restore($id)
    {
        return $this->gradeYearSettingService->restoreGradeYearSetting($id);
    }

    /**
     * Force delete the specified resource from storage.
     */
    public function forceDelete($id)
    {
        return $this->gradeYearSettingService->forceDeleteGradeYearSetting($id);
    }

    /**
     * Get settings by grade.
     */
    public function getByGrade($gradeId)
    {
        return $this->gradeYearSettingService->getSettingsByGrade($gradeId);
    }

    /**
     * Get settings by year.
     */
    public function getByYear($yearId)
    {
        return $this->gradeYearSettingService->getSettingsByYear($yearId);
    }
} 