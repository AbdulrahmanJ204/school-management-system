<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NewsTargetResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'section' => $this->whenLoaded('section', function () {
                return new SectionResource($this->section);
            }),
            'grade' =>  $this->whenLoaded('grade', function () {
                return new GradeResource($this->grade);
            }),
        ];
    }
}
