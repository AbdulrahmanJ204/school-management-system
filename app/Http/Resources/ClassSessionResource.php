<?php

namespace App\Http\Resources;

use App\Http\Resources\Basic\SubjectBasicResource;
use App\Http\Resources\Basic\SectionBasicResource;
use App\Http\Resources\Basic\UserBasicResource;
use App\Http\Resources\BaseResource;
use Illuminate\Http\Request;

/**
 * Class Session Resource - Complete class session information
 * مورد جلسة الفصل - معلومات الجلسة الكاملة
 * Uses basic resources to avoid circular dependencies
 * يستخدم الموارد الأساسية لتجنب التضارب الدوري
 */
class ClassSessionResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'schedule_id' => $this->schedule_id,
            'school_day_id' => $this->school_day_id,
            'teacher_id' => $this->teacher_id,
            'subject_id' => $this->subject_id,
            'section_id' => $this->section_id,
            'class_period_id' => $this->class_period_id,
            'status' => $this->status,
            'total_students_count' => $this->total_students_count,
            'present_students_count' => $this->present_students_count,
            'created_by' => $this->created_by,

            // Use basic resources to avoid circular dependencies
            // استخدام الموارد الأساسية لتجنب التضارب الدوري
            'schedule' => $this->whenLoadedResource('schedule', ScheduleResource::class),
            'school_day' => $this->whenLoadedResource('schoolDay', SchoolDayResource::class),
            'teacher' => $this->whenLoadedResource('teacher.user', UserBasicResource::class),
            'subject' => $this->whenLoadedResource('subject', SubjectBasicResource::class),
            'section' => $this->whenLoadedResource('section', SectionBasicResource::class),
            'class_period' => $this->whenLoadedResource('classPeriod', ClassPeriodResource::class),
            'created_by_user' => $this->whenLoadedResource('createdBy', UserBasicResource::class),

            // Load relationships only when explicitly requested
            // تحميل العلاقات فقط عند الطلب الصريح
            'student_attendances' => $this->whenExplicitlyRequestedCollection(
                $request, 
                'attendances', 
                StudentAttendanceResource::class
            ),
            'assignments' => $this->whenExplicitlyRequestedCollection(
                $request, 
                'assignments', 
                AssignmentResource::class
            ),
            'study_notes' => $this->whenExplicitlyRequestedCollection(
                $request, 
                'study_notes', 
                StudyNoteResource::class
            ),

            // Computed properties
            'attendance_percentage' => $this->when($this->total_students_count > 0, function () {
                return round(($this->present_students_count / $this->total_students_count) * 100, 2);
            }),

            'can_be_started' => $this->canBeStarted(),
            'is_today' => $this->schoolDay && $this->schoolDay->date->isToday(),

            'created_at' => $this->formatDate($this->created_at),
            'updated_at' => $this->formatDate($this->updated_at),
        ];
    }
}
