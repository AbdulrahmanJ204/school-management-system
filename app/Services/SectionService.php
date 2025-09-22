<?php

namespace App\Services;

use App\Enums\Permissions\SectionPermission;
use App\Exceptions\PermissionException;
use App\Helpers\ResponseHelper;
use App\Http\Requests\SectionRequest;
use App\Http\Resources\SectionResource;
use App\Models\Section;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Illuminate\Support\Facades\Auth;

class SectionService
{
    

    /**
     * @throws PermissionException
     */
    public function listSection(): JsonResponse
    {
        AuthHelper::authorize(SectionPermission::VIEW_SECTIONS);

        $sections = Section::with([
            'grade'
        ])
            ->orderBy('title', 'asc')
            ->get();

        return ResponseHelper::jsonResponse(
            SectionResource::collection($sections)
        );
    }

    /**
     * @throws PermissionException
     */
    public function listTrashedSections(): JsonResponse
    {
        AuthHelper::authorize(SectionPermission::MANAGE_DELETED_SECTIONS);

        $sections = Section::with([
            'grade'
        ])
            ->onlyTrashed()
            ->orderBy('title', 'asc')
            ->get();

        return ResponseHelper::jsonResponse(
            SectionResource::collection($sections)
        );
    }

    /**
     * @throws PermissionException
     */
    public function createSection(SectionRequest $request): JsonResponse
    {
        AuthHelper::authorize(SectionPermission::CREATE_SECTION);

        $admin = Auth::user();
        $credentials = $request->validated();
        $credentials['created_by'] = $admin->id;
        $section = Section::create($credentials);

        $section->load([
            'grade'
        ]);

        return ResponseHelper::jsonResponse(
            new SectionResource($section),
            __('messages.section.created'),
            ResponseAlias::HTTP_CREATED,
            true
        );
    }

    /**
     * @throws PermissionException
     */
    public function showSection(Section $section): JsonResponse
    {
        AuthHelper::authorize(SectionPermission::VIEW_SECTION);

        $section->load([
            'grade',
            'studentEnrollments.student',
            'teacherSectionSubjects.teacher'
        ]);

        return ResponseHelper::jsonResponse(
            new SectionResource($section),
        );
    }

    /**
     * @throws PermissionException
     */
    public function updateSection(SectionRequest $request, Section $section): JsonResponse
    {
        AuthHelper::authorize(SectionPermission::UPDATE_SECTION);

        $section->update($request->validated());

        $section->load([
            'grade'
        ]);

        return ResponseHelper::jsonResponse(
            new SectionResource($section),
            __('messages.section.updated'),
        );
    }

    /**
     * @throws PermissionException
     */
    public function destroySection(Section $section): JsonResponse
    {
        AuthHelper::authorize(SectionPermission::DELETE_SECTION);

        $section->delete();

        return ResponseHelper::jsonResponse(
            null,
            __('messages.section.deleted'),
        );
    }

    /**
     * @throws PermissionException
     */
    public function restoreSection($id): JsonResponse
    {
        AuthHelper::authorize(SectionPermission::MANAGE_DELETED_SECTIONS);

        $section = Section::withTrashed()->findOrFail($id);

        if (!$section->trashed()) {
            return ResponseHelper::jsonResponse(
                null,
                'Section is not deleted',
                ResponseAlias::HTTP_BAD_REQUEST,
                false
            );
        }

        $section->restore();

        return ResponseHelper::jsonResponse(
            new SectionResource($section),
            __('messages.section.restored'),
        );
    }

    /**
     * @throws PermissionException
     */
    public function forceDeleteSection($id): JsonResponse
    {
        AuthHelper::authorize(SectionPermission::MANAGE_DELETED_SECTIONS);

//        $section = Section::withTrashed()->findOrFail($id);
        $section = Section::findOrFail($id);

        // Check if section has related data
        if ($section->studentEnrollments()->exists()) {
            return ResponseHelper::jsonResponse(
                null,
                __('messages.section.has_enrollments'),
                ResponseAlias::HTTP_BAD_REQUEST,
                false
            );
        }

        if ($section->teacherSectionSubjects()->exists()) {
            return ResponseHelper::jsonResponse(
                null,
                __('messages.section.has_teacher_assignments'),
                ResponseAlias::HTTP_BAD_REQUEST,
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
