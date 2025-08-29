<?php

namespace App\Http\Resources;

use App\Http\Resources\Basic\GradeBasicResource;
use App\Http\Resources\Basic\UserBasicResource;
use App\Http\Resources\BaseResource;
use Illuminate\Http\Request;

/**
 * Section Resource - Complete section information
 * مورد القسم - معلومات القسم الكاملة
 * Uses basic resources to avoid circular dependencies
 * يستخدم الموارد الأساسية لتجنب التضارب الدوري
 */
class SectionResource extends BaseResource
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
            'title' => $this->title,
            'grade_id' => $this->grade_id,
            'created_at' => $this->formatDate($this->created_at),
            'updated_at' => $this->formatDate($this->updated_at),
            'created_by' => $this->whenLoaded('createdBy', function () {
                return $this->createdBy->id . '-' . $this->createdBy->first_name . ' ' . $this->createdBy->last_name;
            }),

            // Use basic resources to avoid circular dependencies
            // استخدام الموارد الأساسية لتجنب التضارب الدوري
            'grade' => $this->whenLoadedResource('grade', GradeBasicResource::class),
            
            // Load relationships only when explicitly requested
            // تحميل العلاقات فقط عند الطلب الصريح
            'student_enrollments' => $this->whenExplicitlyRequestedCollection(
                $request, 
                'enrollments', 
                StudentEnrollmentResource::class
            ),
            'teacher_section_subjects' => $this->whenExplicitlyRequestedCollection(
                $request, 
                'teacher_subjects', 
                TeacherSectionSubjectResource::class
            ),
        ];
    }
}
