<?php

namespace App\Services;

use App\Enums\PermissionEnum;
use App\Exceptions\PermissionException;
use App\Helpers\ResponseHelper;
use App\Http\Resources\TeacherAttendanceResource;
use App\Models\TeacherAttendance;
use App\Traits\HasPermissionChecks;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class TeacherAttendanceService
{
    use HasPermissionChecks;

    /**
     * @throws PermissionException
     */
    public function listTeacherAttendances(Request $request): JsonResponse
    {
        $this->checkPermission(PermissionEnum::VIEW_TEACHER_ATTENDANCES);

        $query = TeacherAttendance::with([
            'teacher.user',
            'classSession',
            'createdBy'
        ]);

        // Apply filters
        if ($request->has('teacher_id')) {
            $query->where('teacher_id', $request->teacher_id);
        }

        if ($request->has('class_session_id')) {
            $query->where('class_session_id', $request->class_session_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('date_from')) {
            $query->whereHas('classSession', function ($q) use ($request) {
                $q->where('date', '>=', $request->date_from);
            });
        }

        if ($request->has('date_to')) {
            $query->whereHas('classSession', function ($q) use ($request) {
                $q->where('date', '<=', $request->date_to);
            });
        }

        $teacherAttendances = $query->orderBy('created_at', 'desc')->paginate(15);

        return ResponseHelper::jsonResponse(
            TeacherAttendanceResource::collection($teacherAttendances),
            'تم عرض حضور المعلمين بنجاح'
        );
    }

    /**
     * @throws PermissionException
     */
    public function createTeacherAttendance(Request $request): JsonResponse
    {
        $this->checkPermission(PermissionEnum::CREATE_TEACHER_ATTENDANCE);

        $data = $request->validated();
        $data['created_by'] = auth()->id();

        $teacherAttendance = TeacherAttendance::create($data);

        return ResponseHelper::jsonResponse(
            new TeacherAttendanceResource($teacherAttendance->load([
                'teacher.user',
                'classSession',
                'createdBy'
            ])),
            'تم إضافة حضور المعلم بنجاح',
            ResponseAlias::HTTP_CREATED,
        );
    }

    /**
     * @throws PermissionException
     */
    public function showTeacherAttendance($id): JsonResponse
    {
        $this->checkPermission(PermissionEnum::VIEW_TEACHER_ATTENDANCE);

        $teacherAttendance = TeacherAttendance::with([
            'teacher.user',
            'classSession',
            'createdBy'
        ])
            ->findOrFail($id);

        return ResponseHelper::jsonResponse(
            new TeacherAttendanceResource($teacherAttendance),
            'تم عرض حضور المعلم بنجاح'
        );
    }

    /**
     * @throws PermissionException
     */
    public function updateTeacherAttendance(Request $request, $id): JsonResponse
    {
        $this->checkPermission(PermissionEnum::UPDATE_TEACHER_ATTENDANCE);

        $teacherAttendance = TeacherAttendance::findOrFail($id);
        $data = $request->validated();

        $teacherAttendance->update($data);

        return ResponseHelper::jsonResponse(
            new TeacherAttendanceResource($teacherAttendance->load([
                'teacher.user',
                'classSession',
                'createdBy'
            ])),
            'تم تحديث حضور المعلم بنجاح'
        );
    }

    /**
     * @throws PermissionException
     */
    public function deleteTeacherAttendance($id): JsonResponse
    {
        $this->checkPermission(PermissionEnum::DELETE_TEACHER_ATTENDANCE);

        $teacherAttendance = TeacherAttendance::findOrFail($id);
        $teacherAttendance->delete();

        return ResponseHelper::jsonResponse(
            null,
            'تم حذف حضور المعلم بنجاح'
        );
    }

    /**
     * @throws PermissionException
     */
    public function getByTeacher($teacherId): JsonResponse
    {
        $this->checkPermission(PermissionEnum::VIEW_TEACHER_ATTENDANCES);

        $teacherAttendances = TeacherAttendance::where('teacher_id', $teacherId)
            ->with([
                'teacher.user',
                'classSession',
                'createdBy'
            ])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return ResponseHelper::jsonResponse(
            TeacherAttendanceResource::collection($teacherAttendances),
            'تم عرض حضور المعلم بنجاح'
        );
    }

    /**
     * @throws PermissionException
     */
    public function getByClassSession($classSessionId): JsonResponse
    {
        $this->checkPermission(PermissionEnum::VIEW_TEACHER_ATTENDANCES);

        $teacherAttendances = TeacherAttendance::where('class_session_id', $classSessionId)
            ->with([
                'teacher.user',
                'classSession',
                'createdBy'
            ])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return ResponseHelper::jsonResponse(
            TeacherAttendanceResource::collection($teacherAttendances),
            'تم عرض حضور المعلمين في الجلسة بنجاح'
        );
    }
}
