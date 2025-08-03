<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rule;

class SubjectRequest extends BaseRequest
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
        $subjectId = $this->route('subject') ? $this->route('subject')->id : null;

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('subjects', 'name')
                    ->where('main_subject_id', $this->main_subject_id)
                    ->ignore($subjectId)
            ],
            'main_subject_id' => ['required', 'integer', 'exists:main_subjects,id'],
            'code' => [
                'required',
                'string',
                'max:10',
                Rule::unique('subjects', 'code')
                    ->ignore($subjectId)
            ],
            'full_mark' => [
                'required',
                'integer',
                'min:1',
                'max:1000'
            ],
            'homework_percentage' => [
                'required',
                'integer',
                'min:0',
                'max:100'
            ],
            'oral_percentage' => [
                'required',
                'integer',
                'min:0',
                'max:100'
            ],
            'activity_percentage' => [
                'required',
                'integer',
                'min:0',
                'max:100'
            ],
            'quiz_percentage' => [
                'required',
                'integer',
                'min:0',
                'max:100'
            ],
            'exam_percentage' => [
                'required',
                'integer',
                'min:0',
                'max:100'
            ],
            'num_class_period' => [
                'required',
                'integer',
                'min:1',
                'max:50'
            ],
            'is_failed' => [
                'boolean'
            ]
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $total = $this->homework_percentage + $this->oral_percentage +
                $this->activity_percentage + $this->quiz_percentage +
                $this->exam_percentage;

            if ($total !== 100) {
                $validator->errors()->add('percentages', 'مجموع النسب المئوية يجب أن يساوي 100%');
            }
        });
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'اسم المادة مطلوب',
            'name.string' => 'اسم المادة يجب أن يكون نصاً',
            'name.max' => 'اسم المادة يجب ألا يتجاوز 255 حرفاً',
            'name.unique' => 'اسم المادة موجود مسبقاً لهذه المادة الرئيسية',

            'main_subject_id.required' => 'المادة الرئيسية مطلوبة',
            'main_subject_id.integer' => 'المادة الرئيسية يجب أن تكون رقماً صحيحاً',
            'main_subject_id.exists' => 'المادة الرئيسية المحددة غير موجودة',

            'code.required' => 'رمز المادة مطلوب',
            'code.string' => 'رمز المادة يجب أن يكون نصاً',
            'code.max' => 'رمز المادة يجب ألا يتجاوز 10 أحرف',
            'code.unique' => 'رمز المادة موجود مسبقاً',

            'full_mark.required' => 'الدرجة الكاملة مطلوبة',
            'full_mark.integer' => 'الدرجة الكاملة يجب أن تكون رقماً صحيحاً',
            'full_mark.min' => 'الدرجة الكاملة يجب أن تكون على الأقل 1',
            'full_mark.max' => 'الدرجة الكاملة يجب ألا تتجاوز 1000',

            'homework_percentage.required' => 'نسبة الواجبات مطلوبة',
            'homework_percentage.integer' => 'نسبة الواجبات يجب أن تكون رقماً صحيحاً',
            'homework_percentage.min' => 'نسبة الواجبات يجب أن تكون على الأقل 0',
            'homework_percentage.max' => 'نسبة الواجبات يجب ألا تتجاوز 100',

            'oral_percentage.required' => 'نسبة الشفوي مطلوبة',
            'oral_percentage.integer' => 'نسبة الشفوي يجب أن تكون رقماً صحيحاً',
            'oral_percentage.min' => 'نسبة الشفوي يجب أن تكون على الأقل 0',
            'oral_percentage.max' => 'نسبة الشفوي يجب ألا تتجاوز 100',

            'activity_percentage.required' => 'نسبة الأنشطة مطلوبة',
            'activity_percentage.integer' => 'نسبة الأنشطة يجب أن تكون رقماً صحيحاً',
            'activity_percentage.min' => 'نسبة الأنشطة يجب أن تكون على الأقل 0',
            'activity_percentage.max' => 'نسبة الأنشطة يجب ألا تتجاوز 100',

            'quiz_percentage.required' => 'نسبة الاختبارات مطلوبة',
            'quiz_percentage.integer' => 'نسبة الاختبارات يجب أن تكون رقماً صحيحاً',
            'quiz_percentage.min' => 'نسبة الاختبارات يجب أن تكون على الأقل 0',
            'quiz_percentage.max' => 'نسبة الاختبارات يجب ألا تتجاوز 100',

            'exam_percentage.required' => 'نسبة الامتحان مطلوبة',
            'exam_percentage.integer' => 'نسبة الامتحان يجب أن تكون رقماً صحيحاً',
            'exam_percentage.min' => 'نسبة الامتحان يجب أن تكون على الأقل 0',
            'exam_percentage.max' => 'نسبة الامتحان يجب ألا تتجاوز 100',

            'num_class_period.required' => 'عدد الحصص مطلوب',
            'num_class_period.integer' => 'عدد الحصص يجب أن يكون رقماً صحيحاً',
            'num_class_period.min' => 'عدد الحصص يجب أن يكون على الأقل 1',
            'num_class_period.max' => 'عدد الحصص يجب ألا يتجاوز 50',
            'is_failed.boolean' => 'حقل الرسوب يجب أن يكون صحيح أو خطأ',
        ];
    }
}
