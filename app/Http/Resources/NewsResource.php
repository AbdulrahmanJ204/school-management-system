<?php

namespace App\Http\Resources;

use App\Enums\UserType;
use App\Models\NewsTarget;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NewsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $user = auth()->user();

        if ($user->user_type === UserType::Admin->value) {
            $targets = $this->whenLoaded('newsTargets');

            $grades = GradeResource::collection($targets->whereNotNull('grade')->pluck('grade')->unique()->values());
            $sections = SectionResource::collection($targets->whereNotNull('section')->pluck('section')->unique()->values());

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
                'date' => $this->schoolDay?->date->format('Y-m-d'),
                'created_at' => $this->created_at->format('Y-m-d H:i:s'),
                'photo' => $this->photo ? asset('storage/' . $this->photo) : null, // Full URL
                'deleted_at'=>$this->deleted_at?->format('Y-m-d'),
                'targets' => $targetsArray,
            ];
        } else
            return [
                "id" => $this->id,
                "title" => $this->title,
                "description" => json_decode($this->content),
                'date' => $this->schoolDay->date->format('Y-m-d'),
                'photo' => $this->photo ? asset('storage/' . $this->photo) : null, // Full URL
            ];
    }
}
