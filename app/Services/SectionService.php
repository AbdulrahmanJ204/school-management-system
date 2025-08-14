<?php

namespace App\Services;

use App\Enums\PermissionEnum;
use App\Exceptions\PermissionException;
use App\Helpers\ResponseHelper;
use App\Http\Requests\SectionRequest;
use App\Http\Resources\SectionResource;
use App\Models\Section;
use App\Traits\HasPermissionChecks;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class SectionService
{
    use HasPermissionChecks;

    /**
     * @throws PermissionException
     */
    public function listSection(): JsonResponse
    {
        $this->checkPermission(PermissionEnum::VIEW_SECTIONS);

        $sections = Section::with([
//            'grade'
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
        $this->checkPermission(PermissionEnum::MANAGE_DELETED_SECTIONS);

        $sections = Section::with([
//            'grade'
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
        $this->checkPermission(PermissionEnum::CREATE_SECTION);

        $admin = auth()->user();
        $credentials = $request->validated();
        $credentials['created_by'] = $admin->id;
        $section = Section::create($credentials);

        $section->load([
//            'grade'
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
        $this->checkPermission(PermissionEnum::VIEW_SECTION);

        $section->load([
//            'grade',
//            'studentEnrollments.student',
//            'teacherSectionSubjects.teacher'
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
        $this->checkPermission(PermissionEnum::UPDATE_SECTION);

        $section->update($request->validated());

        $section->load([
//            'grade'
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
        $this->checkPermission(PermissionEnum::DELETE_SECTION);

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
        $this->checkPermission(PermissionEnum::MANAGE_DELETED_SECTIONS);

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
        $this->checkPermission(PermissionEnum::MANAGE_DELETED_SECTIONS);

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
