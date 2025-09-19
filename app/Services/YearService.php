<?php

namespace App\Services;

use App\Enums\Permissions\YearPermission;
use App\Exceptions\PermissionException;
use App\Helpers\ResponseHelper;
use App\Http\Requests\YearRequest;
use App\Http\Resources\YearResource;
use App\Models\Year;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Illuminate\Support\Facades\Auth;

class YearService
{
    

    /**
     * @throws PermissionException
     */
    public function listYear(): JsonResponse
    {
        AuthHelper::authorize(YearPermission::VIEW_YEARS);

        $years = Year::with([
            'semesters'
        ])
            ->orderBy('start_date', 'desc')
            ->get();

        return ResponseHelper::jsonResponse(
            YearResource::collection($years)
        );
    }

    /**
     * @throws PermissionException
     */
    public function listTrashedYears(): JsonResponse
    {
        AuthHelper::authorize(YearPermission::MANAGE_DELETED_YEARS);

        $years = Year::with([
            'semesters'
        ])
            ->onlyTrashed()
            ->orderBy('start_date', 'desc')
            ->get();

        return ResponseHelper::jsonResponse(
            YearResource::collection($years)
        );
    }

    /**
     * @throws PermissionException
     */
    public function createYear(YearRequest $request): JsonResponse
    {
        AuthHelper::authorize(YearPermission::CREATE_YEAR);

        $admin = Auth::user();
        $credentials = $request->validated();
        $credentials['created_by'] = $admin->id;
        $year = Year::create($credentials);

        return ResponseHelper::jsonResponse(
            new YearResource($year),
            __('messages.year.created'),
            ResponseAlias::HTTP_CREATED,
        );
    }

    /**
     * @throws PermissionException
     */
    public function showYear(Year $year): JsonResponse
    {
        AuthHelper::authorize(YearPermission::VIEW_YEAR);

        $year->load([
            'semesters.schoolDays',
            'settingGradeYears.grade'
        ]);
        return ResponseHelper::jsonResponse(
            new YearResource($year),
        );
    }

    /**
     * @throws PermissionException
     */
    public function updateYear($request, Year $year): JsonResponse
    {
        AuthHelper::authorize(YearPermission::UPDATE_YEAR);

        if($request->is_active){
            $activeYears = Year::where('is_active',true)->get();
            foreach ($activeYears as $activeYear){
                $activeYear->update(['is_active' => false]);
            }
        }

        $year->update([
            'name' => $request->name,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'is_active' => $request->is_active ?? $year->is_active,
        ]);

        $year->load([
            'semesters'
        ]);

        return ResponseHelper::jsonResponse(
            new YearResource($year),
            __('messages.year.updated'),
        );
    }

    /**
     * @throws PermissionException
     */
    public function destroyYear(Year $year): JsonResponse
    {
        AuthHelper::authorize(YearPermission::DELETE_YEAR);

        // Check if year has related data
        if ($year->semesters()->exists()) {
            return response()->json([
                'message' => 'Cannot delete year with existing semesters'
            ], ResponseAlias::HTTP_CONFLICT);
        }

        $year->delete();

        return ResponseHelper::jsonResponse(
            null,
            __('messages.year.deleted'),
        );
    }

    /**
     * @throws PermissionException
     */
    public function restoreYear($id): JsonResponse
    {
        AuthHelper::authorize(YearPermission::MANAGE_DELETED_YEARS);

        $year = Year::withTrashed()->findOrFail($id);

        if (!$year->trashed()) {
            return ResponseHelper::jsonResponse(
                null,
                'Year is not deleted',
                ResponseAlias::HTTP_BAD_REQUEST,
                false
            );
        }

        $year->restore();

        return ResponseHelper::jsonResponse(
            new YearResource($year),
            __('messages.year.restored'),
        );
    }

    /**
     * @throws PermissionException
     */
    public function forceDeleteYear($id): JsonResponse
    {
        AuthHelper::authorize(YearPermission::MANAGE_DELETED_YEARS);

//        $year = Year::withTrashed()->findOrFail($id);
        $year = Year::findOrFail($id);

        // Check if year has related data
        if ($year->semesters()->exists()) {
            return ResponseHelper::jsonResponse(
                null,
                __('messages.year.has_semesters'),
                ResponseAlias::HTTP_BAD_REQUEST,
                false
            );
        }

        $year->forceDelete();

        return ResponseHelper::jsonResponse(
            null,
            __('messages.year.force_deleted'),
        );
    }

    /**
     * @throws PermissionException
     */
    public function ActiveYear(Year $year): JsonResponse
    {
        AuthHelper::authorize(YearPermission::UPDATE_YEAR);

        $activeYears = Year::where('is_active',true)->get();
        foreach ($activeYears as $activeYear){
            $activeYear->update(['is_active' => false]);
        }
        $year->update(['is_active' => true]);

        return ResponseHelper::jsonResponse(
            new YearResource($year),
        );
    }

    /**
     * @throws PermissionException
     */
    public function getYearsWithNestedData(): JsonResponse
    {
        AuthHelper::authorize(YearPermission::VIEW_YEARS);

        $years = Year::with([
            'semesters',
            'grades.sections',
            'grades.mainSubjects.subjects'
        ])
            ->orderBy('start_date', 'desc')
            ->get();

        return ResponseHelper::jsonResponse(
            YearResource::collection($years),
            'Years with nested data',
            ResponseAlias::HTTP_OK
        );
    }
}
