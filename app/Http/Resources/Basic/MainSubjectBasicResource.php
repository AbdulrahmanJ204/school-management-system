<?php

namespace App\Http\Resources\Basic;

use App\Http\Resources\BaseResource;
use Illuminate\Http\Request;

/**
 * Basic Main Subject Resource - Contains only essential main subject data
 * مورد المادة الرئيسية الأساسي - يحتوي على البيانات الأساسية فقط
 * Used to avoid circular dependencies in other resources
 * يُستخدم لتجنب التضارب الدوري في الموارد الأخرى
 */
class MainSubjectBasicResource extends BaseResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'code' => $this->code,
            'success_rate' => $this->success_rate,
            'grade_id' => $this->grade_id,
            'created_at' => $this->formatDate($this->created_at),
            'updated_at' => $this->formatDate($this->updated_at),
        ];
    }
}
