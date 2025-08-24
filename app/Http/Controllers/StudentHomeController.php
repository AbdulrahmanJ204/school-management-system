<?php

namespace App\Http\Controllers;

use App\Services\StudentHomeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Exception;

class StudentHomeController extends Controller
{
    protected StudentHomeService $studentHomeService;

    public function __construct(StudentHomeService $studentHomeService)
    {
        $this->studentHomeService = $studentHomeService;
    }

    /**
     * Get student home data
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function home(Request $request): JsonResponse
    {
        try {
            $userId = auth()->id();
            
            $homeData = $this->studentHomeService->getStudentHomeData($userId);

            return response()->json([
                'message' => 'تم جلب بيانات الطالب بنجاح',
                'data' => $homeData,
                'status_code' => 200
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'حدث خطأ في جلب بيانات الطالب: ' . $e->getMessage(),
                'data' => null,
                'status_code' => 500
            ], 500);
        }
    }
}
