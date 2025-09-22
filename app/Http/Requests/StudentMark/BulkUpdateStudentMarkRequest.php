<?php

namespace App\Http\Requests\StudentMark;

use App\Http\Requests\BaseRequest;
use App\Models\Subject;
use App\Models\StudentMark;
use Illuminate\Contracts\Validation\ValidationRule;

class BulkUpdateStudentMarkRequest extends BaseRequest
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
            'marks' => 'required|array|min:1',
            'marks.*.id' => [
                'required',
                'integer',
                'exists:student_marks,id'
            ],
            'marks.*.subject_id' => [
                'required',
                'integer',
                'exists:subjects,id'
            ],
            'marks.*.enrollment_id' => [
                'required',
                'integer',
                'exists:student_enrollments,id'
            ],
            'marks.*.homework' => [
                'nullable',
                'integer',
                'min:0'
            ],
            'marks.*.oral' => [
                'nullable',
                'integer',
                'min:0'
            ],
            'marks.*.activity' => [
                'nullable',
                'integer',
                'min:0'
            ],
            'marks.*.quiz' => [
                'nullable',
                'integer',
                'min:0'
            ],
            'marks.*.exam' => [
                'nullable',
                'integer',
                'min:0'
            ],
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $marks = $this->input('marks', []);
            
            foreach ($marks as $index => $mark) {
                // Check if at least one mark type is provided
                $hasMarks = ($mark['homework'] ?? null) || 
                           ($mark['oral'] ?? null) || 
                           ($mark['activity'] ?? null) || 
                           ($mark['quiz'] ?? null) || 
                           ($mark['exam'] ?? null);

                if (!$hasMarks) {
                    $validator->errors()->add("marks.{$index}.marks", 'يجب إدخال درجة واحدة على الأقل للعنصر ' . ($index + 1));
                }

                // Validate max values based on subject
                if (isset($mark['subject_id'])) {
                    $subject = Subject::find($mark['subject_id']);
                    if ($subject) {
                        $homeworkMax = $subject->homework_percentage * $subject->full_mark / 100;
                        $oralMax = $subject->oral_percentage * $subject->full_mark / 100;
                        $activityMax = $subject->activity_percentage * $subject->full_mark / 100;
                        $quizMax = $subject->quiz_percentage * $subject->full_mark / 100;
                        $examMax = $subject->exam_percentage * $subject->full_mark / 100;

                        if (isset($mark['homework']) && $mark['homework'] > $homeworkMax) {
                            $validator->errors()->add("marks.{$index}.homework", "درجة الواجبات يجب ألا تتجاوز {$homeworkMax}");
                        }
                        if (isset($mark['oral']) && $mark['oral'] > $oralMax) {
                            $validator->errors()->add("marks.{$index}.oral", "درجة الشفوي يجب ألا تتجاوز {$oralMax}");
                        }
                        if (isset($mark['activity']) && $mark['activity'] > $activityMax) {
                            $validator->errors()->add("marks.{$index}.activity", "درجة الأنشطة يجب ألا تتجاوز {$activityMax}");
                        }
                        if (isset($mark['quiz']) && $mark['quiz'] > $quizMax) {
                            $validator->errors()->add("marks.{$index}.quiz", "درجة الاختبارات يجب ألا تتجاوز {$quizMax}");
                        }
                        if (isset($mark['exam']) && $mark['exam'] > $examMax) {
                            $validator->errors()->add("marks.{$index}.exam", "درجة الامتحان يجب ألا تتجاوز {$examMax}");
                        }
                    }
                }

                // Validate that the student mark exists and belongs to the correct enrollment and subject
                if (isset($mark['id'], $mark['enrollment_id'], $mark['subject_id'])) {
                    $studentMark = StudentMark::find($mark['id']);
                    if ($studentMark) {
                        if ($studentMark->enrollment_id != $mark['enrollment_id']) {
                            $validator->errors()->add("marks.{$index}.enrollment_id", 'معرف التسجيل لا يطابق الدرجة المحددة');
                        }
                        if ($studentMark->subject_id != $mark['subject_id']) {
                            $validator->errors()->add("marks.{$index}.subject_id", 'معرف المادة لا يطابق الدرجة المحددة');
                        }
                    }
                }
            }

            // Check for duplicate IDs
            $ids = [];
            foreach ($marks as $index => $mark) {
                if (isset($mark['id'])) {
                    if (in_array($mark['id'], $ids)) {
                        $validator->errors()->add("marks.{$index}.id", 'تم تكرار نفس معرف الدرجة');
                    }
                    $ids[] = $mark['id'];
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
            'marks.required' => 'بيانات الدرجات مطلوبة',
            'marks.array' => 'بيانات الدرجات يجب أن تكون مصفوفة',
            'marks.min' => 'يجب إدخال درجة واحدة على الأقل',
            
            'marks.*.id.required' => 'معرف الدرجة مطلوب',
            'marks.*.id.integer' => 'معرف الدرجة يجب أن يكون رقماً صحيحاً',
            'marks.*.id.exists' => 'الدرجة المحددة غير موجودة',

            'marks.*.subject_id.required' => 'المادة مطلوبة',
            'marks.*.subject_id.integer' => 'المادة يجب أن تكون رقماً صحيحاً',
            'marks.*.subject_id.exists' => 'المادة المحددة غير موجودة',

            'marks.*.enrollment_id.required' => 'تسجيل الطالب مطلوب',
            'marks.*.enrollment_id.integer' => 'تسجيل الطالب يجب أن يكون رقماً صحيحاً',
            'marks.*.enrollment_id.exists' => 'تسجيل الطالب المحدد غير موجود',

            'marks.*.homework.integer' => 'درجة الواجبات يجب أن تكون رقماً صحيحاً',
            'marks.*.homework.min' => 'درجة الواجبات يجب أن تكون على الأقل 0',

            'marks.*.oral.integer' => 'درجة الشفوي يجب أن تكون رقماً صحيحاً',
            'marks.*.oral.min' => 'درجة الشفوي يجب أن تكون على الأقل 0',

            'marks.*.activity.integer' => 'درجة الأنشطة يجب أن تكون رقماً صحيحاً',
            'marks.*.activity.min' => 'درجة الأنشطة يجب أن تكون على الأقل 0',

            'marks.*.quiz.integer' => 'درجة الاختبارات يجب أن تكون رقماً صحيحاً',
            'marks.*.quiz.min' => 'درجة الاختبارات يجب أن تكون على الأقل 0',

            'marks.*.exam.integer' => 'درجة الامتحان يجب أن تكون رقماً صحيحاً',
            'marks.*.exam.min' => 'درجة الامتحان يجب أن تكون على الأقل 0',
        ];
    }
}
