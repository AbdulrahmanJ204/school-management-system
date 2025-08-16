<?php

namespace App\Services;

use App\Enums\Permissions\TimetablePermission;
use App\Exceptions\SchoolShiftNotFoundException;
use App\Helpers\AuthHelper;
use App\Helpers\ResponseHelper;
use App\Http\Resources\SchoolShiftResource;
use App\Models\SchoolShift;
use App\Models\Section;
use Illuminate\Support\Facades\DB;

class SchoolShiftService
{
    public function create($request)
    {
        $user = auth()->user();

        AuthHelper::authorize(TimetablePermission::create->value);

        $credentials = $request->validated();

        $overlappingShift = SchoolShift::where(function($query) use ($credentials) {
            $query->where(function($q) use ($credentials) {
                $q->where('start_time', '<', $credentials['end_time'])
                    ->where('end_time', '>', $credentials['start_time']);
            });
        })->first();

        if ($overlappingShift) {
            return ResponseHelper::jsonResponse(
                null,
                __('messages.school_shift.overlapped'),
                201
            );
        }

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
            $gradeId = $target['grade_id'];

            // If sections is missing or empty → get all sections for this grade
            $sections = !empty($target['sections'])
                ? $target['sections']
                : Section::where('grade_id', $gradeId)->pluck('id')->toArray();

            foreach ($sections as $sectionId) {
                $shift->targets()->create([
                    'grade_id'  => $gradeId,
                    'section_id'=> $sectionId,
                ]);
            }
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

        // Check for overlapping shifts (exclude the current one)
        $overlappingShift = SchoolShift::where('id', '!=', $id)
            ->where(function($query) use ($credentials) {
                $query->where('start_time', '<', $credentials['end_time'])
                    ->where('end_time', '>', $credentials['start_time']);
            })
            ->first();

        if ($overlappingShift) {
            return ResponseHelper::jsonResponse(
                null,
                __('messages.school_shift.overlapped'),
                201
            );
        }

        try {
            DB::beginTransaction();

            // Update main shift fields
            $schoolShift->update([
                'name'       => $credentials['name'] ?? $schoolShift->name,
                'start_time' => $credentials['start_time'] ?? $schoolShift->start_time,
                'end_time'   => $credentials['end_time'] ?? $schoolShift->end_time,
                'is_active'  => $credentials['is_active'] ?? $schoolShift->is_active,
            ]);

            // Sync targets if provided
            if (isset($credentials['targets'])) {
                $existingIds = [];

                foreach ($credentials['targets'] as $target) {
                    $gradeId = $target['grade_id'];

                    // Use provided sections or all sections for the grade
                    $sections = !empty($target['sections'])
                        ? $target['sections']
                        : Section::where('grade_id', $gradeId)->pluck('id')->toArray();

                    foreach ($sections as $sectionId) {
                        $existing = $schoolShift->targets()
                            ->where('grade_id', $gradeId)
                            ->where('section_id', $sectionId)
                            ->first();

                        if (!$existing) {
                            $newTarget = $schoolShift->targets()->create([
                                'grade_id'  => $gradeId,
                                'section_id'=> $sectionId,
                            ]);
                            $existingIds[] = $newTarget->id;
                        } else {
                            $existingIds[] = $existing->id;
                        }
                    }
                }

                // Remove any old targets not included in this update
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
            throw $e; // Re-throw so your global handler can catch it
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
