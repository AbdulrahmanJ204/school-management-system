<?php

namespace App\Http\Resources\Basic;

use App\Http\Resources\BaseResource;
use Illuminate\Http\Request;

/**
 * Basic User Resource - Contains only essential user data
 * مورد المستخدم الأساسي - يحتوي على البيانات الأساسية فقط
 * Used to avoid circular dependencies in other resources
 * يُستخدم لتجنب التضارب الدوري في الموارد الأخرى
 */
class UserBasicResource extends BaseResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'full_name' => trim("{$this->first_name} {$this->father_name} {$this->last_name}"),
            'first_name' => $this->first_name,
            'father_name' => $this->father_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'user_type' => $this->user_type,
            'image' => $this->image ? asset('storage/' . $this->image) : asset('storage/user_images/default.png'),
        ];
    }
}
