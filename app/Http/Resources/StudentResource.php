<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentResource extends JsonResource
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
            'user_id' => $this->user_id,
            'student_number' => $this->student_number,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'full_name' => $this->first_name . ' ' . $this->last_name,
            'date_of_birth' => $this->date_of_birth,
            'gender' => $this->gender,
            'address' => $this->address,
            'phone' => $this->phone,
            'email' => $this->email,
            'parent_name' => $this->parent_name,
            'parent_phone' => $this->parent_phone,
            'emergency_contact' => $this->emergency_contact,
            'medical_conditions' => $this->medical_conditions,
            'allergies' => $this->allergies,
            'status' => $this->status,
            
            // User relationship
            'user' => new UserResource($this->whenLoaded('user')),
            
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
} 