<?php

namespace App\Services;

use App\Enums\Permissions\TimetablePermission;
use App\Exceptions\ClassPeriodNotFoundException;
use App\Exceptions\PermissionException;
use App\Helpers\AuthHelper;
use App\Helpers\ResponseHelper;
use App\Http\Resources\ClassPeriodResource;
use App\Models\ClassPeriod;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Throwable;
use Illuminate\Support\Facades\Auth;

class ClassPeriodService
{
    /**
     * @throws PermissionException
     */
    public function create($request): JsonResponse
    {
        $user = Auth::user();

        AuthHelper::authorize(TimetablePermission::create_class_period->value);

        $credentials = $request->validated();

        $start = Carbon::parse($credentials['start_time']);
        $end   = Carbon::parse($credentials['end_time']);
        $durationMinutes = $start->diffInMinutes($end);

        try {
            DB::beginTransaction();

            $classPeriod = ClassPeriod::create([
                'name'             => $credentials['name'],
                'start_time'       => $credentials['start_time'],
                'end_time'         => $credentials['end_time'],
                'school_shift_id'  => $credentials['school_shift_id'],
                'period_order'     => $credentials['period_order'],
                'type'             => $credentials['type'],
                'duration_minutes' => $durationMinutes,
                'created_by'       => $user->id,
            ]);

            DB::commit();

            return ResponseHelper::jsonResponse(
                new ClassPeriodResource($classPeriod),
                __('messages.class_period.created')
            );

        } catch (Throwable $e) {
            DB::rollBack();
            return ResponseHelper::jsonResponse(
                null,
                $e->getMessage(),
                400,
                false
            );
        }
    }

    /**
     * @throws ClassPeriodNotFoundException
     * @throws PermissionException
     */
    public function update($request, $id): JsonResponse
    {
        AuthHelper::authorize(TimetablePermission::update_class_period->value);

        $classPeriod = ClassPeriod::findOrFail($id);

        if (!$classPeriod) {
            throw new ClassPeriodNotFoundException();
        }

        $credentials = $request->validated();

        $start = Carbon::parse($credentials['start_time'] ?? $classPeriod->start_time);
        $end   = Carbon::parse($credentials['end_time'] ?? $classPeriod->end_time);
        $durationMinutes = $start->diffInMinutes($end);

        try {
            DB::beginTransaction();

            $classPeriod->update([
                'name'             => $credentials['name'] ?? $classPeriod->name,
                'start_time'       => $credentials['start_time'] ?? $classPeriod->start_time,
                'end_time'         => $credentials['end_time'] ?? $classPeriod->end_time,
                'school_shift_id'  => $credentials['school_shift_id'] ?? $classPeriod->school_shift_id,
                'period_order'     => $credentials['period_order'] ?? $classPeriod->period_order,
                'type'             => $credentials['type'] ?? $classPeriod->type,
                'duration_minutes' => $durationMinutes,
            ]);

            DB::commit();

            return ResponseHelper::jsonResponse(
                new ClassPeriodResource($classPeriod),
                __('messages.class_period.updated'),
                201
            );

        } catch (Throwable $e) {
            DB::rollBack();
            return ResponseHelper::jsonResponse(
                null,
                $e->getMessage(),
                400,
                false
            );
        }
    }

    /**
     * @throws PermissionException
     */
    public function list($request): JsonResponse
    {
        AuthHelper::authorize(TimetablePermission::list_class_period->value);

        $query = ClassPeriod::with('schoolShift');

        // Apply school_shift_id filter if provided
        if ($request->has('school_shift_id') && $request->school_shift_id) {
            $query->where('school_shift_id', $request->school_shift_id);
        }

        $classPeriods = $query
            ->orderBy('school_shift_id')
            ->orderBy('period_order')
            ->get();

        return ResponseHelper::jsonResponse(
            ClassPeriodResource::collection($classPeriods),
            __('messages.class_period.list')
        );
    }

    /**
     * @throws ClassPeriodNotFoundException
     * @throws PermissionException
     */
    public function get($id): JsonResponse
    {
        AuthHelper::authorize(TimetablePermission::get_class_period->value);

        $classPeriod = ClassPeriod::with('schoolShift')->find($id);

        if (!$classPeriod) {
            throw new ClassPeriodNotFoundException();
        }

        return ResponseHelper::jsonResponse(
            new ClassPeriodResource($classPeriod),
            __('messages.class_period.get')
        );
    }

    /**
     * @throws ClassPeriodNotFoundException
     * @throws PermissionException
     */
    public function delete($id): JsonResponse
    {
        AuthHelper::authorize(TimetablePermission::delete_class_period->value);

        $classPeriod = ClassPeriod::findOrFail($id);

        if (!$classPeriod) {
            throw new ClassPeriodNotFoundException();
        }

        // Check if the class period can be deleted
        if (!$classPeriod->canBeDeleted()) {
            $reason = $classPeriod->getDeletionBlockReason();
            $message = match($reason) {
                'has_class_sessions' => __('messages.class_period.has_class_sessions'),
                'has_schedules' => __('messages.class_period.has_schedules'),
                default => __('messages.class_period.cannot_delete')
            };
            
            return ResponseHelper::jsonResponse(
                null,
                $message,
                400,
                false
            );
        }

        try {
            DB::beginTransaction();

            $classPeriod->delete();

            DB::commit();

            return ResponseHelper::jsonResponse(
                null,
                __('messages.class_period.deleted')
            );
        } catch (Throwable $e) {
            DB::rollBack();
            return ResponseHelper::jsonResponse(
                null,
                $e->getMessage(),
                400,
                false
            );
        }
    }

    /**
     * Force delete a class period and all related records
     * @throws ClassPeriodNotFoundException
     * @throws PermissionException
     */
    public function forceDelete($id): JsonResponse
    {
        AuthHelper::authorize(TimetablePermission::delete_class_period->value);

        $classPeriod = ClassPeriod::findOrFail($id);

        if (!$classPeriod) {
            throw new ClassPeriodNotFoundException();
        }

        try {
            DB::beginTransaction();

            $classPeriod->forceDelete();

            DB::commit();

            return ResponseHelper::jsonResponse(
                null,
                __('messages.class_period.force_deleted')
            );
        } catch (Throwable $e) {
            DB::rollBack();
            return ResponseHelper::jsonResponse(
                null,
                $e->getMessage(),
                400,
                false
            );
        }
    }
}
