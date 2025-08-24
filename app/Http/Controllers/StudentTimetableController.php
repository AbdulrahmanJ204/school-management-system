<?php

namespace App\Http\Controllers;

use App\Services\StudentTimetableService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Exception;

class StudentTimetableController extends Controller
{
    protected StudentTimetableService $studentTimetableService;

    public function __construct(StudentTimetableService $studentTimetableService)
    {
        $this->studentTimetableService = $studentTimetableService;
    }

    /**
     * Get student timetable
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function timetable(Request $request): JsonResponse
    {
        try {
            $userId = auth()->id();
            
            $timetableData = $this->studentTimetableService->getStudentTimetable($userId);

            return response()->json([
                'message' => 'تم جلب الجدول الدراسي للطالب بنجاح',
                'data' => $timetableData,
                'status_code' => 200
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'حدث خطأ في جلب الجدول الدراسي للطالب: ' . $e->getMessage(),
                'data' => null,
                'status_code' => 404
            ], 404);
        }
    }
}
