<?php

namespace App\Services;

use App\Enums\Permissions\TimetablePermission;
use App\Exceptions\ScheduleNotFoundException;
use App\Helpers\AuthHelper;
use App\Http\Resources\ScheduleCollection;
use App\Models\ClassPeriod;
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

        $classPeriod = ClassPeriod::findOrFail($credentials['class_period_id']);

        if ($classPeriod->type === 'break') {
            return ResponseHelper::jsonResponse(
                null,
                __('messages.schedule.cant_be_created'),
                400
            );
        }

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

        $classPeriod = ClassPeriod::findOrFail($credentials['class_period_id']);

        if ($classPeriod->type === 'break') {
            return ResponseHelper::jsonResponse(
                null,
                __('messages.schedule.cant_be_created'),
                400
            );
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
    public function get($request)
    {
        AuthHelper::authorize(TimetablePermission::get_schedule->value);

        $gradeId   = $request->query('grade_id');
        $sectionId = $request->query('section_id');
        $timetableId   = $request->query('grade_id');

        $query = Schedule::with([
            'classPeriod',
            'teacherSectionSubject.teacher',
            'teacherSectionSubject.section.grade',
            'teacherSectionSubject.subject'
        ]);

        if ($gradeId) {
            $query->whereHas('teacherSectionSubject.section', fn($q) => $q->where('grade_id', $gradeId));
        }

        if ($sectionId) {
            $query->whereHas('teacherSectionSubject.section', fn($q) => $q->where('id', $sectionId));
        }

        if ($timetableId) {
            $query->where('timetable_id', $timetableId);
        }

        $schedules = $query
            ->join('class_periods', 'schedules.class_period_id', '=', 'class_periods.id')
            ->orderBy('schedules.week_day')
            ->orderBy('class_periods.start_time')
            ->select('schedules.*')
            ->get();

        return ResponseHelper::jsonResponse(
            new ScheduleCollection($schedules),
            __('messages.schedule.listed'),
            200
        );
    }

    public function list($request)
    {
        AuthHelper::authorize(TimetablePermission::list_schedule->value);

        $timetableId   = $request->query('grade_id');

        $schedules = Schedule::with([
            'classPeriod',
            'teacherSectionSubject.teacher',
            'teacherSectionSubject.section.grade',
            'teacherSectionSubject.subject'
        ])
            ->where('timetable_id', $timetableId)
            ->get();

        return ResponseHelper::jsonResponse(
            new ScheduleCollection($schedules),
            __('messages.schedule.listed'),
            200
        );
    }
}
