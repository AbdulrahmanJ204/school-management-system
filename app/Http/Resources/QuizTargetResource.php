<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuizTargetResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'subject'  => $this->subject?->name ?? null,
            'grade'    => $this->section?->grade?->title ?? null,
            'section'  => $this->section?->title ?? null,
            'semester' => $this->semester?->name ?? null,
        ];
    }
}
