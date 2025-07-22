<?php

namespace App\Http\Resources;

use App\Models\Grade;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SectionResource extends JsonResource
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
            'grade' => GradeResource::make($this->whenLoaded('grade')),
        ];
    }
}
