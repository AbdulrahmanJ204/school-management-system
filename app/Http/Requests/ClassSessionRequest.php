<?php

namespace App\Http\Requests;

use App\Models\ClassSession;
use Illuminate\Contracts\Validation\ValidationRule;

class ClassSessionRequest extends BaseRequest
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
        $classSessionId = $this->route('class_session') ? $this->route('class_session')->id : null;

        return [
            'schedule_id' => [
                'required',
                'integer',
                'exists:schedules,id'
            ],
            'school_day_id' => [
                'required',
                'integer',
                'exists:school_days,id'
            ],
            'class_period_id' => [
                'required',
                'integer',
                'exists:class_periods,id'
            ],
            'status' => [
                'nullable',
                'string',
                'in:scheduled,ongoing,completed,cancelled'
            ],
            'total_students_count' => [
                'nullable',
                'integer',
                'min:0'
            ],
            'present_students_count' => [
                'nullable',
                'integer',
                'min:0'
            ]
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Check if class session already exists for this schedule and school day
            if (!$this->route('class_session')) {
                $existingSession = ClassSession::where('schedule_id', $this->schedule_id)
                    ->where('school_day_id', $this->school_day_id)
                    ->first();

                if ($existingSession) {
                    $validator->errors()->add('session', 'جلسة الفصل موجودة مسبقاً لهذا اليوم والجدول');
                }
            }

            // Validate present_students_count cannot exceed total_students_count
            if ($this->present_students_count && $this->total_students_count) {
                if ($this->present_students_count > $this->total_students_count) {
                    $validator->errors()->add('present_students_count', 'عدد الطلاب الحاضرين لا يمكن أن يتجاوز العدد الإجمالي');
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
            'schedule_id.required' => 'الجدول مطلوب',
            'schedule_id.integer' => 'الجدول يجب أن يكون رقماً صحيحاً',
            'schedule_id.exists' => 'الجدول المحدد غير موجود',

            'school_day_id.required' => 'اليوم الدراسي مطلوب',
            'school_day_id.integer' => 'اليوم الدراسي يجب أن يكون رقماً صحيحاً',
            'school_day_id.exists' => 'اليوم الدراسي المحدد غير موجود',

            'class_period_id.required' => 'الحصة الدراسية مطلوبة',
            'class_period_id.integer' => 'الحصة الدراسية يجب أن تكون رقماً صحيحاً',
            'class_period_id.exists' => 'الحصة الدراسية المحددة غير موجودة',

            'status.nullable' => 'الحالة اختيارية',
            'status.string' => 'الحالة يجب أن تكون نصاً',
            'status.in' => 'الحالة يجب أن تكون واحدة من: scheduled, ongoing, completed, cancelled',

            'total_students_count.nullable' => 'العدد الإجمالي للطلاب اختياري',
            'total_students_count.integer' => 'العدد الإجمالي للطلاب يجب أن يكون رقماً صحيحاً',
            'total_students_count.min' => 'العدد الإجمالي للطلاب يجب أن يكون 0 أو أكثر',

            'present_students_count.nullable' => 'عدد الطلاب الحاضرين اختياري',
            'present_students_count.integer' => 'عدد الطلاب الحاضرين يجب أن يكون رقماً صحيحاً',
            'present_students_count.min' => 'عدد الطلاب الحاضرين يجب أن يكون 0 أو أكثر',
        ];
    }
}
