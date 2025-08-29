<?php

namespace App\Http\Resources\Basic;

use App\Http\Resources\BaseResource;
use Illuminate\Http\Request;

/**
 * Basic Section Resource - Contains only essential section data
 * مورد القسم الأساسي - يحتوي على البيانات الأساسية فقط
 * Used to avoid circular dependencies in other resources
 * يُستخدم لتجنب التضارب الدوري في الموارد الأخرى
 */
class SectionBasicResource extends BaseResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'grade_id' => $this->grade_id,
            'created_at' => $this->formatDate($this->created_at),
            'updated_at' => $this->formatDate($this->updated_at),
        ];
    }
}
