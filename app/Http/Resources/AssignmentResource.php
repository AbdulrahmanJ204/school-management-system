<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AssignmentResource extends JsonResource
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
            'assigned_session' => [
                'id' => $this->assignedSession->id,
                'date' => $this->assignedSession->date->format('Y-m-d'),
                'time' => $this->assignedSession->time->format('H:i'),
            ],
            'due_session' => $this->dueSession ? [
                'id' => $this->dueSession->id,
                'date' => $this->dueSession->date->format('Y-m-d'),
                'time' => $this->dueSession->time->format('H:i'),
            ] : null,
            'type' => $this->type,
            'title' => $this->title,
            'description' => $this->description,
            'photo' => $this->photo ? asset('storage/' . $this->photo) : null,
            'subject' => [
                'id' => $this->subject->id,
                'name' => $this->subject->name,
            ],
            'section' => [
                'id' => $this->section->id,
                'title' => $this->section->title,
                'grade' => [
                    'id' => $this->section->grade->id,
                    'name' => $this->section->grade->name,
                ],
            ],
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
            'deleted_at' => $this->deleted_at?->format('Y-m-d H:i:s'),
            'created_by' => [
                'id' => $this->createdBy->id,
                'name' => $this->createdBy->first_name . ' ' . $this->createdBy->father_name . ' ' . $this->createdBy->last_name,
            ],
        ];
    }
}
