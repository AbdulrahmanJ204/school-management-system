<?php

namespace App\Services;

use App\Enums\Permissions\TimetablePermission;
use App\Exceptions\SchoolShiftNotFoundException;
use App\Helpers\AuthHelper;
use App\Helpers\ResponseHelper;
use App\Http\Resources\SchoolShiftResource;
use App\Models\SchoolShift;
use Illuminate\Support\Facades\DB;

class SchoolShiftService
{
    public function create($request)
    {
        $user = auth()->user();

        AuthHelper::authorize(TimetablePermission::create->value);

        $credentials = $request->validated();
        $credentials['created_by'] = $user->id;

        DB::beginTransaction();

        $shift = SchoolShift::create([
            'name'       => $credentials['name'],
            'start_time' => $credentials['start_time'],
            'end_time'   => $credentials['end_time'],
            'is_active'  => $credentials['is_active'],
            'created_by' => $user->id,
        ]);

        foreach ($credentials['targets'] as $target) {
            $shift->targets()->create([
                'grade_id'   => $target['grade_id'],
                'section_id' => $target['section_id'],
            ]);
        }

        DB::commit();

        return ResponseHelper::jsonResponse(
            new SchoolShiftResource($shift),
            __('messages.school_shift.created'),
            200
        );
    }
    public function update($request, $id)
    {
        AuthHelper::authorize(TimetablePermission::update->value);

        $schoolShift = SchoolShift::with('targets')->find($id);

        if (!$schoolShift) {
            throw new SchoolShiftNotFoundException();
        }

        $credentials = $request->validated();

        try {
            DB::beginTransaction();

            $schoolShift->update([
                'name'       => $credentials['name'] ?? $schoolShift->name,
                'start_time' => $credentials['start_time'] ?? $schoolShift->start_time,
                'end_time'   => $credentials['end_time'] ?? $schoolShift->end_time,
                'is_active'  => $credentials['is_active'] ?? $schoolShift->is_active,
            ]);

            $existingIds = [];

            if (!empty($credentials['targets'])) {
                foreach ($credentials['targets'] as $target) {
                    if (isset($target['id'])) {
                        // Update existing
                        $shiftTarget = $schoolShift->targets()
                            ->where('id', $target['id'])
                            ->first();

                        if ($shiftTarget) {
                            $shiftTarget->update([
                                'grade_id'   => $target['grade_id'],
                                'section_id' => $target['section_id'],
                            ]);
                            $existingIds[] = $shiftTarget->id;
                        }
                    } else {
                        // Create new
                        $newTarget = $schoolShift->targets()->create([
                            'grade_id'   => $target['grade_id'],
                            'section_id' => $target['section_id'],
                        ]);
                        $existingIds[] = $newTarget->id;
                    }
                }

                // Delete removed ones
                $schoolShift->targets()->whereNotIn('id', $existingIds)->delete();
            }

            DB::commit();

            $schoolShift->load('targets');
            return ResponseHelper::jsonResponse(
                new SchoolShiftResource($schoolShift),
                __('messages.school_shift.updated'),
                201
            );
        } catch (\Throwable $e) {
            DB::rollBack();
        }
    }

    public function delete($id)
    {
        AuthHelper::authorize(TimetablePermission::delete->value);

        $schoolShift = SchoolShift::find($id);

        if (!$schoolShift) {
            throw new SchoolShiftNotFoundException();
        }

        try {
            DB::beginTransaction();

            $schoolShift->targets()->delete();

            $schoolShift->delete();

            DB::commit();

            return ResponseHelper::jsonResponse(
                null,
                __('messages.school_shift.deleted'),
                200
            );
        } catch (\Throwable $e) {
            DB::rollBack();
        }
    }
    public function get($id)
    {
        AuthHelper::authorize(TimetablePermission::get->value);

        $schoolShift = SchoolShift::find($id);

        if (!$schoolShift) {
            throw new SchoolShiftNotFoundException();
        }

        return ResponseHelper::jsonResponse(
            new SchoolShiftResource($schoolShift),
            __('messages.school_shift.get'),
            200
        );
    }
    public function list()
    {
        AuthHelper::authorize(TimetablePermission::list->value);

        $shifts = SchoolShift::latest()->get();

        return ResponseHelper::jsonResponse(
            SchoolShiftResource::collection($shifts),
            __('messages.school_shift.list'),
            200
        );
    }
}
