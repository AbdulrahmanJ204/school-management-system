<?php

namespace App\Http\Resources;

use App\Http\Resources\Basic\GradeBasicResource;
use App\Http\Resources\Basic\SubjectBasicResource;
use App\Http\Resources\BaseResource;
use Illuminate\Http\Request;

/**
 * Main Subject Resource - Complete main subject information
 * مورد المادة الرئيسية - معلومات المادة الرئيسية الكاملة
 * Uses basic resources to avoid circular dependencies
 * يستخدم الموارد الأساسية لتجنب التضارب الدوري
 */
class MainSubjectResource extends BaseResource
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
            'success_rate' => $this->success_rate,
            'grade_id' => $this->grade_id,
            'grade' => $this->whenLoadedResource('grade', GradeBasicResource::class),
            'created_by' => $this->getCreatedByName(),
            
            // Use basic resources to avoid circular dependencies
            // استخدام الموارد الأساسية لتجنب التضارب الدوري
            'subjects' => $this->whenLoadedCollection('subjects', SubjectBasicResource::class),
            'subjects_count' => $this->when(
                $this->relationLoaded('subjects'),
                $this->subjects->count()
            ),
            
            'created_at' => $this->formatDate($this->created_at),
            'updated_at' => $this->formatDate($this->updated_at),
        ];
    }
}
