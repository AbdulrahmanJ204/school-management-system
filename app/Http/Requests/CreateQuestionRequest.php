<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Validator;

class CreateQuestionRequest extends FormRequest
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
            'question_text' => 'required|json',
            'question_photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'choices' => 'required|json',
            'choices_count'   => 'required|integer|min:2',
            'right_choice' => 'required|integer|min:0',
            'hint' => 'nullable|string',
            'hint_photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'order' => 'required|integer|min:1',
        ];
    }
}
