<?php

namespace App\Services;

use App\Exceptions\PermissionException;
use App\Helpers\ResponseHelper;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Models\TeacherSectionSubject;
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
}
