<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ListQuizzesRequest extends FormRequest
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
            'grade_id'   => 'sometimes|integer|exists:grades,id',
            'section_id' => 'sometimes|integer|exists:sections,id',
            'subject_id' => 'sometimes|integer|exists:subjects,id',
            'year_id'    => 'sometimes|integer|exists:years,id'
        ];
    }
}
