<?php

namespace App\Http\Requests;

class CreateQuestionRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'question_text' => 'required',
            'question_photo' => 'nullable|image|mimes:jpg,jpeg,png,webp',
            'choices' => 'required',
            'choices_count'   => 'required|integer|min:2',
            'right_choice' => 'required|integer|min:0',
            'hint' => 'nullable',
            'hint_photo' => 'nullable|image|mimes:jpg,jpeg,png,webp',
            'order' => 'required|integer|min:1',
        ];
    }
}
