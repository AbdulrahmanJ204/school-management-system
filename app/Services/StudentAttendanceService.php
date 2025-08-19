<?php

namespace App\Services;

use App\Enums\PermissionEnum;
use App\Exceptions\PermissionException;
use App\Helpers\ResponseHelper;
use App\Http\Resources\StudentAttendanceResource;
use App\Models\StudentAttendance;
use App\Traits\HasPermissionChecks;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class StudentAttendanceService
{
    use HasPermissionChecks;

    /**
     * @throws PermissionException
     */
    public function listStudentAttendances(Request $request): JsonResponse
    {
        $this->checkPermission(PermissionEnum::VIEW_STUDENT_ATTENDANCES);

        $query = StudentAttendance::with(['student.user', 'classSession', 'createdBy']);

        // Apply filters
        if ($request->has('student_id')) {
            $query->where('student_id', $request->student_id);
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

        $studentAttendances = $query->orderBy('created_at', 'desc')->paginate(15);

        return ResponseHelper::jsonResponse(
            StudentAttendanceResource::collection($studentAttendances),
            'تم عرض حضور الطلاب بنجاح'
        );
    }

    /**
     * @throws PermissionException
     */
    public function createStudentAttendance(Request $request): JsonResponse
    {
        $this->checkPermission(PermissionEnum::CREATE_STUDENT_ATTENDANCE);

        $data = $request->validated();
        $data['created_by'] = auth()->id();

        $studentAttendance = StudentAttendance::create($data);

        return ResponseHelper::jsonResponse(
            new StudentAttendanceResource($studentAttendance->load(['student.user', 'classSession', 'createdBy'])),
            'تم إضافة حضور الطالب بنجاح',
            ResponseAlias::HTTP_CREATED,
        );
    }

    /**
     * @throws PermissionException
     */
    public function showStudentAttendance($id): JsonResponse
    {
        $this->checkPermission(PermissionEnum::VIEW_STUDENT_ATTENDANCE);

        $studentAttendance = StudentAttendance::with(['student.user', 'classSession', 'createdBy'])
            ->findOrFail($id);

        return ResponseHelper::jsonResponse(
            new StudentAttendanceResource($studentAttendance),
            'تم عرض حضور الطالب بنجاح'
        );
    }

    /**
     * @throws PermissionException
     */
    public function updateStudentAttendance(Request $request, $id): JsonResponse
    {
        $this->checkPermission(PermissionEnum::UPDATE_STUDENT_ATTENDANCE);

        $studentAttendance = StudentAttendance::findOrFail($id);
        $data = $request->validated();

        $studentAttendance->update($data);

        return ResponseHelper::jsonResponse(
            new StudentAttendanceResource($studentAttendance->load(['student.user', 'classSession', 'createdBy'])),
            'تم تحديث حضور الطالب بنجاح'
        );
    }

    /**
     * @throws PermissionException
     */
    public function deleteStudentAttendance($id): JsonResponse
    {
        $this->checkPermission(PermissionEnum::DELETE_STUDENT_ATTENDANCE);

        $studentAttendance = StudentAttendance::findOrFail($id);
        $studentAttendance->delete();

        return ResponseHelper::jsonResponse(
            null,
            'تم حذف حضور الطالب بنجاح'
        );
    }

    /**
     * @throws PermissionException
     */
    public function getByStudent($studentId): JsonResponse
    {
        $this->checkPermission(PermissionEnum::VIEW_STUDENT_ATTENDANCES);

        $studentAttendances = StudentAttendance::where('student_id', $studentId)
            ->with(['student.user', 'classSession', 'createdBy'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return ResponseHelper::jsonResponse(
            StudentAttendanceResource::collection($studentAttendances),
            'تم عرض حضور الطالب بنجاح'
        );
    }

    /**
     * @throws PermissionException
     */
    public function getByClassSession($classSessionId): JsonResponse
    {
        $this->checkPermission(PermissionEnum::VIEW_STUDENT_ATTENDANCES);

        $studentAttendances = StudentAttendance::where('class_session_id', $classSessionId)
            ->with(['student.user', 'classSession', 'createdBy'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return ResponseHelper::jsonResponse(
            StudentAttendanceResource::collection($studentAttendances),
            'تم عرض حضور الطلاب في الجلسة بنجاح'
        );
    }
}
