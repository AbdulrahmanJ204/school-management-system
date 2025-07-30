<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StudentMarkRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $studentMarkId = $this->route('student_mark') ? $this->route('student_mark')->id : null;

        return [
            'subject_id' => [
                'required',
                'integer',
                'exists:subjects,id'
            ],
            'enrollment_id' => [
                'required',
                'integer',
                'exists:student_enrollments,id'
            ],
            'homework' => [
                'nullable',
                'integer',
                'min:0',
                'max:100'
            ],
            'oral' => [
                'nullable',
                'integer',
                'min:0',
                'max:100'
            ],
            'activity' => [
                'nullable',
                'integer',
                'min:0',
                'max:100'
            ],
            'quiz' => [
                'nullable',
                'integer',
                'min:0',
                'max:100'
            ],
            'exam' => [
                'nullable',
                'integer',
                'min:0',
                'max:100'
            ],
            'total' => [
                'nullable',
                'numeric',
                'min:0',
                'max:100'
            ]
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Check if at least one mark type is provided
            $hasMarks = $this->homework || $this->oral || $this->activity || $this->quiz || $this->exam;
            
            if (!$hasMarks) {
                $validator->errors()->add('marks', 'يجب إدخال درجة واحدة على الأقل');
            }

            // Check if enrollment and subject combination is unique (for create only)
            if (!$this->route('student_mark')) {
                $existingMark = \App\Models\StudentMark::where('enrollment_id', $this->enrollment_id)
                    ->where('subject_id', $this->subject_id)
                    ->first();

                if ($existingMark) {
                    $validator->errors()->add('combination', 'يوجد درجة مسجلة مسبقاً لهذا الطالب في هذه المادة');
                }
            }
        });
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'subject_id.required' => 'المادة مطلوبة',
            'subject_id.integer' => 'المادة يجب أن تكون رقماً صحيحاً',
            'subject_id.exists' => 'المادة المحددة غير موجودة',

            'enrollment_id.required' => 'تسجيل الطالب مطلوب',
            'enrollment_id.integer' => 'تسجيل الطالب يجب أن يكون رقماً صحيحاً',
            'enrollment_id.exists' => 'تسجيل الطالب المحدد غير موجود',

            'homework.nullable' => 'درجة الواجبات اختيارية',
            'homework.integer' => 'درجة الواجبات يجب أن تكون رقماً صحيحاً',
            'homework.min' => 'درجة الواجبات يجب أن تكون على الأقل 0',
            'homework.max' => 'درجة الواجبات يجب ألا تتجاوز 100',

            'oral.nullable' => 'درجة الشفوي اختيارية',
            'oral.integer' => 'درجة الشفوي يجب أن تكون رقماً صحيحاً',
            'oral.min' => 'درجة الشفوي يجب أن تكون على الأقل 0',
            'oral.max' => 'درجة الشفوي يجب ألا تتجاوز 100',

            'activity.nullable' => 'درجة الأنشطة اختيارية',
            'activity.integer' => 'درجة الأنشطة يجب أن تكون رقماً صحيحاً',
            'activity.min' => 'درجة الأنشطة يجب أن تكون على الأقل 0',
            'activity.max' => 'درجة الأنشطة يجب ألا تتجاوز 100',

            'quiz.nullable' => 'درجة الاختبارات اختيارية',
            'quiz.integer' => 'درجة الاختبارات يجب أن تكون رقماً صحيحاً',
            'quiz.min' => 'درجة الاختبارات يجب أن تكون على الأقل 0',
            'quiz.max' => 'درجة الاختبارات يجب ألا تتجاوز 100',

            'exam.nullable' => 'درجة الامتحان اختيارية',
            'exam.integer' => 'درجة الامتحان يجب أن تكون رقماً صحيحاً',
            'exam.min' => 'درجة الامتحان يجب أن تكون على الأقل 0',
            'exam.max' => 'درجة الامتحان يجب ألا تتجاوز 100',

            'total.nullable' => 'الدرجة الإجمالية اختيارية',
            'total.numeric' => 'الدرجة الإجمالية يجب أن تكون رقماً',
            'total.min' => 'الدرجة الإجمالية يجب أن تكون على الأقل 0',
            'total.max' => 'الدرجة الإجمالية يجب ألا تتجاوز 100',
        ];
    }
} 