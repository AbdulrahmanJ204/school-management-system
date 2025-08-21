<?php

namespace App\Http\Controllers;

use App\Exceptions\PermissionException;
use App\Services\TeacherService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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

    /**
     * Get students in a section with their marks for a specific subject
     * 
     * @OA\Get(
     *     path="/api/teacher/section/{sectionId}/subject/{subjectId}/students",
     *     summary="Get students in a section with their marks",
     *     description="Retrieve students enrolled in a specific section with their marks for a specific subject",
     *     tags={"Teacher"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="sectionId",
     *         in="path",
     *         required=true,
     *         description="Section ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="subjectId",
     *         in="path",
     *         required=true,
     *         description="Subject ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(property="status_code", type="integer", example=200),
     *             @OA\Property(property="message", type="string", example="تم جلب بيانات الطلاب وعلاماتهم بنجاح"),
     *             @OA\Property(property="data", type="array", @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="first_name", type="string", example="اسم الطالب الأول"),
     *                 @OA\Property(property="last_name", type="string", example="اسم الطالب الثاني"),
     *                 @OA\Property(property="father_name", type="string", example="اسم الاب"),
     *                 @OA\Property(property="mother_name", type="string", example="اسم الام"),
     *                 @OA\Property(property="photo_link", type="string", example="رابط صورة الطالب"),
     *                 @OA\Property(property="birth_date", type="string", example="تاريخ الميلاد"),
     *                 @OA\Property(property="gender", type="string", example="الجنس"),
     *                 @OA\Property(property="phone_number", type="string", example="رقم الهاتف"),
     *                 @OA\Property(property="email", type="string", example="البريد الإلكتروني"),
     *                 @OA\Property(property="grandfather_name", type="string", example="اسم الجد"),
     *                 @OA\Property(property="general_id", type="string", example="الرقم العام"),
     *                 @OA\Property(property="results", type="object", 
     *                     @OA\Property(property="activityMark", type="integer", nullable=true, example=10),
     *                     @OA\Property(property="oralMark", type="integer", nullable=true, example=10),
     *                     @OA\Property(property="homeworkMark", type="integer", nullable=true, example=10),
     *                     @OA\Property(property="quizMark", type="integer", nullable=true, example=10),
     *                     @OA\Property(property="examMark", type="integer", nullable=true, example=10)
     *                 )
     *             ))
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden - User is not a teacher or not assigned to this section/subject",
     *         @OA\JsonContent(
     *             @OA\Property(property="status_code", type="integer", example=403),
     *             @OA\Property(property="message", type="string", example="غير مصرح لك بالوصول إلى هذه الشعبة أو المادة")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not Found - No active semester found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status_code", type="integer", example=404),
     *             @OA\Property(property="message", type="string", example="لا يوجد فصل دراسي نشط حالياً")
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
    public function getStudentsInSectionWithMarks(Request $request, int $sectionId, int $subjectId): JsonResponse
    {
        return $this->teacherService->getStudentsInSectionWithMarks($sectionId, $subjectId);
    }
}
