<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ScheduleCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'timetable' => $this->collection->groupBy('week_day')->map(function ($items, $day) {
                return [
                    'day_name' => $day,
                    'day_date' => optional($items->first()->classSession?->schoolDay)->date,
                    'lectures' => ScheduleResource::collection($items)->values(),
                ];
            })->values()
        ];
    }
}
