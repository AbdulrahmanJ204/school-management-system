<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuizResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id'          => $this->id,
            'name'       => $this->name,
            'is_active'   => $this->is_active,
            'taken_at'    => $this->taken_at,
            'created_by'  => $this->created_by,
            'created_at'  => $this->created_at,
        ];
    }
}
