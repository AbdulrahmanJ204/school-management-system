<?php

namespace App\Http\Requests\StudentEnrollment;

use App\Http\Requests\BaseRequest;
use App\Models\StudentEnrollment;
use Illuminate\Contracts\Validation\ValidationRule;

class UpdateStudentEnrollmentRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->hasPermissionTo('تحديث تسجيل طالب');
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
            'section_id' => [
                'required',
                'integer',
                'exists:sections,id'
            ],
            'semester_id' => [
                'required',
                'integer',
                'exists:semesters,id'
            ],
            'enrollment_date' => [
                'nullable',
                'date'
            ],
            'status' => [
                'nullable',
                'string',
                'in:active,inactive,graduated,transferred'
            ]
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $enrollmentId = $this->route('student_enrollment') ? $this->route('student_enrollment')->id : null;
            
            // Check if enrollment already exists for this student and semester (excluding current enrollment)
            $existingEnrollment = StudentEnrollment::where('student_id', $this->student_id)
                ->where('semester_id', $this->semester_id)
                ->where('id', '!=', $enrollmentId)
                ->first();

            if ($existingEnrollment) {
                $validator->errors()->add('enrollment', 'الطالب مسجل مسبقاً في هذا الفصل الدراسي');
            }
        });
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

            'section_id.required' => 'الشعبة مطلوبة',
            'section_id.integer' => 'الشعبة يجب أن تكون رقماً صحيحاً',
            'section_id.exists' => 'الشعبة المحددة غير موجودة',

            'semester_id.required' => 'الفصل الدراسي مطلوب',
            'semester_id.integer' => 'الفصل الدراسي يجب أن يكون رقماً صحيحاً',
            'semester_id.exists' => 'الفصل الدراسي المحدد غير موجود',

            'enrollment_date.nullable' => 'تاريخ التسجيل اختياري',
            'enrollment_date.date' => 'تاريخ التسجيل يجب أن يكون تاريخاً صحيحاً',

            'status.nullable' => 'الحالة اختيارية',
            'status.string' => 'الحالة يجب أن تكون نصاً',
            'status.in' => 'الحالة يجب أن تكون واحدة من: active, inactive, graduated, transferred',
        ];
    }
}
