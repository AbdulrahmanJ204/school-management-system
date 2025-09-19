<?php

namespace App\Http\Resources;

use App\Http\Resources\Basic\GradeBasicResource;
use App\Http\Resources\Basic\SectionBasicResource;
use App\Http\Resources\BaseResource;
use App\Enums\UserType;
use App\Models\NewsTarget;
use Illuminate\Http\Request;

/**
 * News Resource - Complete news information
 * مورد الأخبار - معلومات الأخبار الكاملة
 * Uses basic resources to avoid circular dependencies
 * يستخدم الموارد الأساسية لتجنب التضارب الدوري
 */
class NewsResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $user = Auth::user();

        if ($user->user_type === UserType::Admin->value) {
            $targets = $this->whenLoaded('targets');

            // Use basic resources to avoid circular dependencies
            // استخدام الموارد الأساسية لتجنب التضارب الدوري
            $grades = GradeBasicResource::collection($targets->whereNotNull('grade')->pluck('grade')->unique()->values());
            $sections = SectionBasicResource::collection($targets->whereNotNull('section')->pluck('section')->unique()->values());

            $targetsArray = [];

            if ($sections->isNotEmpty()) {
                $targetsArray['sections'] = $sections;
            }

            if ($grades->isNotEmpty()) {
                $targetsArray['grades'] = $grades;
            }
            
            return [
                "id" => $this->id,
                "title" => $this->title,
                "description" => json_decode($this->content),
                'date' => $this->publish_date->format('Y-m-d h:i:s A'),
                'created_at' => $this->formatDate($this->created_at),
                'photo' => $this->photo ? asset('storage/' . $this->photo) : null,
                'deleted_at' => $this->formatDate($this->deleted_at),
                'targets' => $targetsArray,
            ];
        } else {
            return [
                "id" => $this->id,
                "title" => $this->title,
                "description" => json_decode($this->content),
                'date' => $this->publish_date->format('Y-m-d h:i:s A'),
                'photo' => $this->photo ? asset('storage/' . $this->photo) : null,
            ];
        }
    }
}
