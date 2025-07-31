<?php

namespace App\Services;

use App\Exceptions\PermissionException;
use App\Helpers\ResponseHelper;
use App\Http\Requests\SectionRequest;
use App\Http\Resources\UserResource;
use App\Http\Resources\SectionResource;
use App\Models\User;
use App\Models\Section;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class SectionService
{
    public function listSection()
    {
        $sections = Section::with(['createdBy', 'grade'])
            ->orderBy('title', 'asc')
            ->get();

        return ResponseHelper::jsonResponse(
            SectionResource::collection($sections)
        );
    }

    public function createSection(SectionRequest $request)
    {
        $admin = auth()->user();
        $credentials = $request->validated();
        $credentials['created_by'] = $admin->id;
        $section = Section::create($credentials);

        $section->load(['createdBy', 'grade']);

        return ResponseHelper::jsonResponse(
            new SectionResource($section),
            __('messages.section.created'),
            201,
            true
        );
    }

    public function showSection(Section $section)
    {
        $section->load([
            'createdBy',
            'grade',
//            'studentEnrollments.student',
//            'teacherSectionSubjects.teacher'
        ]);

        return ResponseHelper::jsonResponse(
            new SectionResource($section),
        );
    }

    public function updateSection(SectionRequest $request, Section $section)
    {
        $section->update($request->validated());

        $section->load(['createdBy', 'grade']);

        return ResponseHelper::jsonResponse(
            new SectionResource($section),
            __('messages.section.updated'),
        );
    }

    public function destroySection(Section $section)
    {
//        // Check if section has related data
//        if ($section->studentEnrollments()->exists()) {
//            return response()->json([
//                'message' => 'Cannot delete section with existing student enrollments'
//            ], Response::HTTP_CONFLICT);
//        }
//
//        if ($section->teacherSectionSubjects()->exists()) {
//            return response()->json([
//                'message' => 'Cannot delete section with existing teacher assignments'
//            ], Response::HTTP_CONFLICT);
//        }

        $section->delete();

        return ResponseHelper::jsonResponse(
            null,
            __('messages.section.deleted'),
        );
    }

    public function listTrashedSections()
    {
        $sections = Section::with(['createdBy', 'grade'])
            ->onlyTrashed()
            ->orderBy('title', 'asc')
            ->get();

        return ResponseHelper::jsonResponse(
            SectionResource::collection($sections)
        );
    }

    public function restoreSection($id)
    {
        $section = Section::withTrashed()->findOrFail($id);
        
        if (!$section->trashed()) {
            return ResponseHelper::jsonResponse(
                null,
                'Section is not deleted',
                400,
                false
            );
        }

        $section->restore();

        return ResponseHelper::jsonResponse(
            new SectionResource($section),
            __('messages.section.restored'),
        );
    }

    public function forceDeleteSection($id)
    {
        $section = Section::withTrashed()->findOrFail($id);
        
        // Check if section has related data
        if ($section->studentEnrollments()->exists()) {
            return ResponseHelper::jsonResponse(
                null,
                __('messages.section.has_students'),
                400,
                false
            );
        }

        if ($section->quizTargets()->exists()) {
            return ResponseHelper::jsonResponse(
                null,
                __('messages.section.has_quiz_targets'),
                400,
                false
            );
        }

        $section->forceDelete();

        return ResponseHelper::jsonResponse(
            null,
            __('messages.section.force_deleted'),
        );
    }
}
