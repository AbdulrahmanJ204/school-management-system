<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GetStudentsBySectionSemesterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'section_id' => 'required|integer|exists:sections,id',
            'semester_id' => 'required|integer|exists:semesters,id',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'section_id.required' => 'Section ID is required.',
            'section_id.integer' => 'Section ID must be an integer.',
            'section_id.exists' => 'The selected section does not exist.',
            'semester_id.required' => 'Semester ID is required.',
            'semester_id.integer' => 'Semester ID must be an integer.',
            'semester_id.exists' => 'The selected semester does not exist.',
        ];
    }
}
