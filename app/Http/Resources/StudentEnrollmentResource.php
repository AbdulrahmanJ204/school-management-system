<?php

namespace App\Http\Resources;

use App\Http\Resources\Basic\SectionBasicResource;
use App\Http\Resources\Basic\GradeBasicResource;
use App\Http\Resources\Basic\UserBasicResource;
use App\Http\Resources\BaseResource;
use Illuminate\Http\Request;

/**
 * Student Enrollment Resource - Complete enrollment information
 * مورد تسجيل الطالب - معلومات التسجيل الكاملة
 * Uses basic resources to avoid circular dependencies
 * يستخدم الموارد الأساسية لتجنب التضارب الدوري
 */
class StudentEnrollmentResource extends BaseResource
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
            'student_id' => $this->student_id,
            'grade_id' => $this->grade_id,
            'section_id' => $this->section_id,
            'semester_id' => $this->semester_id,
            'year_id' => $this->year_id,
            'last_year_gpa' => $this->last_year_gpa,
            'enrollment_date' => $this->enrollment_date,
            'status' => $this->status,
            'created_by' => $this->created_by,

            // Use basic resources to avoid circular dependencies
            // استخدام الموارد الأساسية لتجنب التضارب الدوري
            'section' => $this->whenLoadedResource('section', SectionBasicResource::class),
            'semester' => $this->whenLoadedResource('semester', SemesterResource::class),
            'year' => $this->whenLoadedResource('year', YearResource::class),
            'created_by_user' => $this->whenLoadedResource('createdBy', UserBasicResource::class),

            // Computed properties using basic resources
            // الخصائص المحسوبة باستخدام الموارد الأساسية
            'grade' => $this->whenLoadedResource('section.grade', GradeBasicResource::class),
            'user' => $this->whenLoadedResource('student.user', UserBasicResource::class),

            'created_at' => $this->formatDate($this->created_at),
            'updated_at' => $this->formatDate($this->updated_at),
        ];
    }
}
