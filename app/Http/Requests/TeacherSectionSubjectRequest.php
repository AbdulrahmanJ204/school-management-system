<?php

namespace App\Http\Requests;

use App\Models\TeacherSectionSubject;
use Illuminate\Contracts\Validation\ValidationRule;

class TeacherSectionSubjectRequest extends BaseRequest
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
            'teacher_id' => [
                'required',
                'integer',
                'exists:teachers,id'
            ],
            'subject_id' => [
                'required',
                'integer',
                'exists:subjects,id'
            ],
            'section_id' => [
                'required',
                'integer',
                'exists:sections,id'
            ],
            'is_active' => [
                'required',
                'boolean'
            ],
            'num_class_period' => [
                'required',
                'integer',
                'min:1'
            ]
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {

            // Check if teacher and subject and section combination is  unique (for create)
            if (!$this->route('teacher-section-subject') && $this->isMethod('post')) {
                $existingMark = TeacherSectionSubject::where('teacher_id', $this->teacher_id)
                    ->where('subject_id', $this->subject_id)
                    ->where('section_id', $this->section_id)
                    ->first();

                if ($existingMark) {
                    $validator->errors()->add('combination', 'يوجد ارتباط مسبق للمعلم بهذه المادة والشعبة');
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
            'teacher_id.required' => 'المعلم مطلوب',
            'teacher_id.integer' => 'المعلم يجب أن يكون رقماً صحيحاً',
            'teacher_id.exists' => 'المعلم المحدد غير موجود',

            'subject_id.required' => 'المادة مطلوبة',
            'subject_id.integer' => 'المادة يجب أن تكون رقماً صحيحاً',
            'subject_id.exists' => 'المادة المحددة غير موجودة',

            'section_id.required' => 'الشعبة مطلوبة',
            'section_id.integer' => 'الشعبة يجب أن تكون رقماً صحيحاً',
            'section_id.exists' => 'الشعبة المحددة غير موجودة',

            'is_active.required' => 'حالة النشاط مطلوبة',
            'is_active.boolean' => 'حالة النشاط يجب أن تكون true أو false',

            'num_class_period.required' => 'عدد الحصص مطلوب',
            'num_class_period.integer' => 'عدد الحصص يجب أن يكون رقماً صحيحاً',
            'num_class_period.min' => 'عدد الحصص يجب أن يكون 1 على الأقل',
        ];
    }
}
