<?php

namespace App\Http\Resources;

use App\Http\Resources\Basic\SectionBasicResource;
use App\Http\Resources\Basic\MainSubjectBasicResource;
use App\Http\Resources\BaseResource;
use Illuminate\Http\Request;

/**
 * Grade Resource - Complete grade information
 * مورد الصف - معلومات الصف الكاملة
 * Uses basic resources to avoid circular dependencies
 * يستخدم الموارد الأساسية لتجنب التضارب الدوري
 */
class GradeResource extends BaseResource
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
            'year_id' => $this->year_id,
            'year' => $this->whenLoaded('year', function () {
                return [
                    'id' => $this->year->id,
                    'name' => $this->year->name,
                    'start_date' => $this->year->start_date?->format('Y-m-d'),
                    'end_date' => $this->year->end_date?->format('Y-m-d'),
                    'is_active' => $this->year->is_active,
                ];
            }),
            'created_at' => $this->formatDate($this->created_at),
            'updated_at' => $this->formatDate($this->updated_at),

            'created_by' => $this->whenLoaded('createdBy', function () {
                return $this->createdBy->id . '-' . $this->createdBy->first_name . ' ' . $this->createdBy->last_name;
            }),

            // Use basic resources to avoid circular dependencies
            // استخدام الموارد الأساسية لتجنب التضارب الدوري
            'sections' => $this->whenLoadedCollection('sections', SectionBasicResource::class),
            'main_subjects' => $this->whenLoadedCollection('mainSubjects', MainSubjectResource::class),
        ];
    }
}
