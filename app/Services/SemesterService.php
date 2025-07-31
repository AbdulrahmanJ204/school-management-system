<?php

namespace App\Services;

use App\Helpers\ResponseHelper;
use App\Http\Requests\SemesterRequest;
use App\Http\Resources\SemesterResource;
use App\Models\Semester;
use App\Models\Year;
use Illuminate\Http\Response;

class SemesterService
{
    public function listTrashedSemesters()
    {
        $semesters = Semester::with(['createdBy', 'year'])
            ->onlyTrashed()
            ->orderBy('start_date', 'desc')
            ->get();

        return ResponseHelper::jsonResponse(
            SemesterResource::collection($semesters)
        );
    }

    public function createSemester(SemesterRequest $request)
    {
        $admin = auth()->user();
        $credentials = $request->validated();
        $credentials['created_by'] = $admin->id;
        $semester = Semester::create($credentials);

        return ResponseHelper::jsonResponse(
            new SemesterResource($semester),
            __('messages.semester.created'),
            201,
            true
        );
    }

    public function updateSemester($request, $semester)
    {
        $semester->update([
            'name' => $request->name,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'is_active' => $request->is_active ?? $semester->is_active,
        ]);

        $semester->load(['createdBy']);

        return ResponseHelper::jsonResponse(
            new SemesterResource($semester),
            __('messages.semester.updated'),
        );
    }

    public function destroySemester(Semester $semester)
    {
        // Check if semester has related data
        if ($semester->schoolDays()->exists()) {
            return response()->json([
                'message' => 'Cannot delete semester with existing school days'
            ], 400);
        }

        $semester->delete();

        return ResponseHelper::jsonResponse(
            null,
            __('messages.semester.deleted'),
        );
    }

    public function restoreSemester($id)
    {
        $semester = Semester::withTrashed()->findOrFail($id);
        
        if (!$semester->trashed()) {
            return ResponseHelper::jsonResponse(
                null,
                'Semester is not deleted',
                400,
                false
            );
        }

        $semester->restore();

        return ResponseHelper::jsonResponse(
            new SemesterResource($semester),
            __('messages.semester.restored'),
        );
    }

    public function forceDeleteSemester($id)
    {
        $semester = Semester::withTrashed()->findOrFail($id);
        
        // Check if semester has related data
        if ($semester->schoolDays()->exists()) {
            return ResponseHelper::jsonResponse(
                null,
                __('messages.semester.has_school_days'),
                400,
                false
            );
        }

        if ($semester->studentEnrollments()->exists()) {
            return ResponseHelper::jsonResponse(
                null,
                __('messages.semester.has_enrollments'),
                400,
                false
            );
        }

        $semester->forceDelete();

        return ResponseHelper::jsonResponse(
            null,
            __('messages.semester.force_deleted'),
        );
    }

    public function ActiveSemester(Semester $semester)
    {

        $activeYears = Year::where('is_active',true)->get();
        foreach ($activeYears as $activeYear){
            $activeYear->update(['is_active' => false]);
        }
        $year = $semester->year();
        $year->update(['is_active' => true]);

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
