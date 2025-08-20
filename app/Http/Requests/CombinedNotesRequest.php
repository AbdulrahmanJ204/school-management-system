<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;

class CombinedNotesRequest extends BaseRequest
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
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'section_id' => [
                'nullable',
                'integer',
                'exists:sections,id'
            ],
            'grade_id' => [
                'nullable',
                'integer',
                'exists:grades,id'
            ],
            'subject_id' => [
                'nullable',
                'integer',
                'exists:subjects,id'
            ],
            'student_id' => [
                'nullable',
                'integer',
                'exists:students,id'
            ]
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'section_id.integer' => 'معرف القسم يجب أن يكون رقماً صحيحاً',
            'section_id.exists' => 'القسم المحدد غير موجود',

            'grade_id.integer' => 'معرف الصف يجب أن يكون رقماً صحيحاً',
            'grade_id.exists' => 'الصف المحدد غير موجود',

            'subject_id.integer' => 'معرف المادة يجب أن يكون رقماً صحيحاً',
            'subject_id.exists' => 'المادة المحددة غير موجودة',

            'student_id.integer' => 'معرف الطالب يجب أن يكون رقماً صحيحاً',
            'student_id.exists' => 'الطالب المحدد غير موجود'
        ];
    }
}
