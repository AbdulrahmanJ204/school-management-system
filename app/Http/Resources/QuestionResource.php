<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use function PHPUnit\Framework\isString;

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
            'id' => $this->id,
            'quiz_id' => $this->quiz_id,
            'question_text' =>
                json_decode($this->question_text)
            ,
            'question_photo' => $this->question_photo
                ? asset('storage/' . $this->question_photo)
                : asset('storage/question_images/default.png'),
            'choices' => $this->choices,
            'right_choice' => $this->right_choice,
            'hint' => json_decode($this->hint),
            'hint_photo' => $this->hint_photo
                ? asset('storage/' . $this->hint_photo)
                : asset('storage/hint_images/default.png'),
            'order' => $this->order,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
