<?php

namespace App\Http\Requests\StudentMark;

use App\Http\Requests\BaseRequest;
use App\Models\Subject;
use Illuminate\Contracts\Validation\ValidationRule;

class UpdateStudentMarkRequest extends BaseRequest
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

        $homeworkMax = $subject ? $subject->homework_percentage * $subject->full_mark / 100 : 100;
        $oralMax     = $subject ? $subject->oral_percentage     * $subject->full_mark / 100 : 100;
        $activityMax = $subject ? $subject->activity_percentage * $subject->full_mark / 100 : 100;
        $quizMax     = $subject ? $subject->quiz_percentage     * $subject->full_mark / 100 : 100;
        $examMax     = $subject ? $subject->exam_percentage     * $subject->full_mark / 100 : 100;

        return [
            'subject_id' => [
                'sometimes',
                'integer',
                'exists:subjects,id'
            ],
            'enrollment_id' => [
                'sometimes',
                'integer',
                'exists:student_enrollments,id'
            ],
            'homework' => [
                'nullable',
                'integer',
                'min:0',
                'max:' . $homeworkMax
            ],
            'oral' => [
                'nullable',
                'integer',
                'min:0',
                'max:' . $oralMax
            ],
            'activity' => [
                'nullable',
                'integer',
                'min:0',
                'max:' . $activityMax
            ],
            'quiz' => [
                'nullable',
                'integer',
                'min:0',
                'max:' . $quizMax
            ],
            'exam' => [
                'nullable',
                'integer',
                'min:0',
                'max:' . $examMax
            ],
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Check if at least one mark type is provided
            $hasMarks = $this->homework || $this->oral || $this->activity || $this->quiz || $this->exam;

            if (!$hasMarks) {
                $validator->errors()->add('marks', 'يجب إدخال درجة واحدة على الأقل');
            }
        });
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        $subject = null;
        if ($this->has('subject_id')) {
            $subject = Subject::find($this->subject_id);
        }

        $homeworkMax = $subject ? $subject->homework_percentage * $subject->full_mark / 100 : 100;
        $oralMax     = $subject ? $subject->oral_percentage     * $subject->full_mark / 100 : 100;
        $activityMax = $subject ? $subject->activity_percentage * $subject->full_mark / 100 : 100;
        $quizMax     = $subject ? $subject->quiz_percentage     * $subject->full_mark / 100 : 100;
        $examMax     = $subject ? $subject->exam_percentage     * $subject->full_mark / 100 : 100;

        return [
            'subject_id.integer' => 'المادة يجب أن تكون رقماً صحيحاً',
            'subject_id.exists' => 'المادة المحددة غير موجودة',

            'enrollment_id.integer' => 'تسجيل الطالب يجب أن يكون رقماً صحيحاً',
            'enrollment_id.exists' => 'تسجيل الطالب المحدد غير موجود',

            'homework.nullable' => 'درجة الواجبات اختيارية',
            'homework.integer' => 'درجة الواجبات يجب أن تكون رقماً صحيحاً',
            'homework.min' => 'درجة الواجبات يجب أن تكون على الأقل 0',
            'homework.max' => 'درجة الواجبات يجب ألا تتجاوز ' . $homeworkMax,

            'oral.nullable' => 'درجة الشفوي اختيارية',
            'oral.integer' => 'درجة الشفوي يجب أن تكون رقماً صحيحاً',
            'oral.min' => 'درجة الشفوي يجب أن تكون على الأقل 0',
            'oral.max' => 'درجة الشفوي يجب ألا تتجاوز ' . $oralMax,

            'activity.nullable' => 'درجة الأنشطة اختيارية',
            'activity.integer' => 'درجة الأنشطة يجب أن تكون رقماً صحيحاً',
            'activity.min' => 'درجة الأنشطة يجب أن تكون على الأقل 0',
            'activity.max' => 'درجة الأنشطة يجب ألا تتجاوز ' . $activityMax,

            'quiz.nullable' => 'درجة الاختبارات اختيارية',
            'quiz.integer' => 'درجة الاختبارات يجب أن تكون رقماً صحيحاً',
            'quiz.min' => 'درجة الاختبارات يجب أن تكون على الأقل 0',
            'quiz.max' => 'درجة الاختبارات يجب ألا تتجاوز ' . $quizMax,

            'exam.nullable' => 'درجة الامتحان اختيارية',
            'exam.integer' => 'درجة الامتحان يجب أن تكون رقماً صحيحاً',
            'exam.min' => 'درجة الامتحان يجب أن تكون على الأقل 0',
            'exam.max' => 'درجة الامتحان يجب ألا تتجاوز ' . $examMax,
        ];
    }
}
