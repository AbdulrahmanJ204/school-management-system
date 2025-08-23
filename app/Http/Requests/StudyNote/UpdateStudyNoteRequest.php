<?php

namespace App\Http\Requests\StudyNote;

use Illuminate\Contracts\Validation\ValidationRule;

class UpdateStudyNoteRequest extends \App\Http\Requests\BaseRequest
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
                'sometimes',
                'integer',
                'exists:school_days,id'
            ],
            'note_type' => [
                'sometimes',
                'string',
                'in:dictation,quiz,homework,general'
            ],
            'note' => [
                'sometimes',
                'string',
                'max:1000'
            ],
            'marks' => [
                'nullable',
                'integer',
                'min:0',
                'max:10'
            ]
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'school_day_id.sometimes' => 'اليوم الدراسي اختياري للتحديث',
            'school_day_id.integer' => 'اليوم الدراسي يجب أن يكون رقماً صحيحاً',
            'school_day_id.exists' => 'اليوم الدراسي المحدد غير موجود',

            'note_type.sometimes' => 'نوع الملاحظة اختياري للتحديث',
            'note_type.string' => 'نوع الملاحظة يجب أن يكون نصاً',
            'note_type.in' => 'نوع الملاحظة يجب أن يكون إملاء أو اختبار أو واجب منزلي أو عام',

            'note.sometimes' => 'الملاحظة اختيارية للتحديث',
            'note.string' => 'الملاحظة يجب أن تكون نصاً',
            'note.max' => 'الملاحظة يجب أن لا تتجاوز 1000 حرف',

            'marks.nullable' => 'الدرجة اختيارية',
            'marks.integer' => 'الدرجة يجب أن تكون رقماً صحيحاً',
            'marks.min' => 'الدرجة يجب أن تكون 0 أو أكثر',
            'marks.max' => 'الدرجة يجب أن تكون 10 أو أقل'
        ];
    }
}
