<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NewsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "title" => $this->title,
            "description" => json_decode($this->content),
            'date'=> $this->schoolDay?->date ?? 'Y-m-d',
            'created_at'=> $this->created_at,
            'photo' => $this->photo ? asset('storage/' . $this->photo) : null, // Full URL
        ];
    }
}
