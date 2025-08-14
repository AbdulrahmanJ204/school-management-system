<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;

class BehaviorNoteRequest extends BaseRequest
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
            'student_id' => [
                'required',
                'integer',
                'exists:students,id'
            ],
            'school_day_id' => [
                'required',
                'integer',
                'exists:school_days,id'
            ],
            'behavior_type' => [
                'required',
                'string',
                'in:positive,negative'
            ],
            'note' => [
                'required',
                'string',
                'max:1000'
            ]
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'student_id.required' => 'الطالب مطلوب',
            'student_id.integer' => 'الطالب يجب أن يكون رقماً صحيحاً',
            'student_id.exists' => 'الطالب المحدد غير موجود',

            'school_day_id.required' => 'اليوم الدراسي مطلوب',
            'school_day_id.integer' => 'اليوم الدراسي يجب أن يكون رقماً صحيحاً',
            'school_day_id.exists' => 'اليوم الدراسي المحدد غير موجود',

            'behavior_type.required' => 'نوع السلوك مطلوب',
            'behavior_type.string' => 'نوع السلوك يجب أن يكون نصاً',
            'behavior_type.in' => 'نوع السلوك يجب أن يكون positive أو negative',

            'note.required' => 'الملاحظة مطلوبة',
            'note.string' => 'الملاحظة يجب أن تكون نصاً',
            'note.max' => 'الملاحظة يجب أن لا تتجاوز 1000 حرف',
        ];
    }
}
