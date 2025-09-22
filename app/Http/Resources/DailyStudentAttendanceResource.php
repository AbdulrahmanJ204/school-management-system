<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DailyStudentAttendanceResource extends JsonResource
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
            'full_name' => trim("{$this->user->first_name} {$this->user->father_name} {$this->user->last_name}"),
            'status' => $this->status ?? 'present', // Default to present if no status
        ];
    }
}
