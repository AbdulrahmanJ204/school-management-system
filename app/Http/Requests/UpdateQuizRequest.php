<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateQuizRequest extends FormRequest
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
            'is_active' => 'prohibited',
            'taken_at' => 'prohibited',
            'name' => 'nullable|string|max:255|unique:quizzes,name',
            'full_score'  => 'nullable|integer|min:1',
            'quiz_photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'grade_id'    => 'nullable|exists:grades,id',
            'subject_id'  => 'nullable|exists:subjects,id',
            'semester_id' => 'nullable|exists:semesters,id',
            'section_ids' => 'nullable|array',
            'section_ids.*' => 'exists:sections,id',
        ];
    }
}
