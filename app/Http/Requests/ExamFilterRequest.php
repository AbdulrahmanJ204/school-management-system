<?php

namespace App\Http\Requests;

class ExamFilterRequest extends BaseRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'grade_id' => [
                'nullable',
                'integer',
                'exists:grades,id'
            ],
            'school_day_id' => [
                'nullable',
                'integer',
                'exists:school_days,id'
            ],
            'subject_id' => [
                'nullable',
                'integer',
                'exists:subjects,id'
            ],
            'type' => [
                'nullable',
                'string',
                'in:exam,quiz'
            ]
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'grade_id.integer' => 'معرف الصف يجب أن يكون رقماً صحيحاً',
            'grade_id.exists' => 'الصف المحدد غير موجود',

            'school_day_id.integer' => 'معرف اليوم الدراسي يجب أن يكون رقماً صحيحاً',
            'school_day_id.exists' => 'اليوم الدراسي المحدد غير موجود',

            'subject_id.integer' => 'معرف المادة يجب أن يكون رقماً صحيحاً',
            'subject_id.exists' => 'المادة المحددة غير موجودة',

            'type.string' => 'نوع الامتحان يجب أن يكون نصاً',
            'type.in' => 'نوع الامتحان يجب أن يكون إما exam أو quiz',
        ];
    }
}
