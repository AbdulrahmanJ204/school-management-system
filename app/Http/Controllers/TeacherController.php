<?php

namespace App\Http\Controllers;

use App\Exceptions\PermissionException;
use App\Services\TeacherService;
use Illuminate\Http\JsonResponse;

class TeacherController extends Controller
{
    protected TeacherService $teacherService;
    public function __construct(TeacherService $teacherService)
    {
        $this->teacherService = $teacherService;
    }

    /**
     * @throws PermissionException
     */
    public function show(): JsonResponse
    {
        return $this->teacherService->listTeachers();
    }

    /**
     * Get teacher's grades, sections, and subjects
     * 
     * @OA\Get(
     *     path="/api/teacher/grades-sections-subjects",
     *     summary="Get teacher's grades, sections, and subjects",
     *     description="Retrieve the grades, sections, and subjects assigned to the authenticated teacher",
     *     tags={"Teacher"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(property="status_code", type="integer", example=200),
     *             @OA\Property(property="message", type="string", example="تم جلب بيانات الصفوف والشعب والمواد بنجاح"),
     *             @OA\Property(property="data", type="array", @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="grade_name", type="string", example="اسم الصف"),
     *                 @OA\Property(property="sections", type="array", @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="section_name", type="string", example="اسم الشعبة"),
     *                     @OA\Property(property="grade_id", type="integer", example=1),
     *                     @OA\Property(property="subjects", type="array", @OA\Items(
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="name", type="string", example="اسم المادة"),
     *                         @OA\Property(property="full_mark", type="integer", example=100),
     *                         @OA\Property(property="min_mark", type="integer", example=50)
     *                     ))
     *                 ))
     *             ))
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden - User is not a teacher or lacks permission",
     *         @OA\JsonContent(
     *             @OA\Property(property="status_code", type="integer", example=403),
     *             @OA\Property(property="message", type="string", example="المستخدم الحالي ليس أستاذاً")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     * 
     * @throws PermissionException
     */
    public function getGradesSectionsSubjects(): JsonResponse
    {
        return $this->teacherService->getTeacherGradesSectionsSubjects();
    }
}
