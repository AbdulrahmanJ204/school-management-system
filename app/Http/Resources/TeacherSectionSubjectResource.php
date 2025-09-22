<?php

namespace App\Http\Resources;

use App\Http\Resources\Basic\GradeBasicResource;
use App\Http\Resources\Basic\SubjectBasicResource;
use App\Http\Resources\Basic\SectionBasicResource;
use App\Http\Resources\Basic\UserBasicResource;
use App\Http\Resources\BaseResource;
use Illuminate\Http\Request;

/**
 * Teacher Section Subject Resource - Complete teacher section subject information
 * مورد مادة قسم المعلم - معلومات مادة قسم المعلم الكاملة
 * Uses basic resources to avoid circular dependencies
 * يستخدم الموارد الأساسية لتجنب التضارب الدوري
 */
class TeacherSectionSubjectResource extends BaseResource
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
            'teacher_id' => $this->teacher_id,
            'grade_id' => $this->grade_id,
            'subject_id' => $this->subject_id,
            'section_id' => $this->section_id,
            'is_active' => $this->is_active,
            'num_class_period' => $this->num_class_period,
            'created_by' => $this->getCreatedByName(),

            // Use basic resources to avoid circular dependencies
            // استخدام الموارد الأساسية لتجنب التضارب الدوري
            'teacher' => $this->whenLoaded('teacher.user', function () {
                return $this->teacher && $this->teacher->user ? new UserBasicResource($this->teacher->user) : null;
            }),
            'grade' => $this->whenLoadedResource('grade', GradeBasicResource::class),
            'subject' => $this->whenLoadedResource('subject', SubjectBasicResource::class),
            'section' => $this->whenLoadedResource('section', SectionBasicResource::class),

            'created_at' => $this->formatDate($this->created_at),
            'updated_at' => $this->formatDate($this->updated_at),
            'deleted_at' => $this->formatDate($this->deleted_at),
        ];
    }
}
