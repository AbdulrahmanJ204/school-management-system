<?php

namespace App\Http\Resources;

use App\Http\Resources\Basic\GradeBasicResource;
use App\Http\Resources\Basic\MainSubjectBasicResource;
use App\Http\Resources\BaseResource;
use Illuminate\Http\Request;

/**
 * Subject Resource - Complete subject information
 * مورد المادة - معلومات المادة الكاملة
 * Uses basic resources to avoid circular dependencies
 * يستخدم الموارد الأساسية لتجنب التضارب الدوري
 */
class SubjectResource extends BaseResource
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
            'name' => $this->name,
            'code' => $this->code,
            'full_mark' => $this->full_mark,
            'min_mark' => $this->mainSubject && isset($this->mainSubject->success_rate) ? 
                (int)($this->mainSubject->success_rate * $this->full_mark / 100) : 0,
            'homework_percentage' => $this->homework_percentage,
            'oral_percentage' => $this->oral_percentage,
            'activity_percentage' => $this->activity_percentage,
            'quiz_percentage' => $this->quiz_percentage,
            'exam_percentage' => $this->exam_percentage,
            'num_class_period' => $this->num_class_period,
            'is_failed' => $this->is_failed,
            'created_by' => $this->getCreatedByName(),

            // Use basic resources to avoid circular dependencies
            // استخدام الموارد الأساسية لتجنب التضارب الدوري
            'main_subject' => $this->whenLoadedResource('mainSubject', MainSubjectBasicResource::class),
            'grade' => $this->whenLoadedResource('mainSubject.grade', GradeBasicResource::class),

            'created_at' => $this->formatDate($this->created_at),
            'updated_at' => $this->formatDate($this->updated_at),
        ];
    }
}
