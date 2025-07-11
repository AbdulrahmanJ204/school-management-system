<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuestionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id'              => $this->id,
            'quiz_id'         => $this->quiz_id,
            'question_text'   => $this->question_text,
            'question_photo'  => $this->$this->question_photo
                ? asset('storage/' . $this->question_photo)
                : asset('storage/question_images/default.png'),
            'choices'         => $this->choices,
            'right_choice'    => $this->right_choice,
            'hint'            => $this->hint,
            'hint_photo'      => $this->hint_photo
                ? asset('storage/' . $this->hint_photo)
                : asset('storage/hint_images/default.png'),
            'order'           => $this->order,
            'created_at'      => $this->created_at,
            'updated_at'      => $this->updated_at,
        ];
    }
}
