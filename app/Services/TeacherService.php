<?php

namespace App\Services;

use App\Exceptions\PermissionException;
use App\Helpers\ResponseHelper;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Models\TeacherSectionSubject;
use App\Models\StudentEnrollment;
use App\Models\StudentMark;
use Illuminate\Http\JsonResponse;

class TeacherService
{
    /**
     * @throws PermissionException
     */
    public function listTeachers(): JsonResponse
    {
        if (!auth()->user()->hasPermissionTo('عرض الاساتذة')) {
            throw new PermissionException();
        }

        $teachers = User::where('user_type', 'teacher')
            ->with(['teacher'])
            ->orderBy('first_name', 'asc')
            ->paginate(50);

        return ResponseHelper::jsonResponse(
            UserResource::collection($teachers),
            __('messages.teacher.listed'),
            200,
            true,
            $teachers->lastPage()
        );
    }

    /**
     * Get teacher's grades, sections, and subjects
     * @throws PermissionException
     */
    public function getTeacherGradesSectionsSubjects(): JsonResponse
    {
        if (!auth()->user()->hasPermissionTo('عرض مواد الأساتذة')) {
            throw new PermissionException();
        }

        // Check if the authenticated user is a teacher
        if (!auth()->user()->teacher) {
            return ResponseHelper::jsonResponse(
                null,
                'المستخدم الحالي ليس أستاذاً',
                403,
                false
            );
        }

        $teacherId = auth()->user()->teacher->id;

        $teacherData = TeacherSectionSubject::where('teacher_id', $teacherId)
            ->where('is_active', true)
            ->with([
                'grade:id,title',
                'section:id,title,grade_id',
                'subject:id,name,full_mark'
            ])
            ->get()
            ->groupBy('grade_id')
            ->map(function ($gradeData, $gradeId) {
                $grade = $gradeData->first()->grade;
                
                $sections = $gradeData->groupBy('section_id')
                    ->map(function ($sectionData, $sectionId) {
                        $section = $sectionData->first()->section;
                        
                        $subjects = $sectionData->map(function ($item) {
                            return [
                                'id' => $item->subject->id,
                                'name' => $item->subject->name,
                                'full_mark' => $item->subject->full_mark,
                                'min_mark' => (int)($item->subject->full_mark * 0.5) // Calculate min_mark as 50% of full_mark
                            ];
                        })->values();

                        return [
                            'id' => $section->id,
                            'section_name' => $section->title,
                            'grade_id' => $section->grade_id,
                            'subjects' => $subjects
                        ];
                    })->values();

                return [
                    'id' => $grade->id,
                    'grade_name' => $grade->title,
                    'sections' => $sections
                ];
            })->values();

        return ResponseHelper::jsonResponse(
            $teacherData,
            'تم جلب بيانات الصفوف والشعب والمواد بنجاح',
            200,
            true
        );
    }

    /**
     * Get students in a section with their marks for a specific subject
     * @throws PermissionException
     */
    public function getStudentsInSectionWithMarks(int $sectionId, int $subjectId): JsonResponse
    {
        // Check if the authenticated user is a teacher
        if (!auth()->user()->teacher) {
            return ResponseHelper::jsonResponse(
                null,
                'المستخدم الحالي ليس أستاذاً',
                403,
                false
            );
        }

        $teacherId = auth()->user()->teacher->id;

        // Verify that the teacher is assigned to this section and subject
        $teacherAssignment = TeacherSectionSubject::where('teacher_id', $teacherId)
            ->where('section_id', $sectionId)
            ->where('subject_id', $subjectId)
            ->where('is_active', true)
            ->first();

        if (!$teacherAssignment) {
            return ResponseHelper::jsonResponse(
                null,
                'غير مصرح لك بالوصول إلى هذه الشعبة أو المادة',
                403,
                false
            );
        }

        // Get current active semester
        $currentSemester = \App\Models\Semester::where('is_active', true)->first();
        
        if (!$currentSemester) {
            return ResponseHelper::jsonResponse(
                null,
                'لا يوجد فصل دراسي نشط حالياً',
                404,
                false
            );
        }

        // Get students enrolled in the section for the current semester
        $students = StudentEnrollment::where('section_id', $sectionId)
            ->where('semester_id', $currentSemester->id)
            ->with([
                'student.user',
                'studentMarks' => function ($query) use ($subjectId) {
                    $query->where('subject_id', $subjectId);
                }
            ])
            ->get()
            ->map(function ($enrollment) {
                $user = $enrollment->student->user;
                $mark = $enrollment->studentMarks->first();

                return [
                    'id' => $user->id,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'father_name' => $user->father_name,
                    'mother_name' => $user->mother_name,
                    'photo_link' => $user->image ? asset('storage/' . $user->image) : asset('storage/user_images/default.png'),
                    'birth_date' => $user->birth_date,
                    'gender' => $user->gender,
                    'phone_number' => $user->phone,
                    'email' => $user->email,
                    'grandfather_name' => $enrollment->student->grandfather,
                    'general_id' => $enrollment->student->general_id,
                    'results' => [
                        'activityMark' => $mark ? $mark->activity : null,
                        'oralMark' => $mark ? $mark->oral : null,
                        'homeworkMark' => $mark ? $mark->homework : null,
                        'quizMark' => $mark ? $mark->quiz : null,
                        'examMark' => $mark ? $mark->exam : null,
                    ]
                ];
            });

        return ResponseHelper::jsonResponse(
            $students,
            'تم جلب بيانات الطلاب وعلاماتهم بنجاح',
            200,
            true
        );
    }
}
