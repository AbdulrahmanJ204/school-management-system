<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TimeTableResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'valid_from'  => $this->valid_from->format('Y-m-d'),
            'valid_to'    => $this->valid_to->format('Y-m-d'),
            'is_active'   => (bool) $this->is_active,
            'created_by'  => $this->created_by,
            'created_at'  => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
