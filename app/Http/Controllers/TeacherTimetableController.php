<?php

namespace App\Http\Controllers;

use App\Services\TeacherTimetableService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Exception;

class TeacherTimetableController extends Controller
{
    protected TeacherTimetableService $teacherTimetableService;

    public function __construct(TeacherTimetableService $teacherTimetableService)
    {
        $this->teacherTimetableService = $teacherTimetableService;
    }

    /**
     * Get teacher timetable
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function timetable(Request $request): JsonResponse
    {
        try {
            $userId = Auth::user()->id;
            
            $timetableData = $this->teacherTimetableService->getTeacherTimetable($userId);

            return response()->json([
                'message' => 'تم جلب الجدول الدراسي للمدرس بنجاح',
                'data' => $timetableData,
                'status_code' => 200
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'حدث خطأ في جلب الجدول الدراسي للمدرس: ' . $e->getMessage(),
                'data' => null,
                'status_code' => 404
            ], 404);
        }
    }
}
