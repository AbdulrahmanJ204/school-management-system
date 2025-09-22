<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;

class ExamRequest extends BaseRequest
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
            'school_day_id' => [
                'required',
                'integer',
                'exists:school_days,id'
            ],
            'subject_id' => [
                'required',
                'integer',
                'exists:subjects,id'
            ],
            'type' => [
                'required',
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
            'school_day_id.required' => 'اليوم الدراسي مطلوب',
            'school_day_id.integer' => 'اليوم الدراسي يجب أن يكون رقماً صحيحاً',
            'school_day_id.exists' => 'اليوم الدراسي المحدد غير موجود',

            'subject_id.required' => 'المادة مطلوبة',
            'subject_id.integer' => 'المادة يجب أن تكون رقماً صحيحاً',
            'subject_id.exists' => 'المادة المحددة غير موجودة',

            'type.required' => 'نوع الامتحان مطلوب',
            'type.string' => 'نوع الامتحان يجب أن يكون نصاً',
            'type.in' => 'نوع الامتحان يجب أن يكون إما امتحان أو مذاكرة',
        ];
    }
}
