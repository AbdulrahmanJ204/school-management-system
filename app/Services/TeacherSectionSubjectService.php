<?php

namespace App\Services;

use App\Enums\PermissionEnum;
use App\Exceptions\PermissionException;
use App\Helpers\ResponseHelper;
use App\Http\Resources\TeacherSectionSubjectResource;
use App\Models\Section;
use App\Models\TeacherSectionSubject;
use App\Traits\HasPermissionChecks;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class TeacherSectionSubjectService
{
    use HasPermissionChecks;

    /**
     * @throws PermissionException
     */
    public function listTeacherSectionSubjects(): JsonResponse
    {
        $this->checkPermission(PermissionEnum::VIEW_TEACHER_SECTION_SUBJECTS);

        $teacherSectionSubjects = TeacherSectionSubject::with([
//            'teacher',
//            'grade',
//            'subject',
//            'section',
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
        $this->checkPermission(PermissionEnum::MANAGE_DELETED_TEACHER_SECTION_SUBJECTS);

        $teacherSectionSubjects = TeacherSectionSubject::onlyTrashed()
            ->with([
//                'teacher',
//                'grade',
//                'subject',
//                'section',
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
        $this->checkPermission(PermissionEnum::CREATE_TEACHER_SECTION_SUBJECT);

        $data = $request->validated();
        $data['created_by'] = auth()->id();
        $data['grade_id'] = Section::findOrFail($data['section_id'])->grade_id;

        $teacherSectionSubject = TeacherSectionSubject::create($data);

        return ResponseHelper::jsonResponse(
            new TeacherSectionSubjectResource($teacherSectionSubject->load([
//                'teacher',
//                'grade',
//                'subject',
//                'section',
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
        $this->checkPermission(PermissionEnum::VIEW_TEACHER_SECTION_SUBJECT);

        $teacherSectionSubject = TeacherSectionSubject::with([
//            'teacher',
//            'grade',
//            'subject',
//            'section',
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
        $this->checkPermission(PermissionEnum::UPDATE_TEACHER_SECTION_SUBJECT);

        $teacherSectionSubject = TeacherSectionSubject::findOrFail($id);
        $data = $request->validated();

        $data['grade_id'] = Section::findOrFail($data['section_id'])->grade_id;
        $teacherSectionSubject->update($data);

        return ResponseHelper::jsonResponse(
            new TeacherSectionSubjectResource($teacherSectionSubject->load([
//                'teacher',
//                'grade',
//                'subject',
//                'section',
            ])),
            __('messages.teacher_section_subject.updated')
        );
    }

    /**
     * @throws PermissionException
     */
    public function deleteTeacherSectionSubject($id): JsonResponse
    {
        $this->checkPermission(PermissionEnum::DELETE_TEACHER_SECTION_SUBJECT);

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
        $this->checkPermission(PermissionEnum::MANAGE_DELETED_TEACHER_SECTION_SUBJECTS);

        $teacherSectionSubject = TeacherSectionSubject::onlyTrashed()->findOrFail($id);
        $teacherSectionSubject->restore();

        return ResponseHelper::jsonResponse(
            new TeacherSectionSubjectResource($teacherSectionSubject->load([
//                'teacher',
//                'grade',
//                'subject',
//                'section',
            ])),
            __('messages.teacher_section_subject.restored')
        );
    }

    /**
     * @throws PermissionException
     */
    public function forceDeleteTeacherSectionSubject($id): JsonResponse
    {
        $this->checkPermission(PermissionEnum::MANAGE_DELETED_TEACHER_SECTION_SUBJECTS);

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
        $this->checkPermission(PermissionEnum::VIEW_TEACHER_SECTION_SUBJECTS);

        $teacherSectionSubjects = TeacherSectionSubject::where('teacher_id', $teacherId)
            ->with([
//                'teacher',
//                'grade',
//                'subject',
//                'section',
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
        $this->checkPermission(PermissionEnum::VIEW_TEACHER_SECTION_SUBJECTS);

        $teacherSectionSubjects = TeacherSectionSubject::where('section_id', $sectionId)
            ->with([
//                'teacher',
//                'grade',
//                'subject',
//                'section',
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
        $this->checkPermission(PermissionEnum::VIEW_TEACHER_SECTION_SUBJECTS);

        $teacherSectionSubjects = TeacherSectionSubject::where('subject_id', $subjectId)
            ->with([
//                'teacher',
//                'grade',
//                'subject',
//                'section',
            ])
            ->orderBy('id', 'desc')
            ->get();

        return ResponseHelper::jsonResponse(
            TeacherSectionSubjectResource::collection($teacherSectionSubjects),
            __('messages.teacher_section_subject.listed')
        );
    }
}
