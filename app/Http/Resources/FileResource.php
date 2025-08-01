<?php

namespace App\Http\Resources;

use App\Enums\UserType;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $user_type = auth()->user()->user_type;
        $array = [
            'id'=>$this->id,
            'name'=>$this->title,
            "description"=> $this->description,
            "type"=> $this->type,
            "size"=> round($this->size  / (1024 * 1024) , 2 ),
            "publish date"=> $this->publish_date->format('Y-m-d h:i:s A'),
            'subject'=>$this->subject?? null
        ];
        if($user_type===UserType::Admin->value){
            $array["deleted_at"] = $this->deleted_at?->format('Y-m-d h:i:s A');


            $targets = $this->whenLoaded('targets') ;
            $grades = GradeResource::collection($targets->whereNotNull('grade')->pluck('grade')->unique()->values());
            $sections = SectionResource::collection($targets->whereNotNull('section')->pluck('section')->unique()->values());

            $targetsArray = [];

            if ($sections->isNotEmpty()) {
                $targetsArray['sections'] = $sections;
            }

            if ($grades->isNotEmpty()) {
                $targetsArray['grades'] = $grades;
            }
            $array["targets"] = $targetsArray;
        }
        return $array;
    }
}
