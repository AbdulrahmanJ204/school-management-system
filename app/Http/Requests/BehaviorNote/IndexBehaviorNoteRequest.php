<?php

namespace App\Http\Requests\BehaviorNote;

use App\Http\Requests\BaseRequest;
use Illuminate\Contracts\Validation\ValidationRule;

class IndexBehaviorNoteRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->hasPermissionTo('عرض ملاحظات السلوك');
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
                'nullable',
                'integer',
                'exists:students,id'
            ],
            'school_day_id' => [
                'nullable',
                'integer',
                'exists:school_days,id'
            ],
            'semester_id' => [
                'nullable',
                'integer',
                'exists:semesters,id'
            ],
            'behavior_type' => [
                'nullable',
                'string',
                'in:positive,negative'
            ],
            'section_id' => [
                'nullable',
                'integer',
                'exists:sections,id'
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
            ],
            'per_page' => [
                'nullable',
                'integer',
                'min:1',
                'max:100'
            ]
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'student_id.nullable' => 'الطالب اختياري',
            'student_id.integer' => 'الطالب يجب أن يكون رقماً صحيحاً',
            'student_id.exists' => 'الطالب المحدد غير موجود',

            'school_day_id.nullable' => 'اليوم الدراسي اختياري',
            'school_day_id.integer' => 'اليوم الدراسي يجب أن يكون رقماً صحيحاً',
            'school_day_id.exists' => 'اليوم الدراسي المحدد غير موجود',

            'semester_id.nullable' => 'الفصل الدراسي اختياري',
            'semester_id.integer' => 'الفصل الدراسي يجب أن يكون رقماً صحيحاً',
            'semester_id.exists' => 'الفصل الدراسي المحدد غير موجود',

            'behavior_type.nullable' => 'نوع السلوك اختياري',
            'behavior_type.string' => 'نوع السلوك يجب أن يكون نصاً',
            'behavior_type.in' => 'نوع السلوك يجب أن يكون positive أو negative',

            'section_id.nullable' => 'القسم اختياري',
            'section_id.integer' => 'القسم يجب أن يكون رقماً صحيحاً',
            'section_id.exists' => 'القسم المحدد غير موجود',

            'date_from.nullable' => 'تاريخ البداية اختياري',
            'date_from.date' => 'تاريخ البداية يجب أن يكون تاريخاً صحيحاً',
            'date_from.date_format' => 'تاريخ البداية يجب أن يكون بالتنسيق Y-m-d',

            'date_to.nullable' => 'تاريخ النهاية اختياري',
            'date_to.date' => 'تاريخ النهاية يجب أن يكون تاريخاً صحيحاً',
            'date_to.date_format' => 'تاريخ النهاية يجب أن يكون بالتنسيق Y-m-d',
            'date_to.after_or_equal' => 'تاريخ النهاية يجب أن يكون بعد أو يساوي تاريخ البداية',

            'per_page.nullable' => 'عدد العناصر في الصفحة اختياري',
            'per_page.integer' => 'عدد العناصر في الصفحة يجب أن يكون رقماً صحيحاً',
            'per_page.min' => 'عدد العناصر في الصفحة يجب أن يكون 1 على الأقل',
            'per_page.max' => 'عدد العناصر في الصفحة يجب أن لا يتجاوز 100'
        ];
    }
}
