<?php

namespace App\Services;

use App\Enums\Permissions\TeacherSectionSubjectPermission;
use App\Exceptions\PermissionException;
use App\Helpers\ResponseHelper;
use App\Http\Resources\TeacherSectionSubjectResource;
use App\Models\Section;
use App\Models\TeacherSectionSubject;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class TeacherSectionSubjectService
{
    

    /**
     * @throws PermissionException
     */
    public function listTeacherSectionSubjects(): JsonResponse
    {
        AuthHelper::authorize(TeacherSectionSubjectPermission::VIEW_TEACHER_SECTION_SUBJECTS);

        $teacherSectionSubjects = TeacherSectionSubject::with([
            'teacher.user',
            'grade',
            'subject',
            'section',
        ])
            ->orderBy('id', 'desc')
            ->get();

        return ResponseHelper::jsonResponse(
            TeacherSectionSubjectResource::collection($teacherSectionSubjects),
            __('messages.teacher_section_subject.listed')
        );
    }

    /**
     * @throws PermissionException
     */
    public function listTrashedTeacherSectionSubjects(): JsonResponse
    {
        AuthHelper::authorize(TeacherSectionSubjectPermission::MANAGE_DELETED_TEACHER_SECTION_SUBJECTS);

        $teacherSectionSubjects = TeacherSectionSubject::onlyTrashed()
            ->with([
                'teacher.user',
                'grade',
                'subject',
                'section',
            ])
            ->orderBy('id', 'desc')
            ->get();

        return ResponseHelper::jsonResponse(
            TeacherSectionSubjectResource::collection($teacherSectionSubjects),
            __('messages.teacher_section_subject.listed')
        );
    }

    /**
     * @throws PermissionException
     */
    public function createTeacherSectionSubject($request): JsonResponse
    {
        AuthHelper::authorize(TeacherSectionSubjectPermission::CREATE_TEACHER_SECTION_SUBJECT);

        $data = $request->validated();
        $data['created_by'] = Auth::user()->id;
        $data['grade_id'] = Section::findOrFail($data['section_id'])->grade_id;

        $teacherSectionSubject = TeacherSectionSubject::create($data);

        return ResponseHelper::jsonResponse(
            new TeacherSectionSubjectResource($teacherSectionSubject->load([
                'teacher.user',
                'grade',
                'subject',
                'section',
            ])),
            __('messages.teacher_section_subject.created'),
            ResponseAlias::HTTP_CREATED,
        );
    }

    /**
     * @throws PermissionException
     */
    public function showTeacherSectionSubject($id): JsonResponse
    {
        AuthHelper::authorize(TeacherSectionSubjectPermission::VIEW_TEACHER_SECTION_SUBJECT);

        $teacherSectionSubject = TeacherSectionSubject::with([
            'teacher.user',
            'grade',
            'subject',
            'section',
        ])
            ->findOrFail($id);

        return ResponseHelper::jsonResponse(
            new TeacherSectionSubjectResource($teacherSectionSubject),
            __('messages.teacher_section_subject.fetched')
        );
    }

    /**
     * @throws PermissionException
     */
    public function updateTeacherSectionSubject($request, $id): JsonResponse
    {
        AuthHelper::authorize(TeacherSectionSubjectPermission::UPDATE_TEACHER_SECTION_SUBJECT);

        $teacherSectionSubject = TeacherSectionSubject::findOrFail($id);
        $data = $request->validated();

        $data['grade_id'] = Section::findOrFail($data['section_id'])->grade_id;
        $teacherSectionSubject->update($data);

        return ResponseHelper::jsonResponse(
            new TeacherSectionSubjectResource($teacherSectionSubject->load([
                'teacher.user',
                'grade',
                'subject',
                'section',
            ])),
            __('messages.teacher_section_subject.updated')
        );
    }

    /**
     * @throws PermissionException
     */
    public function deleteTeacherSectionSubject($id): JsonResponse
    {
        AuthHelper::authorize(TeacherSectionSubjectPermission::DELETE_TEACHER_SECTION_SUBJECT);

        $teacherSectionSubject = TeacherSectionSubject::findOrFail($id);
        $teacherSectionSubject->delete();

        return ResponseHelper::jsonResponse(
            null,
            __('messages.teacher_section_subject.deleted')
        );
    }

    /**
     * @throws PermissionException
     */
    public function restoreTeacherSectionSubject($id): JsonResponse
    {
        AuthHelper::authorize(TeacherSectionSubjectPermission::MANAGE_DELETED_TEACHER_SECTION_SUBJECTS);

        $teacherSectionSubject = TeacherSectionSubject::onlyTrashed()->findOrFail($id);
        $teacherSectionSubject->restore();

        return ResponseHelper::jsonResponse(
            new TeacherSectionSubjectResource($teacherSectionSubject->load([
                'teacher.user',
                'grade',
                'subject',
                'section',
            ])),
            __('messages.teacher_section_subject.restored')
        );
    }

    /**
     * @throws PermissionException
     */
    public function forceDeleteTeacherSectionSubject($id): JsonResponse
    {
        AuthHelper::authorize(TeacherSectionSubjectPermission::MANAGE_DELETED_TEACHER_SECTION_SUBJECTS);

//        $teacherSectionSubject = TeacherSectionSubject::onlyTrashed()->findOrFail($id);
        $teacherSectionSubject = TeacherSectionSubject::findOrFail($id);
        $teacherSectionSubject->forceDelete();

        return ResponseHelper::jsonResponse(
            null,
            __('messages.teacher_section_subject.force_deleted')
        );
    }

    /**
     * @throws PermissionException
     */
    public function getByTeacher($teacherId): JsonResponse
    {
        AuthHelper::authorize(TeacherSectionSubjectPermission::VIEW_TEACHER_SECTION_SUBJECTS);

        $teacherSectionSubjects = TeacherSectionSubject::where('teacher_id', $teacherId)
            ->with([
                'teacher.user',
                'grade',
                'subject',
                'section',
            ])
            ->orderBy('id', 'desc')
            ->get();

        return ResponseHelper::jsonResponse(
            TeacherSectionSubjectResource::collection($teacherSectionSubjects),
            __('messages.teacher_section_subject.listed')
        );
    }

    /**
     * @throws PermissionException
     */
    public function getBySection($sectionId): JsonResponse
    {
        AuthHelper::authorize(TeacherSectionSubjectPermission::VIEW_TEACHER_SECTION_SUBJECTS);

        $teacherSectionSubjects = TeacherSectionSubject::where('section_id', $sectionId)
            ->with([
                'teacher.user',
                'grade',
                'subject',
                'section',
            ])
            ->orderBy('id', 'desc')
            ->get();

        return ResponseHelper::jsonResponse(
            TeacherSectionSubjectResource::collection($teacherSectionSubjects),
            __('messages.teacher_section_subject.listed')
        );
    }

    /**
     * @throws PermissionException
     */
    public function getBySubject($subjectId): JsonResponse
    {
        AuthHelper::authorize(TeacherSectionSubjectPermission::VIEW_TEACHER_SECTION_SUBJECTS);

        $teacherSectionSubjects = TeacherSectionSubject::where('subject_id', $subjectId)
            ->with([
                'teacher.user',
                'grade',
                'subject',
                'section',
            ])
            ->orderBy('id', 'desc')
            ->get();

        return ResponseHelper::jsonResponse(
            TeacherSectionSubjectResource::collection($teacherSectionSubjects),
            __('messages.teacher_section_subject.listed')
        );
    }
}
