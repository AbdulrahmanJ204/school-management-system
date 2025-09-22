<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TeacherAssignmentResource extends JsonResource
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
            'type' => $this->type,
            'subjectName' => $this->subject->name,
            'teacherName' => $this->createdBy->first_name . ' ' . $this->createdBy->father_name . ' ' . $this->createdBy->last_name,
            'title' => $this->title,
            'description' => $this->description,
            'creation' => [
                'date' => $this->assignedSession->schoolDay->date->format('Y-m-d'),
                'periodNumber' => $this->assignedSession->classPeriod->period_order,
            ],
            'delivery' => $this->dueSession ? [
                'date' => $this->dueSession->schoolDay->date->format('Y-m-d'),
                'periodNumber' => $this->dueSession->classPeriod->period_order,
            ] : null,
            'imageUrl' => $this->photo ? asset('storage/' . $this->photo) : null,
        ];
    }
}

