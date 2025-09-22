<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rule;

class CreateMainSubjectWithSubjectRequest extends BaseRequest
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
            // Main Subject fields
            'grade_id' => ['required', 'integer', 'exists:grades,id'],
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('main_subjects', 'name')
                    ->where('grade_id', $this->grade_id)
            ],
            'code' => [
                'required',
                'string',
                'max:10',
                Rule::unique('main_subjects', 'code')
            ],
            'success_rate' => [
                'required',
                'integer',
                'min:0',
                'max:100'
            ],

            // Subject fields
            'subject_name' => [
                'required',
                'string',
                'max:255'
            ],
            'subject_code' => [
                'required',
                'string',
                'max:10'
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
            // Main Subject messages
            'grade_id.required' => 'حقل الصف مطلوب',
            'grade_id.integer' => 'حقل الصف يجب أن يكون رقماً صحيحاً',
            'grade_id.exists' => 'الصف المحدد غير موجود',

            'name.required' => 'اسم المادة الرئيسية مطلوب',
            'name.string' => 'اسم المادة الرئيسية يجب أن يكون نصاً',
            'name.max' => 'اسم المادة الرئيسية يجب ألا يتجاوز 255 حرفاً',
            'name.unique' => 'اسم المادة الرئيسية موجود مسبقاً لهذا الصف',

            'code.required' => 'رمز المادة الرئيسية مطلوب',
            'code.string' => 'رمز المادة الرئيسية يجب أن يكون نصاً',
            'code.max' => 'رمز المادة الرئيسية يجب ألا يتجاوز 10 أحرف',
            'code.unique' => 'رمز المادة الرئيسية موجود مسبقاً',

            'success_rate.required' => 'معدل النجاح مطلوب',
            'success_rate.integer' => 'معدل النجاح يجب أن يكون رقماً صحيحاً',
            'success_rate.min' => 'معدل النجاح يجب أن يكون على الأقل 0',
            'success_rate.max' => 'معدل النجاح يجب ألا يتجاوز 100',

            // Subject messages
            'subject_name.required' => 'اسم المادة مطلوب',
            'subject_name.string' => 'اسم المادة يجب أن يكون نصاً',
            'subject_name.max' => 'اسم المادة يجب ألا يتجاوز 255 حرفاً',

            'subject_code.required' => 'رمز المادة مطلوب',
            'subject_code.string' => 'رمز المادة يجب أن يكون نصاً',
            'subject_code.max' => 'رمز المادة يجب ألا يتجاوز 10 أحرف',

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
