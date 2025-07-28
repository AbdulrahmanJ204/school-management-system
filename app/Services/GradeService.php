<?php

namespace App\Services;

use App\Exceptions\PermissionException;
use App\Helpers\ResponseHelper;
use App\Http\Requests\GradeRequest;
use App\Http\Resources\GradeResource;
use App\Models\Grade;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class GradeService
{
    public function listGrade()
    {
        $grades = Grade::with([
            'createdBy',
//            'sections',
//            'subjectMajors'
        ])
            ->orderBy('created_at', 'desc')
            ->get();

        return ResponseHelper::jsonResponse(
            GradeResource::collection($grades)
        );
    }

    public function createGrade(GradeRequest $request)
    {
        $admin = auth()->user();
        $credentials = $request->validated();
        $credentials['created_by'] = $admin->id;
        $grade = Grade::create($credentials);

        return ResponseHelper::jsonResponse(
            new GradeResource($grade),
            __('messages.grade.created'),
            201,
            true
        );
    }

    public function showGrade(Grade $grade)
    {
        $grade->load([
            'createdBy',
//            'sections',
//            'subjectMajors.subjects',
//            'settingGradeYears.year'
        ]);

        return ResponseHelper::jsonResponse(
            new GradeResource($grade),
        );
    }

    public function updateGrade($request, Grade $grade)
    {
        $grade->update([
            'title' => $request->title,
        ]);

        $grade->load([
            'createdBy',
//            'sections',
//            'subjectMajors'
        ]);

        return ResponseHelper::jsonResponse(
            new GradeResource($grade),
            __('messages.grade.updated'),
        );
    }

    public function destroyGrade(Grade $grade)
    {
//        // Check if grade has related data
//        if ($grade->sections()->exists()) {
//            return response()->json([
//                'message' => 'Cannot delete grade with existing sections'
//            ], Response::HTTP_CONFLICT);
//        }
//
//        if ($grade->subjectMajors()->exists()) {
//            return response()->json([
//                'message' => 'Cannot delete grade with existing subject majors'
//            ], Response::HTTP_CONFLICT);
//        }
//
//        if ($grade->settingGradeYears()->exists()) {
//            return response()->json([
//                'message' => 'Cannot delete grade with existing year settings'
//            ], Response::HTTP_CONFLICT);
//        }

        $grade->delete();

        return ResponseHelper::jsonResponse(
            null,
            __('messages.grade.deleted'),
        );
    }
}
