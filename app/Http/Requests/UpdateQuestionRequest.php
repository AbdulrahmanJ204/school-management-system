<?php

namespace App\Http\Requests;

class UpdateQuestionRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth('api')->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'question_text' => 'nullable',
            'question_photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'choices' => 'nullable',
            'choices_count'   => 'nullable|integer|min:2',
            'right_choice' => 'nullable|integer|min:0',
            'hint' => 'nullable',
            'hint_photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'order' => 'nullable|integer|min:1',
        ];
    }
}
