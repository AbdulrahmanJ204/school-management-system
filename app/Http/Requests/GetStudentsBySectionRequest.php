<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GetStudentsBySectionRequest extends FormRequest
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
            'sectionId' => 'required|integer|exists:sections,id',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'sectionId' => $this->route('sectionId'),
        ]);
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'sectionId.required' => 'Section ID is required.',
            'sectionId.integer' => 'Section ID must be an integer.',
            'sectionId.exists' => 'The selected section does not exist.',
        ];
    }
}
