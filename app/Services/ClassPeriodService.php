<?php

namespace App\Services;

use App\Enums\Permissions\TimetablePermission;
use App\Exceptions\ClassPeriodNotFoundException;
use App\Helpers\AuthHelper;
use App\Helpers\ResponseHelper;
use App\Http\Resources\ClassPeriodResource;
use App\Models\ClassPeriod;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ClassPeriodService
{
    public function create($request)
    {
        $user = auth()->user();

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
                __('messages.class_period.created'),
                200
            );

        } catch (\Throwable $e) {
            DB::rollBack();
            return $e->getMessage();
        }
    }

    public function update($request, $id)
    {
        AuthHelper::authorize(TimetablePermission::update_class_period->value);

        $classPeriod = ClassPeriod::find($id);

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

        } catch (\Throwable $e) {
            DB::rollBack();
            return $e->getMessage();
        }
    }
    public function list()
    {
        AuthHelper::authorize(TimetablePermission::list_class_period->value);

        $classPeriods = ClassPeriod::with('schoolShift')
            ->orderBy('school_shift_id')
            ->orderBy('period_order')
            ->get();

        return ResponseHelper::jsonResponse(
            ClassPeriodResource::collection($classPeriods),
            __('messages.class_period.list'),
            200
        );
    }
    public function get($id)
    {
        AuthHelper::authorize(TimetablePermission::get_class_period->value);

        $classPeriod = ClassPeriod::with('schoolShift')->find($id);

        if (!$classPeriod) {
            throw new ClassPeriodNotFoundException();
        }

        return ResponseHelper::jsonResponse(
            new ClassPeriodResource($classPeriod),
            __('messages.class_period.get'),
            200
        );
    }
    public function delete($id)
    {
        AuthHelper::authorize(TimetablePermission::delete_class_period->value);

        $classPeriod = ClassPeriod::find($id);

        if (!$classPeriod) {
            throw new ClassPeriodNotFoundException();
        }

        try {
            DB::beginTransaction();

            $classPeriod->delete();

            DB::commit();

            return ResponseHelper::jsonResponse(
                null,
                __('messages.class_period.deleted'),
                200
            );
        } catch (\Throwable $e) {
            DB::rollBack();
            return $e->getMessage();
        }
    }
}
