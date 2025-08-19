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
            'grade'    => $this->grade->title ?? null,
            'subject'  => $this->subject->name ?? null,
            'semester' => $this->semester->name ?? null,
            'sections' => $this->section ? [
                'id'   => $this->section->id,
                'name' => $this->section->title,
            ] : 'All Sections',
        ];
    }
}
