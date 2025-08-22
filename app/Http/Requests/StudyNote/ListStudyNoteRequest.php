<?php

namespace App\Http\Requests\StudyNote;

use Illuminate\Contracts\Validation\ValidationRule;

class ListStudyNoteRequest extends \App\Http\Requests\BaseRequest
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
            'subject_id' => [
                'nullable',
                'integer',
                'exists:subjects,id'
            ],
            'note_type' => [
                'nullable',
                'string',
                'in:dictation,quiz,homework,general'
            ],
            'date_from' => [
                'nullable',
                'date',
                'date_format:Y-m-d'
            ],
            'date_to' => [
                'nullable',
                'date',
                'date_format:Y-m-d',
                'after_or_equal:date_from'
            ]
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'subject_id.nullable' => 'المادة اختيارية',
            'subject_id.integer' => 'المادة يجب أن تكون رقماً صحيحاً',
            'subject_id.exists' => 'المادة المحددة غير موجودة',

            'note_type.nullable' => 'نوع الملاحظة اختياري',
            'note_type.string' => 'نوع الملاحظة يجب أن يكون نصاً',
            'note_type.in' => 'نوع الملاحظة يجب أن يكون إملاء أو اختبار أو واجب منزلي أو عام',

            'date_from.nullable' => 'تاريخ البداية اختياري',
            'date_from.date' => 'تاريخ البداية يجب أن يكون تاريخاً صحيحاً',
            'date_from.date_format' => 'تاريخ البداية يجب أن يكون بالتنسيق Y-m-d',

            'date_to.nullable' => 'تاريخ النهاية اختياري',
            'date_to.date' => 'تاريخ النهاية يجب أن يكون تاريخاً صحيحاً',
            'date_to.date_format' => 'تاريخ النهاية يجب أن يكون بالتنسيق Y-m-d',
            'date_to.after_or_equal' => 'تاريخ النهاية يجب أن يكون بعد أو يساوي تاريخ البداية'
        ];
    }
}
