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
            'grade' => [
                'id'    => $this->grade->id ?? null,
                'title' => $this->grade->title ?? null,
            ],
            'subject' => [
                'id'   => $this->subject->id ?? null,
                'name' => $this->subject->name ?? null,
            ],
            'semester' => [
                'id'   => $this->semester->id ?? null,
                'name' => $this->semester->name ?? null,
            ],
            'year' => [
                'id'   => $this->semester->year->id ?? null,
                'name' => $this->semester->year->name ?? null,
            ],
            'sections' => $this->section
                ? [
                    'id'   => $this->section->id,
                    'name' => $this->section->title,
                ]
                :'All Sections',
        ];
    }
}
