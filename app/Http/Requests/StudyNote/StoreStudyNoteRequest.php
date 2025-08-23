<?php

namespace App\Http\Requests\StudyNote;

use App\Http\Requests\BaseRequest;
use Illuminate\Contracts\Validation\ValidationRule;

class StoreStudyNoteRequest extends BaseRequest
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
            'subject_id' => [
                'required',
                'integer',
                'exists:subjects,id'
            ],
            'note_type' => [
                'required',
                'string',
                'in:dictation,quiz,homework,general'
            ],
            'school_day_id' => [
                'nullable',
                'integer',
                'exists:school_days,id'
            ],
            'note' => [
                'required',
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
            'student_id.required' => 'الطالب مطلوب',
            'student_id.integer' => 'الطالب يجب أن يكون رقماً صحيحاً',
            'student_id.exists' => 'الطالب المحدد غير موجود',

            'subject_id.required' => 'المادة مطلوبة',
            'subject_id.integer' => 'المادة يجب أن تكون رقماً صحيحاً',
            'subject_id.exists' => 'المادة المحددة غير موجودة',

            'note_type.required' => 'نوع الملاحظة مطلوب',
            'note_type.string' => 'نوع الملاحظة يجب أن يكون نصاً',
            'note_type.in' => 'نوع الملاحظة يجب أن يكون إملاء أو اختبار أو واجب منزلي أو عام',

            'school_day_id.nullable' => 'اليوم الدراسي اختياري',
            'school_day_id.integer' => 'اليوم الدراسي يجب أن يكون رقماً صحيحاً',
            'school_day_id.exists' => 'اليوم الدراسي المحدد غير موجود',

            'note.required' => 'الملاحظة مطلوبة',
            'note.string' => 'الملاحظة يجب أن تكون نصاً',
            'note.max' => 'الملاحظة يجب أن لا تتجاوز 1000 حرف',

            'marks.nullable' => 'الدرجة اختيارية',
            'marks.integer' => 'الدرجة يجب أن تكون رقماً صحيحاً',
            'marks.min' => 'الدرجة يجب أن تكون 0 أو أكثر',
            'marks.max' => 'الدرجة يجب أن تكون 10 أو أقل'
        ];
    }
}
