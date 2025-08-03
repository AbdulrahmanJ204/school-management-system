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
            'main_subject_id' => [
                'required',
                'integer',
                'exists:main_subjects,id'
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

            'main_subject_id.required' => 'المادة الرئيسية مطلوبة',
            'main_subject_id.integer' => 'المادة الرئيسية يجب أن تكون رقماً صحيحاً',
            'main_subject_id.exists' => 'المادة الرئيسية المحددة غير موجودة',
        ];
    }
}
