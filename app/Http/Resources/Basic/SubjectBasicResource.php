<?php

namespace App\Http\Resources\Basic;

use App\Http\Resources\BaseResource;
use Illuminate\Http\Request;

/**
 * Basic Subject Resource - Contains only essential subject data
 * مورد المادة الأساسي - يحتوي على البيانات الأساسية فقط
 * Used to avoid circular dependencies in other resources
 * يُستخدم لتجنب التضارب الدوري في الموارد الأخرى
 */
class SubjectBasicResource extends BaseResource
{
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
            'created_at' => $this->formatDate($this->created_at),
            'updated_at' => $this->formatDate($this->updated_at),
        ];
    }
}
