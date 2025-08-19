<?php

namespace App\Services;

use App\Enums\PermissionEnum;
use App\Exceptions\PermissionException;
use App\Helpers\ResponseHelper;
use App\Http\Requests\SemesterRequest;
use App\Http\Resources\SemesterResource;
use App\Models\Semester;
use App\Traits\HasPermissionChecks;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class SemesterService
{
    use HasPermissionChecks;

    /**
     * @throws PermissionException
     */
    public function listSemesters(): JsonResponse
    {
        $this->checkPermission(PermissionEnum::VIEW_SEMESTERS);

        $semesters = Semester::with([
            'year'
        ])
            ->orderBy('start_date', 'desc')
            ->get();

        return ResponseHelper::jsonResponse(
            SemesterResource::collection($semesters)
        );
    }

    /**
     * @throws PermissionException
     */
    public function listTrashedSemesters(): JsonResponse
    {
        $this->checkPermission(PermissionEnum::MANAGE_DELETED_SEMESTERS);

        $semesters = Semester::with([
            'year'
        ])
            ->onlyTrashed()
            ->orderBy('start_date', 'desc')
            ->get();

        return ResponseHelper::jsonResponse(
            SemesterResource::collection($semesters)
        );
    }

    /**
     * @throws PermissionException
     */
    public function createSemester(SemesterRequest $request): JsonResponse
    {
        $this->checkPermission(PermissionEnum::CREATE_SEMESTER);

        $admin = auth()->user();
        $credentials = $request->validated();
        $credentials['created_by'] = $admin->id;
        $semester = Semester::create($credentials);

        return ResponseHelper::jsonResponse(
            new SemesterResource($semester),
            __('messages.semester.created'),
            ResponseAlias::HTTP_CREATED,
        );
    }

    /**
     * @throws PermissionException
     */
    public function showSemester(Semester $semester): JsonResponse
    {
        $this->checkPermission(PermissionEnum::VIEW_SEMESTER);

        $semester->load([
            'year',
            'schoolDays'
        ]);

        return ResponseHelper::jsonResponse(
            new SemesterResource($semester),
        );
    }

    /**
     * @throws PermissionException
     */
    public function updateSemester($request, $semester): JsonResponse
    {
        $this->checkPermission(PermissionEnum::UPDATE_SEMESTER);

        if($request->is_active){
            $activeSemesters = Semester::where('is_active',true)->get();
            foreach ($activeSemesters as $activeSemester){
                $activeSemester->update(['is_active' => false]);
            }
        }
        $semester->update([
            'name' => $request->name,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'is_active' => $request->is_active ?? $semester->is_active,
        ]);

        return ResponseHelper::jsonResponse(
            new SemesterResource($semester),
            __('messages.semester.updated'),
        );
    }

    /**
     * @throws PermissionException
     */
    public function destroySemester(Semester $semester): JsonResponse
    {
        $this->checkPermission(PermissionEnum::DELETE_SEMESTER);

        // Check if semester has related data
        if ($semester->schoolDays()->exists()) {
            return ResponseHelper::jsonResponse(
                null,
                'Cannot delete semester with existing school days',
                ResponseAlias::HTTP_BAD_REQUEST,
                false
            );
        }

        $semester->delete();

        return ResponseHelper::jsonResponse(
            null,
            __('messages.semester.deleted'),
        );
    }

    /**
     * @throws PermissionException
     */
    public function restoreSemester($id): JsonResponse
    {
        $this->checkPermission(PermissionEnum::MANAGE_DELETED_SEMESTERS);

        $semester = Semester::withTrashed()->findOrFail($id);

        if (!$semester->trashed()) {
            return ResponseHelper::jsonResponse(
                null,
                'Semester is not deleted',
                ResponseAlias::HTTP_BAD_REQUEST,
                false
            );
        }

        $semester->restore();

        return ResponseHelper::jsonResponse(
            new SemesterResource($semester),
            __('messages.semester.restored'),
        );
    }

    /**
     * @throws PermissionException
     */
    public function forceDeleteSemester($id): JsonResponse
    {
        $this->checkPermission(PermissionEnum::MANAGE_DELETED_SEMESTERS);

//        $semester = Semester::withTrashed()->findOrFail($id);
        $semester = Semester::findOrFail($id);

        // Check if semester has related data
        if ($semester->schoolDays()->exists()) {
            return ResponseHelper::jsonResponse(
                null,
                __('messages.semester.has_school_days'),
                ResponseAlias::HTTP_BAD_REQUEST,
                false
            );
        }

        if ($semester->studentEnrollments()->exists()) {
            return ResponseHelper::jsonResponse(
                null,
                __('messages.semester.has_enrollments'),
                ResponseAlias::HTTP_BAD_REQUEST,
                false
            );
        }

        $semester->forceDelete();

        return ResponseHelper::jsonResponse(
            null,
            __('messages.semester.force_deleted'),
        );
    }

    /**
     * @throws PermissionException
     */
    public function ActiveSemester(Semester $semester): JsonResponse
    {
        $this->checkPermission(PermissionEnum::UPDATE_SEMESTER);

        $activeSemesters = Semester::where('is_active',true)->get();
        foreach ($activeSemesters as $activeSemester){
            $activeSemester->update(['is_active' => false]);
        }
        $semester->update(['is_active' => true]);

        return ResponseHelper::jsonResponse(
            new SemesterResource($semester),
        );
    }
}
