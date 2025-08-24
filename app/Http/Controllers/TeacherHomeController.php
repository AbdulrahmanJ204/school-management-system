<?php

namespace App\Http\Controllers;

use App\Services\TeacherHomeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Exception;

class TeacherHomeController extends Controller
{
    protected TeacherHomeService $teacherHomeService;

    public function __construct(TeacherHomeService $teacherHomeService)
    {
        $this->teacherHomeService = $teacherHomeService;
    }

    /**
     * Get teacher home data
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function home(Request $request): JsonResponse
    {
        try {
            $userId = auth()->id();
            
            $homeData = $this->teacherHomeService->getTeacherHomeData($userId);

            return response()->json([
                'message' => 'تم جلب بيانات المدرس بنجاح',
                'data' => $homeData,
                'status_code' => 200
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'حدث خطأ في جلب بيانات المدرس: ' . $e->getMessage(),
                'data' => null,
                'status_code' => 404
            ], 404);
        }
    }
}
