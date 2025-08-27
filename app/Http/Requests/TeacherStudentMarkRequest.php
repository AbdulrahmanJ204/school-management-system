<?php

namespace App\Http\Requests;

use App\Models\Subject;
use Illuminate\Contracts\Validation\ValidationRule;

class TeacherStudentMarkRequest extends BaseRequest
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
        $subject = null;
        if ($this->has('subject_id')) {
            $subject = Subject::find($this->subject_id);
        }

        $homeworkMax = $subject ? $subject->full_mark * $subject->homework_percentage / 100 : 100;
        $oralMax = $subject ? $subject->full_mark * $subject->oral_percentage / 100 : 100;
        $activityMax = $subject ? $subject->full_mark * $subject->activity_percentage / 100 : 100;
        $quizMax = $subject ? $subject->full_mark * $subject->quiz_percentage / 100 : 100;
        $examMax = $subject ? $subject->full_mark * $subject->exam_percentage / 100 : 100;

        return [
            'subject_id' => [
                'required',
                'integer',
                'exists:subjects,id'
            ],
            'semester_id' => [
                'required',
                'integer',
                'exists:semesters,id'
            ],
            'section_id' => [
                'required',
                'integer',
                'exists:sections,id'
            ],
            'homework' => [
                'nullable',
                'integer',
                'min:0',
                "max:{$homeworkMax}"
            ],
            'oral' => [
                'nullable',
                'integer',
                'min:0',
                "max:{$oralMax}"
            ],
            'activity' => [
                'nullable',
                'integer',
                'min:0',
                "max:{$activityMax}"
            ],
            'quiz' => [
                'nullable',
                'integer',
                'min:0',
                "max:{$quizMax}"
            ],
            'exam' => [
                'nullable',
                'integer',
                'min:0',
                "max:{$examMax}"
            ]
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Check if at least one mark is provided
            $hasMarks = $this->homework !== null || 
                       $this->oral !== null || 
                       $this->activity !== null || 
                       $this->quiz !== null || 
                       $this->exam !== null;

            if (!$hasMarks) {
                $validator->errors()->add('marks', 'يجب إدخال علامة واحدة على الأقل');
            }
        });
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'subject_id.required' => 'معرف المادة مطلوب',
            'subject_id.integer' => 'معرف المادة يجب أن تكون رقماً صحيحاً',
            'subject_id.exists' => 'المادة المحددة غير موجودة',

            'semester_id.required' => 'معرف الفصل الدراسي مطلوب',
            'semester_id.integer' => 'معرف الفصل الدراسي يجب أن يكون رقماً صحيحاً',
            'semester_id.exists' => 'الفصل الدراسي المحدد غير موجود',

            'section_id.required' => 'معرف الشعبة مطلوب',
            'section_id.integer' => 'معرف الشعبة يجب أن يكون رقماً صحيحاً',
            'section_id.exists' => 'الشعبة المحددة غير موجودة',

            'homework.integer' => 'علامة الواجب يجب أن تكون رقماً صحيحاً',
            'homework.min' => 'علامة الواجب يجب أن تكون على الأقل 0',
            'homework.max' => 'علامة الواجب يجب ألا تتجاوز الدرجة الكاملة للمادة',

            'oral.integer' => 'علامة الشفوي يجب أن تكون رقماً صحيحاً',
            'oral.min' => 'علامة الشفوي يجب أن تكون على الأقل 0',
            'oral.max' => 'علامة الشفوي يجب ألا تتجاوز الدرجة الكاملة للمادة',

            'activity.integer' => 'علامة النشاط يجب أن تكون رقماً صحيحاً',
            'activity.min' => 'علامة النشاط يجب أن تكون على الأقل 0',
            'activity.max' => 'علامة النشاط يجب ألا تتجاوز الدرجة الكاملة للمادة',

            'quiz.integer' => 'علامة الاختبار يجب أن تكون رقماً صحيحاً',
            'quiz.min' => 'علامة الاختبار يجب أن تكون على الأقل 0',
            'quiz.max' => 'علامة الاختبار يجب ألا تتجاوز الدرجة الكاملة للمادة',

            'exam.integer' => 'علامة الامتحان يجب أن تكون رقماً صحيحاً',
            'exam.min' => 'علامة الامتحان يجب أن تكون على الأقل 0',
            'exam.max' => 'علامة الامتحان يجب ألا تتجاوز الدرجة الكاملة للمادة',

            'marks.required' => 'يجب إدخال علامة واحدة على الأقل',
        ];
    }
}
