<?php

namespace App\Http\Requests\StudentEnrollment;

use App\Http\Requests\BaseRequest;
use App\Models\StudentEnrollment;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Auth;

class UpdateStudentEnrollmentRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::user()->hasPermissionTo('تحديث تسجيل طالب');
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
                'nullable',
                'integer',
                'exists:sections,id'
            ],
            'grade_id' => [
                'required',
                'integer',
                'exists:grades,id'
            ],
            'semester_id' => [
                'required',
                'integer',
                'exists:semesters,id'
            ],
            'last_year_gpa' => [
                'nullable',
                'numeric',
                'min:0',
                'max:100',
                'regex:/^\d+(\.\d{1,2})?$/'
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

            'section_id.nullable' => 'الشعبة اختيارية',
            'section_id.integer' => 'الشعبة يجب أن تكون رقماً صحيحاً',
            'section_id.exists' => 'الشعبة المحددة غير موجودة',

            'grade_id.required' => 'الصف مطلوب',
            'grade_id.integer' => 'الصف يجب أن يكون رقماً صحيحاً',
            'grade_id.exists' => 'الصف المحدد غير موجود',

            'semester_id.required' => 'الفصل الدراسي مطلوب',
            'semester_id.integer' => 'الفصل الدراسي يجب أن يكون رقماً صحيحاً',
            'semester_id.exists' => 'الفصل الدراسي المحدد غير موجود',

            'last_year_gpa.nullable' => 'المعدل التراكمي للسنة السابقة اختياري',
            'last_year_gpa.numeric' => 'المعدل التراكمي يجب أن يكون رقماً',
            'last_year_gpa.min' => 'المعدل التراكمي يجب أن يكون أكبر من أو يساوي 0',
            'last_year_gpa.max' => 'المعدل التراكمي يجب أن يكون أقل من أو يساوي 100',
            'last_year_gpa.regex' => 'المعدل التراكمي يجب أن يكون بتنسيق صحيح (مثال: 3.5)',

            'enrollment_date.nullable' => 'تاريخ التسجيل اختياري',
            'enrollment_date.date' => 'تاريخ التسجيل يجب أن يكون تاريخاً صحيحاً',

            'status.nullable' => 'الحالة اختيارية',
            'status.string' => 'الحالة يجب أن تكون نصاً',
            'status.in' => 'الحالة يجب أن تكون واحدة من: active, inactive, graduated, transferred',
        ];
    }
}
