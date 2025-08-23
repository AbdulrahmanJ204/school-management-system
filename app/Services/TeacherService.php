<?php

namespace App\Services;

use App\Exceptions\PermissionException;
use App\Helpers\ResponseHelper;
use App\Http\Resources\UserResource;
use App\Models\Semester;
use App\Models\TeacherAttendance;
use App\Models\User;
use App\Models\TeacherSectionSubject;
use App\Models\StudentEnrollment;
use App\Models\StudentMark;
use App\Models\Schedule;
use App\Models\ClassSession;
use App\Enums\WeekDay;
use Carbon\Carbon;
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
        $currentSemester = Semester::where('is_active', true)->first();

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
        );
    }

    /**
     * Get teacher profile with detailed information
     * @throws PermissionException
     */
    public function getTeacherProfile(): JsonResponse
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

        $teacher = auth()->user()->teacher;
        $user = auth()->user();

        // Calculate age
        $birthDate = Carbon::parse($user->birth_date);
        $age = $birthDate->age;

        // Get available days from schedules
        $availableDays = Schedule::whereHas('teacherSectionSubject', function ($query) use ($teacher) {
            $query->where('teacher_id', $teacher->id)->where('is_active', true);
        })
        ->pluck('week_day')
        ->unique()
        ->map(function ($day) {
            return WeekDay::arabic()[$day];
        })
        ->values()
        ->toArray();

        // Calculate attendance statistics
        // TeacherAttendance only records absences and lateness, not present attendance
        $totalSessions = ClassSession::where('teacher_id', $teacher->id)->count();

        if ($totalSessions > 0) {
            $completedSessions = ClassSession::where('teacher_id', $teacher->id)
                ->where('status', 'completed')
                ->count();

            $attendancePercentage = round(($completedSessions / $totalSessions) * 100);

            // Get absence records from TeacherAttendance
            $absenceRecords = TeacherAttendance::where('teacher_id', $teacher->id)->get();

            $absencePercentage = round((TeacherAttendance::where('teacher_id', $teacher->id)
                ->where('status', 'Unexcused absence')
                ->count() / $totalSessions) * 100);

            $latenessPercentage = round((TeacherAttendance::where('teacher_id', $teacher->id)
                ->where('status', 'Late')
                ->count() / $totalSessions) * 100);

            $justifiedAbsencePercentage = round((TeacherAttendance::where('teacher_id', $teacher->id)
                ->where('status', 'Excused absence')
                ->count() / $totalSessions) * 100);
        } else {
            $attendancePercentage = 0;
            $absencePercentage = 0;
            $latenessPercentage = 0;
            $justifiedAbsencePercentage = 0;
        }

        // Get grades and sections
        $gradesAndSections = TeacherSectionSubject::where('teacher_id', $teacher->id)
            ->where('is_active', true)
            ->with(['grade:id,title', 'section:id,title'])
            ->get()
            ->groupBy('grade.title')
            ->map(function ($gradeData) {
                $sections = [];
                foreach ($gradeData->pluck('section.title') as $sectionTitle) {
                    // Convert Arabic section names to numbers
                    $sectionNumber = $this->convertArabicSectionToNumber($sectionTitle);
                    $sections[$sectionNumber] = true;
                }
                return $sections;
            })
            ->toArray();

        // Get primary subject (most taught subject)
        $primarySubject = TeacherSectionSubject::where('teacher_id', $teacher->id)
            ->where('is_active', true)
            ->with('subject:id,name')
            ->get()
            ->groupBy('subject.name')
            ->map->count()
            ->sortDesc()
            ->keys()
            ->first() ?? 'غير محدد';

        $profileData = [
            'firstName' => $user->first_name,
            'lastName' => $user->last_name,
            'subject' => $primarySubject,
            'fatherName' => $user->father_name,
            'image' => $user->image ? asset('storage/' . $user->image) : asset('storage/user_images/default.png'),
            'birthDate' => $user->birth_date,
            'age' => $age,
            'gender' => $user->gender,
            'email' => $user->email,
            'phone' => $user->phone,
            'availableDays' => ["الأربعاء","الأحد","الخميس"],
//            Todo after Create class session
//            'availableDays' => $availableDays,
            'attendancePercentage' => $attendancePercentage,
            'absencePercentage' => $absencePercentage,
            'latenessPercentage' => $latenessPercentage,
            'justifiedAbsencePercentage' => $justifiedAbsencePercentage,
            'classesAndSections' => $gradesAndSections
        ];

        return ResponseHelper::jsonResponse(
            $profileData,
            'تم جلب بيانات الملف الشخصي بنجاح',
        );
    }

    /**
     * Convert Arabic section names to numbers
     */
    private function convertArabicSectionToNumber(string $sectionName): int
    {
        $arabicToNumber = [
            'الأولى' => 1,
            'الثانية' => 2,
            'الثالثة' => 3,
            'الرابعة' => 4,
            'الخامسة' => 5,
            'السادسة' => 6,
            'السابعة' => 7,
            'الثامنة' => 8,
            'التاسعة' => 9,
            'العاشرة' => 10,
            'الحادية عشر' => 11,
            'الثانية عشر' => 12,
            'الثالثة عشر' => 13,
            'الرابعة عشر' => 14,
            'الخامسة عشر' => 15,
            'السادسة عشر' => 16,
            'السابعة عشر' => 17,
            'الثامنة عشر' => 18,
            'التاسعة عشر' => 19,
            'العشرون' => 20
        ];

        return $arabicToNumber[$sectionName] ?? (int) $sectionName;
    }
}
