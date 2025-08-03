<?php

namespace App\Services;

use App\Enums\PermissionEnum;
use App\Exceptions\PermissionException;
use App\Helpers\ResponseHelper;
use App\Http\Requests\SchoolDayRequest;
use App\Http\Resources\SchoolDayResource;
use App\Models\SchoolDay;
use App\Models\Semester;
use App\Traits\HasPermissionChecks;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class SchoolDayService
{
    use HasPermissionChecks;

    /**
     * @throws PermissionException
     */
    public function listSchoolDays(): JsonResponse
    {
        $this->checkPermission(PermissionEnum::VIEW_SCHOOL_DAYS);

        $schoolDays = SchoolDay::with([
//            'semester'
        ])
            ->orderBy('date', 'desc')
            ->get();

        return ResponseHelper::jsonResponse(
            SchoolDayResource::collection($schoolDays)
        );
    }

    /**
     * @throws PermissionException
     */
    public function listTrashedSchoolDays(Semester $semester): JsonResponse
    {
        $this->checkPermission(PermissionEnum::MANAGE_DELETED_SCHOOL_DAYS);

        $schoolDays = SchoolDay::with([
//            'semester'
        ])
            ->where('semester_id', $semester->id)
            ->onlyTrashed()
            ->orderBy('date', 'desc')
            ->get();

        return ResponseHelper::jsonResponse(
            SchoolDayResource::collection($schoolDays)
        );
    }

    /**
     * @throws PermissionException
     */
    public function listSchoolDay(Semester $semester): JsonResponse
    {
        $this->checkPermission(PermissionEnum::VIEW_SCHOOL_DAYS);

        $schoolDays = $semester->schoolDays;

        return ResponseHelper::jsonResponse(
            SchoolDayResource::collection($schoolDays)
        );
    }

    /**
     * @throws PermissionException
     */
    public function createSchoolDay(SchoolDayRequest $request): JsonResponse
    {
        $this->checkPermission(PermissionEnum::CREATE_SCHOOL_DAY);

        $admin = auth()->user();
        $credentials = $request->validated();
        $credentials['created_by'] = $admin->id;
        $schoolDay = SchoolDay::create($credentials);

        return ResponseHelper::jsonResponse(
            new SchoolDayResource($schoolDay),
            __('messages.school_day.created'),
            ResponseAlias::HTTP_CREATED,
            true
        );
    }

//    /**
//     * @throws PermissionException
//     */
//    public function showSchoolDay(SchoolDay $schoolDay): JsonResponse
//    {
//        $this->checkPermission(PermissionEnum::VIEW_SCHOOL_DAY);
//
//        $schoolDay->load([
////            'semester.year',
////            'assignments.subject',
////            'behaviorNotes.student',
////            'studyNotes.student',
////            'studentAttendances.student',
////            'teacherAttendances.teacher',
////            'news'
//        ]);
//
//        return ResponseHelper::jsonResponse(
//            new SchoolDayResource($schoolDay),
//        );
//    }

    /**
     * @throws PermissionException
     */
    public function updateSchoolDay($request, SchoolDay $schoolDay): JsonResponse
    {
        $this->checkPermission(PermissionEnum::UPDATE_SCHOOL_DAY);

        $schoolDay->update([
            'date' => $request->date,
            'semester_id' => $request->semester_id,
            'type' => $request->type,
        ]);

        $schoolDay->load([
//            'semester'
        ]);

        return ResponseHelper::jsonResponse(
            new SchoolDayResource($schoolDay),
            __('messages.school_day.updated'),
        );
    }

    /**
     * @throws PermissionException
     */
    public function destroySchoolDay(SchoolDay $schoolDay): JsonResponse
    {
        $this->checkPermission(PermissionEnum::DELETE_SCHOOL_DAY);

        // Check if school day has related data
        if ($schoolDay->assignments()->exists() ||
            $schoolDay->behaviorNotes()->exists() ||
            $schoolDay->studyNotes()->exists() ||
            $schoolDay->studentAttendances()->exists() ||
            $schoolDay->teacherAttendances()->exists() ||
            $schoolDay->news()->exists()) {
            return response()->json([
                'message' => 'Cannot delete school day with existing related data'
            ], ResponseAlias::HTTP_CONFLICT);
        }

        $schoolDay->delete();

        return ResponseHelper::jsonResponse(
            null,
            __('messages.school_day.deleted'),
        );
    }

    /**
     * @throws PermissionException
     */
    public function restoreSchoolDay($id): JsonResponse
    {
        $this->checkPermission(PermissionEnum::MANAGE_DELETED_SCHOOL_DAYS);

        $schoolDay = SchoolDay::withTrashed()->findOrFail($id);

        if (!$schoolDay->trashed()) {
            return ResponseHelper::jsonResponse(
                null,
                'School day is not deleted',
                ResponseAlias::HTTP_BAD_REQUEST,
                false
            );
        }

        $schoolDay->restore();

        return ResponseHelper::jsonResponse(
            new SchoolDayResource($schoolDay),
            __('messages.school_day.restored'),
        );
    }

    /**
     * @throws PermissionException
     */
    public function forceDeleteSchoolDay($id): JsonResponse
    {
        $this->checkPermission(PermissionEnum::MANAGE_DELETED_SCHOOL_DAYS);

//        $schoolDay = SchoolDay::withTrashed()->findOrFail($id);
        $schoolDay = SchoolDay::findOrFail($id);

        // Check if school day has related data
        if ($schoolDay->assignments()->exists() ||
            $schoolDay->behaviorNotes()->exists() ||
            $schoolDay->studyNotes()->exists() ||
            $schoolDay->studentAttendances()->exists() ||
            $schoolDay->teacherAttendances()->exists() ||
            $schoolDay->news()->exists()) {
            return ResponseHelper::jsonResponse(
                null,
                __('messages.school_day.has_related_data'),
                ResponseAlias::HTTP_BAD_REQUEST,
                false
            );
        }

        $schoolDay->forceDelete();

        return ResponseHelper::jsonResponse(
            null,
            __('messages.school_day.force_deleted'),
        );
    }
}
