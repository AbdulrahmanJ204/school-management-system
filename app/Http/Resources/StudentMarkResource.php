<?php

namespace App\Http\Resources;

use App\Http\Resources\Basic\SubjectBasicResource;
use App\Http\Resources\Basic\SectionBasicResource;
use App\Http\Resources\BaseResource;
use Illuminate\Http\Request;

/**
 * Student Mark Resource - Complete student mark information
 * مورد علامة الطالب - معلومات العلامة الكاملة
 * Uses basic resources to avoid circular dependencies
 * يستخدم الموارد الأساسية لتجنب التضارب الدوري
 */
class StudentMarkResource extends BaseResource
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
            'subject_id' => $this->subject_id,
            'enrollment_id' => $this->enrollment_id,
            'homework' => $this->homework,
            'oral' => $this->oral,
            'activity' => $this->activity,
            'quiz' => $this->quiz,
            'exam' => $this->exam,
            'total' => $this->total,
            'created_by' => $this->getCreatedByName(),

            // Use basic resources to avoid circular dependencies
            // استخدام الموارد الأساسية لتجنب التضارب الدوري
            'subject' => $this->whenLoadedResource('subject', SubjectBasicResource::class),
            'enrollment' => $this->whenLoadedResource('enrollment', StudentEnrollmentResource::class),
            'section' => $this->whenLoadedResource('enrollment.section', SectionBasicResource::class),
            'semester' => $this->whenLoadedResource('enrollment.semester', SemesterResource::class),

            'created_at' => $this->formatDate($this->created_at),
            'updated_at' => $this->formatDate($this->updated_at),
        ];
    }
}
