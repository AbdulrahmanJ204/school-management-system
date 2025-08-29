<?php

namespace App\Http\Resources;

use App\Http\Resources\BaseResource;
use Illuminate\Http\Request;

/**
 * Assignment Resource - Complete assignment information
 * مورد الواجب - معلومات الواجب الكاملة
 * Uses basic data structures to avoid circular dependencies
 * يستخدم هياكل البيانات الأساسية لتجنب التضارب الدوري
 */
class AssignmentResource extends BaseResource
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
            
            // Use basic data structures to avoid circular dependencies
            // استخدام هياكل البيانات الأساسية لتجنب التضارب الدوري
            'subject' => [
                'id' => $this->subject->id,
                'name' => $this->subject->name,
            ],
            'section' => [
                'id' => $this->section->id,
                'title' => $this->section->title,
                'grade' => [
                    'id' => $this->section->grade->id,
                    'name' => $this->section->grade->title,
                ],
            ],
            
            'created_at' => $this->formatDate($this->created_at),
            'updated_at' => $this->formatDate($this->updated_at),
            'deleted_at' => $this->formatDate($this->deleted_at),
            'created_by' => [
                'id' => $this->createdBy->id,
                'name' => $this->createdBy->first_name . ' ' . $this->createdBy->father_name . ' ' . $this->createdBy->last_name,
            ],
        ];
    }
}
