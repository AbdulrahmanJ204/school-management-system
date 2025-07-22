<?php

namespace App\Services;

use App\Helpers\ResponseHelper;
use App\Http\Requests\SchoolDayRequest;
use App\Http\Resources\SchoolDayResource;
use App\Models\SchoolDay;
use Illuminate\Http\Response;

class SchoolDayService
{
    public function listSchoolDay()
    {
        $schoolDays = SchoolDay::with(['createdBy', 'semester'])
            ->orderBy('date', 'desc')
            ->get();

        return ResponseHelper::jsonResponse(
            SchoolDayResource::collection($schoolDays)
        );
    }

    public function createSchoolDay(SchoolDayRequest $request)
    {
        $admin = auth()->user();
        $credentials = $request->validated();
        $credentials['created_by'] = $admin->id;
        $schoolDay = SchoolDay::create($credentials);

        return ResponseHelper::jsonResponse(
            new SchoolDayResource($schoolDay),
            __('messages.school_day.created'),
            201,
            true
        );
    }

//        todo after (behaviorNotes, behaviorNotes, assignments, studentAttendances, teacherAttendances, news)

//    public function showSchoolDay(SchoolDay $schoolDay)
//    {
//        $schoolDay->load([
//            'createdBy',
//            'semester.year',
//            'assignments.subject',
//            'behaviorNotes.student',
//            'studyNotes.student',
//            'studentAttendances.student',
//            'teacherAttendances.teacher',
//            'news'
//        ]);
//
//        return ResponseHelper::jsonResponse(
//            new SchoolDayResource($schoolDay),
//        );
//    }

    public function updateSchoolDay($request, SchoolDay $schoolDay)
    {
        $schoolDay->update([
            'date' => $request->date,
            'semester_id' => $request->semester_id,
            'type' => $request->type,
        ]);

        $schoolDay->load(['createdBy', 'semester']);

        return ResponseHelper::jsonResponse(
            new SchoolDayResource($schoolDay),
            __('messages.school_day.updated'),
        );
    }

    public function destroySchoolDay(SchoolDay $schoolDay)
    {
        // Check if school day has related data
        if ($schoolDay->assignments()->exists() ||
            $schoolDay->behaviorNotes()->exists() ||
            $schoolDay->studyNotes()->exists() ||
            $schoolDay->studentAttendances()->exists() ||
//            $schoolDay->news()->exists() ||
            $schoolDay->teacherAttendances()->exists()) {
            return response()->json([
                'message' => 'Cannot delete school day with existing related data'
            ], 400);
        }

        $schoolDay->delete();

        return ResponseHelper::jsonResponse(
            null,
            __('messages.school_day.deleted'),
        );
    }
}
