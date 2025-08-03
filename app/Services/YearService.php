<?php

namespace App\Services;

use App\Enums\PermissionEnum;
use App\Exceptions\PermissionException;
use App\Helpers\ResponseHelper;
use App\Http\Requests\YearRequest;
use App\Http\Resources\UserResource;
use App\Http\Resources\YearResource;
use App\Models\User;
use App\Models\Year;
use App\Traits\HasPermissionChecks;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class YearService
{
    use HasPermissionChecks;

    public function listYear()
    {
        $this->checkPermission(PermissionEnum::VIEW_YEARS);

        $years = Year::with(['createdBy', 'semesters'])
            ->orderBy('start_date', 'desc')
            ->get();

        return ResponseHelper::jsonResponse(
            YearResource::collection($years)
        );
    }

    public function listTrashedYears()
    {
        $this->checkPermission(PermissionEnum::MANAGE_DELETED_YEARS);

        $years = Year::with(['createdBy', 'semesters'])
            ->onlyTrashed()
            ->orderBy('start_date', 'desc')
            ->get();

        return ResponseHelper::jsonResponse(
            YearResource::collection($years)
        );
    }

    public function createYear(YearRequest $request)
    {
        $this->checkPermission(PermissionEnum::CREATE_YEAR);

        $admin = auth()->user();
        $credentials = $request->validated();
        $credentials['created_by'] = $admin->id;
        $year = Year::create($credentials);

        return ResponseHelper::jsonResponse(
            new YearResource($year),
            __('messages.year.created'),
            201,
            true
        );
    }

    public function showYear(Year $year)
    {
        $this->checkPermission(PermissionEnum::VIEW_YEAR);

        $year->load(['createdBy', 'semesters.schoolDays', 'settingGradeYears.grade']);

        return ResponseHelper::jsonResponse(
            new YearResource($year),
        );
    }

    public function updateYear($request,Year $year)
    {
        $this->checkPermission(PermissionEnum::UPDATE_YEAR);

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

        $year->load(['createdBy', 'semesters']);

        return ResponseHelper::jsonResponse(
            new YearResource($year),
            __('messages.year.updated'),
        );
    }

    public function destroyYear(Year $year)
    {
        $this->checkPermission(PermissionEnum::DELETE_YEAR);

        // Check if year has related data
        if ($year->semesters()->exists()) {
            return response()->json([
                'message' => 'Cannot delete year with existing semesters'
            ], Response::HTTP_CONFLICT);
        }

        $year->delete();

        return ResponseHelper::jsonResponse(
            null,
            __('messages.year.deleted'),
        );
    }

    public function restoreYear($id)
    {
        $this->checkPermission(PermissionEnum::MANAGE_DELETED_YEARS);

        $year = Year::withTrashed()->findOrFail($id);

        if (!$year->trashed()) {
            return ResponseHelper::jsonResponse(
                null,
                'Year is not deleted',
                400,
                false
            );
        }

        $year->restore();

        return ResponseHelper::jsonResponse(
            new YearResource($year),
            __('messages.year.restored'),
        );
    }

    public function forceDeleteYear($id)
    {
        $this->checkPermission(PermissionEnum::MANAGE_DELETED_YEARS);

        $year = Year::withTrashed()->findOrFail($id);

        // Check if year has related data
        if ($year->semesters()->exists()) {
            return ResponseHelper::jsonResponse(
                null,
                __('messages.year.has_semesters'),
                400,
                false
            );
        }

        $year->forceDelete();

        return ResponseHelper::jsonResponse(
            null,
            __('messages.year.force_deleted'),
        );
    }

    public function ActiveYear(Year $year)
    {
        $this->checkPermission(PermissionEnum::UPDATE_YEAR);

        $activeYears = Year::where('is_active',true)->get();
        foreach ($activeYears as $activeYear){
            $activeYear->update(['is_active' => false]);
        }
        $year->update(['is_active' => true]);

        return ResponseHelper::jsonResponse(
            new YearResource($year),
        );
    }
}
