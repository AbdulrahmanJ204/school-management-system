<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AppUpdateCheckResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        if ($this->resource === null) {
            return [
                'id' => null,
                'last_version' => $request->input('version'),
                'app_url' => null,
                'change_log' => null,
                'is_force' => false,
                'has_update' => false,
            ];
        }

        return [
            'id' => $this->id,
            'last_version' => $this->version,
            'app_url' => $this->url,
            'change_log' => $this->change_log,
            'is_force' => $this->is_force_update,
            'has_update' => true,
        ];
    }
}
