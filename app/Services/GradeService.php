<?php

namespace App\Services;

use App\Enums\Permissions\GradePermission;
use App\Exceptions\PermissionException;
use App\Helpers\ResponseHelper;
use App\Http\Requests\GradeRequest;
use App\Http\Resources\GradeResource;
use App\Models\Grade;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Illuminate\Support\Facades\Auth;

class GradeService
{
    

    /**
     * @throws PermissionException
     */
    public function listGrade(): JsonResponse
    {
        AuthHelper::authorize(GradePermission::VIEW_GRADES);

        $grades = Grade::with([
            'year',
            'sections',
            'mainSubjects'
        ])
            ->orderBy('created_at', 'desc')
            ->get();

        return ResponseHelper::jsonResponse(
            GradeResource::collection($grades)
        );
    }

    /**
     * @throws PermissionException
     */
    public function listTrashedGrades(): JsonResponse
    {
        AuthHelper::authorize(GradePermission::MANAGE_DELETED_GRADES);

        $grades = Grade::with([
            'year',
            'sections',
            'mainSubjects'
        ])
            ->onlyTrashed()
            ->orderBy('created_at', 'desc')
            ->get();

        return ResponseHelper::jsonResponse(
            GradeResource::collection($grades)
        );
    }

    /**
     * @throws PermissionException
     */
    public function createGrade(GradeRequest $request): JsonResponse
    {
        AuthHelper::authorize(GradePermission::CREATE_GRADE);

        $admin = Auth::user();
        $credentials = $request->validated();
        $credentials['created_by'] = $admin->id;
        $grade = Grade::create($credentials);

        return ResponseHelper::jsonResponse(
            new GradeResource($grade),
            __('messages.grade.created'),
            ResponseAlias::HTTP_CREATED,
            true
        );
    }

    /**
     * @throws PermissionException
     */
    public function showGrade(Grade $grade): JsonResponse
    {
        AuthHelper::authorize(GradePermission::VIEW_GRADE);

        $grade->load([
            'year',
            'sections',
            'mainSubjects.subjects',
            'settingGradeYears.year'
        ]);

        return ResponseHelper::jsonResponse(
            new GradeResource($grade),
        );
    }

    /**
     * @throws PermissionException
     */
    public function updateGrade($request, Grade $grade): JsonResponse
    {
        AuthHelper::authorize(GradePermission::UPDATE_GRADE);

        $grade->update([
            'title' => $request->title,
            'year_id' => $request->year_id,
        ]);

        $grade->load([
            'year',
            'sections',
            'mainSubjects'
        ]);

        return ResponseHelper::jsonResponse(
            new GradeResource($grade),
            __('messages.grade.updated'),
        );
    }

    /**
     * @throws PermissionException
     */
    public function destroyGrade(Grade $grade): JsonResponse
    {
        AuthHelper::authorize(GradePermission::DELETE_GRADE);

        $grade->delete();

        return ResponseHelper::jsonResponse(
            null,
            __('messages.grade.deleted'),
        );
    }

    /**
     * @throws PermissionException
     */
    public function restoreGrade($id): JsonResponse
    {
        AuthHelper::authorize(GradePermission::MANAGE_DELETED_GRADES);

        $grade = Grade::withTrashed()->findOrFail($id);

        if (!$grade->trashed()) {
            return ResponseHelper::jsonResponse(
                null,
                __('messages.grade.not_deleted'),
                ResponseAlias::HTTP_BAD_REQUEST,
                false
            );
        }

        $grade->restore();

        return ResponseHelper::jsonResponse(
            new GradeResource($grade),
            __('messages.grade.restored'),
        );
    }

    /**
     * @throws PermissionException
     */
    public function forceDeleteGrade($id): JsonResponse
    {
        AuthHelper::authorize(GradePermission::MANAGE_DELETED_GRADES);

        $grade = Grade::withTrashed()->findOrFail($id);

        // Check if grade has related data
        if ($grade->sections()->exists()) {
            return ResponseHelper::jsonResponse(
                null,
                __('messages.grade.has_sections'),
                ResponseAlias::HTTP_BAD_REQUEST,
                false
            );
        }

        if ($grade->mainSubjects()->exists()) {
            return ResponseHelper::jsonResponse(
                null,
                __('messages.grade.has_main_subjects'),
                ResponseAlias::HTTP_BAD_REQUEST,
                false
            );
        }

        if ($grade->settingGradeYears()->exists()) {
            return ResponseHelper::jsonResponse(
                null,
                __('messages.grade.has_year_settings'),
                ResponseAlias::HTTP_BAD_REQUEST,
                false
            );
        }

        $grade->forceDelete();

        return ResponseHelper::jsonResponse(
            null,
            __('messages.grade.force_deleted'),
        );
    }
}
