<?php

namespace App\Services;

use App\Enums\Permissions\GradeYearSettingPermission;
use App\Helpers\ResponseHelper;
use App\Http\Requests\GradeYearSettingRequest;
use App\Http\Resources\GradeYearSettingResource;
use App\Models\GradeYearSetting;
use App\Models\Grade;
use App\Models\Year;
use App\Exceptions\PermissionException;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class GradeYearSettingService
{
    

    /**
     * Get list of all grade year settings.
     * @throws PermissionException
     */
    public function listGradeYearSettings(): JsonResponse
    {
        AuthHelper::authorize(GradeYearSettingPermission::VIEW_GRADE_YEAR_SETTINGS);

        $settings = GradeYearSetting::with([
            'year',
            'grade',
        ])->orderBy('created_at', 'desc')->get();

        return ResponseHelper::jsonResponse(
            GradeYearSettingResource::collection($settings)
        );
    }

    /**
     * Create a new grade year setting.
     * @throws PermissionException
     */
    public function createGradeYearSetting(GradeYearSettingRequest $request): JsonResponse
    {
        AuthHelper::authorize(GradeYearSettingPermission::CREATE_GRADE_YEAR_SETTING);

        $credentials = $request->validated();
        $credentials['created_by'] = Auth::user()->id;

        // Check if setting already exists for this year and grade combination
        $existingSetting = GradeYearSetting::where('year_id', $credentials['year_id'])
            ->where('grade_id', $credentials['grade_id'])
            ->first();

        if ($existingSetting) {
            return ResponseHelper::jsonResponse(
                null,
                __('messages.grade_year_setting.already_exists'),
                ResponseAlias::HTTP_BAD_REQUEST,
                false
            );
        }

        $setting = GradeYearSetting::create($credentials);
        $setting->load([
            'year',
            'grade',
        ]);

        return ResponseHelper::jsonResponse(
            new GradeYearSettingResource($setting),
            __('messages.grade_year_setting.created'),
            ResponseAlias::HTTP_CREATED,
            true
        );
    }

    /**
     * Show a specific grade year setting.
     * @throws PermissionException
     */
    public function showGradeYearSetting(GradeYearSetting $gradeYearSetting): JsonResponse
    {
        AuthHelper::authorize(GradeYearSettingPermission::VIEW_GRADE_YEAR_SETTING);

        $gradeYearSetting->load([
            'year',
            'grade',
        ]);

        return ResponseHelper::jsonResponse(
            new GradeYearSettingResource($gradeYearSetting)
        );
    }

    /**
     * Update a grade year setting.
     * @throws PermissionException
     */
    public function updateGradeYearSetting(GradeYearSettingRequest $request, GradeYearSetting $gradeYearSetting): JsonResponse
    {
        AuthHelper::authorize(GradeYearSettingPermission::UPDATE_GRADE_YEAR_SETTING);

        $credentials = $request->validated();

        // Check if setting already exists for this year and grade combination (excluding current setting)
        $existingSetting = GradeYearSetting::where('year_id', $credentials['year_id'])
            ->where('grade_id', $credentials['grade_id'])
            ->where('id', '!=', $gradeYearSetting->id)
            ->first();

        if ($existingSetting) {
            return ResponseHelper::jsonResponse(
                null,
                __('messages.grade_year_setting.already_exists'),
                ResponseAlias::HTTP_BAD_REQUEST,
                false
            );
        }

        $gradeYearSetting->update($credentials);
        $gradeYearSetting->load([
            'year',
            'grade',
        ]);

        return ResponseHelper::jsonResponse(
            new GradeYearSettingResource($gradeYearSetting),
            __('messages.grade_year_setting.updated')
        );
    }

    /**
     * Delete a grade year setting.
     * @throws PermissionException
     */
    public function destroyGradeYearSetting(GradeYearSetting $gradeYearSetting): JsonResponse
    {
        AuthHelper::authorize(GradeYearSettingPermission::DELETE_GRADE_YEAR_SETTING);

        $gradeYearSetting->delete();

        return ResponseHelper::jsonResponse(
            null,
            __('messages.grade_year_setting.deleted')
        );
    }

    /**
     * Get list of trashed grade year settings.
     * @throws PermissionException
     */
    public function listTrashedGradeYearSettings(): JsonResponse
    {
        AuthHelper::authorize(GradeYearSettingPermission::MANAGE_DELETED_GRADE_YEAR_SETTINGS);

        $settings = GradeYearSetting::with([
            'year',
            'grade',
        ])->onlyTrashed()->orderBy('created_at', 'desc')->get();

        return ResponseHelper::jsonResponse(
            GradeYearSettingResource::collection($settings)
        );
    }

    /**
     * Restore a trashed grade year setting.
     * @throws PermissionException
     */
    public function restoreGradeYearSetting($id): JsonResponse
    {
        AuthHelper::authorize(GradeYearSettingPermission::MANAGE_DELETED_GRADE_YEAR_SETTINGS);

        $setting = GradeYearSetting::withTrashed()->findOrFail($id);

        if (!$setting->trashed()) {
            return ResponseHelper::jsonResponse(
                null,
                'Grade year setting is not deleted',
                ResponseAlias::HTTP_BAD_REQUEST,
                false
            );
        }

        $setting->restore();
        $setting->load([
            'year',
            'grade',
        ]);

        return ResponseHelper::jsonResponse(
            new GradeYearSettingResource($setting),
            __('messages.grade_year_setting.restored')
        );
    }

    /**
     * Force delete a trashed grade year setting.
     * @throws PermissionException
     */
    public function forceDeleteGradeYearSetting($id): JsonResponse
    {
        AuthHelper::authorize(GradeYearSettingPermission::MANAGE_DELETED_GRADE_YEAR_SETTINGS);

        $setting = GradeYearSetting::withTrashed()->findOrFail($id);

        $setting->forceDelete();

        return ResponseHelper::jsonResponse(
            null,
            __('messages.grade_year_setting.force_deleted')
        );
    }

    /**
     * Get settings by grade.
     * @throws PermissionException
     */
    public function getSettingsByGrade($gradeId): JsonResponse
    {
        AuthHelper::authorize(GradeYearSettingPermission::VIEW_GRADE_YEAR_SETTINGS);

        $settings = GradeYearSetting::where('grade_id', $gradeId)->with([
            'year',
            'grade',
        ])->orderBy('created_at', 'desc')->get();

        return ResponseHelper::jsonResponse(
            GradeYearSettingResource::collection($settings)
        );
    }

    /**
     * Get settings by year.
     * @throws PermissionException
     */
    public function getSettingsByYear($yearId): JsonResponse
    {
        AuthHelper::authorize(GradeYearSettingPermission::VIEW_GRADE_YEAR_SETTINGS);

        $settings = GradeYearSetting::where('year_id', $yearId)->with([
            'year',
            'grade',
        ])->orderBy('created_at', 'desc')->get();

        return ResponseHelper::jsonResponse(
            GradeYearSettingResource::collection($settings)
        );
    }
}
