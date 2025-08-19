<?php

namespace App\Services;

use App\Enums\Permissions\TimetablePermission;
use App\Exceptions\ScheduleNotFoundException;
use App\Helpers\AuthHelper;
use App\Models\Schedule;
use App\Helpers\ResponseHelper;
use App\Http\Resources\ScheduleResource;
use Illuminate\Support\Facades\DB;

class ScheduleService
{
    public function create($request)
    {
        $user = auth()->user();

        AuthHelper::authorize(TimetablePermission::create_schedule->value);

        $credentials = $request->validated();

        try {
            DB::beginTransaction();

            $schedule = Schedule::create([
                'class_period_id'            => $credentials['class_period_id'],
                'teacher_section_subject_id' => $credentials['teacher_section_subject_id'],
                'timetable_id'               => $credentials['timetable_id'],
                'week_day'                   => $credentials['week_day'],
                'created_by'                 => $user->id,
            ]);

            DB::commit();

            return ResponseHelper::jsonResponse(
                new ScheduleResource($schedule),
                __('messages.schedule.created'),
                200
            );

        } catch (\Throwable $e) {
            DB::rollBack();
            return $e->getMessage();
        }
    }
    public function update($request, $id)
    {
        AuthHelper::authorize(TimetablePermission::update_schedule->value);

        $credentials = $request->validated();

        $schedule = Schedule::find($id);

        if (!$schedule) {
            throw new ScheduleNotFoundException();
        }

        try {
            DB::beginTransaction();

            $schedule->update($credentials);

            DB::commit();

            return ResponseHelper::jsonResponse(
                new ScheduleResource($schedule),
                __('messages.schedule.updated'),
                200
            );
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
    public function delete(int $id)
    {
        AuthHelper::authorize(TimetablePermission::delete_schedule->value);

        $schedule = Schedule::find($id);

        if (!$schedule) {
            throw new ScheduleNotFoundException();
        }

        try {
            DB::beginTransaction();

            $schedule->delete();

            DB::commit();

            return ResponseHelper::jsonResponse(
                null,
                __('messages.schedule.deleted'),
                200
            );
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
    public function get(int $id)
    {
        AuthHelper::authorize(TimetablePermission::get_schedule->value);

        $schedule = Schedule::with(['classPeriod', 'teacherSectionSubject', 'timetable'])
            ->find($id);

        if (!$schedule) {
            return ResponseHelper::jsonResponse(
                null,
                __('messages.schedule.not_found'),
                404
            );
        }

        return ResponseHelper::jsonResponse(
            new ScheduleResource($schedule),
            __('messages.schedule.get'),
            200
        );
    }

    public function list()
    {
        AuthHelper::authorize(TimetablePermission::list_schedule->value);

        $schedules = Schedule::with(['classPeriod', 'teacherSectionSubject', 'timetable'])
            ->latest()
            ->get();

        return ResponseHelper::jsonResponse(
            ScheduleResource::collection($schedules),
            __('messages.schedule.list'),
            200
        );
    }
}
