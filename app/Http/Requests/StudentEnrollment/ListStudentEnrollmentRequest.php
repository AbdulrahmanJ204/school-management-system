<?php

namespace App\Http\Requests\StudentEnrollment;

use App\Http\Requests\BaseRequest;
use Illuminate\Contracts\Validation\ValidationRule;

class ListStudentEnrollmentRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->hasPermissionTo('عرض تسجيلات الطلاب');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'student_id' => 'sometimes|integer|exists:students,id',
            'section_id' => 'sometimes|integer|exists:sections,id',
            'semester_id' => 'sometimes|integer|exists:semesters,id',
            'grade_id' => 'sometimes|integer|exists:grades,id',
            'year_id' => 'sometimes|integer|exists:years,id',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'student_id.integer' => 'معرف الطالب يجب أن يكون رقماً صحيحاً',
            'student_id.exists' => 'الطالب المحدد غير موجود',

            'section_id.integer' => 'معرف الشعبة يجب أن يكون رقماً صحيحاً',
            'section_id.exists' => 'الشعبة المحددة غير موجودة',

            'semester_id.integer' => 'معرف الفصل الدراسي يجب أن يكون رقماً صحيحاً',
            'semester_id.exists' => 'الفصل الدراسي المحدد غير موجود',

            'grade_id.integer' => 'معرف الصف يجب أن يكون رقماً صحيحاً',
            'grade_id.exists' => 'الصف المحدد غير موجود',

            'year_id.integer' => 'معرف السنة الدراسية يجب أن يكون رقماً صحيحاً',
            'year_id.exists' => 'السنة الدراسية المحددة غير موجودة',
        ];
    }
}
