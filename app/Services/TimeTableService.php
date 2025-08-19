<?php

namespace App\Services;

use App\Enums\Permissions\TimetablePermission;
use App\Exceptions\TimetableNotFoundException;
use App\Helpers\AuthHelper;
use App\Helpers\ResponseHelper;
use App\Http\Resources\TimeTableResource;
use App\Models\TimeTable;
use Illuminate\Support\Facades\DB;

class TimeTableService
{
    public function create($request)
    {
        $user = auth()->user();

        AuthHelper::authorize(TimetablePermission::create_timetable->value);

        $credentials = $request->validated();

        try {
            DB::beginTransaction();

            $timetable = Timetable::create([
                'valid_from' => $credentials['valid_from'],
                'valid_to'   => $credentials['valid_to'],
                'is_active'  => $credentials['is_active'],
                'created_by' => $user->id,
            ]);

            DB::commit();

            return ResponseHelper::jsonResponse(
                new TimetableResource($timetable),
                __('messages.timetable.created'),
                201
            );
        } catch (\Throwable $e) {
            DB::rollBack();
            return $e->getMessage();
        }
    }

    public function update($request, $id)
    {
        AuthHelper::authorize(TimetablePermission::update_timetable->value);

        $timetable = Timetable::find($id);

        if (!$timetable) {
            throw new TimetableNotFoundException();
        }

        $credentials = $request->validated();

        try {
            DB::beginTransaction();

            $timetable->update([
                'valid_from' => $credentials['valid_from'] ?? $timetable->valid_from,
                'valid_to'   => $credentials['valid_to'] ?? $timetable->valid_to,
                'is_active'  => $credentials['is_active'] ?? $timetable->is_active,
            ]);

            DB::commit();

            return ResponseHelper::jsonResponse(
                new TimetableResource($timetable),
                __('messages.timetable.updated'),
                200
            );

        } catch (\Throwable $e) {
            DB::rollBack();
            return $e->getMessage();
        }
    }
    public function delete($id)
    {
        AuthHelper::authorize(TimetablePermission::delete_timetable->value);

        $timetable = Timetable::find($id);
        if (!$timetable) {
            throw new TimetableNotFoundException();
        }

        try {
            DB::beginTransaction();

            $timetable->delete();

            DB::commit();

            return ResponseHelper::jsonResponse(
                null,
                __('messages.timetable.deleted'),
                200
            );

        } catch (\Throwable $e) {
            DB::rollBack();
            return $e->getMessage();
        }
    }
    public function get($id)
    {
        AuthHelper::authorize(TimetablePermission::get_timetable->value);

        $timetable = Timetable::find($id);

        if (!$timetable) {
            throw new TimetableNotFoundException();
        }

        return ResponseHelper::jsonResponse(
            new TimetableResource($timetable),
            __('messages.timetable.get'),
            200
        );
    }
    public function list()
    {
        AuthHelper::authorize(TimetablePermission::list_timetable->value);

        $timetables = Timetable::latest()->paginate(10);

        return ResponseHelper::jsonResponse(
            TimetableResource::collection($timetables),
            __('messages.timetable.list'),
            200
        );
    }
}
