<?php

namespace App\Http\Requests\StudentMark;

use App\Http\Requests\BaseRequest;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Auth;

class ListStudentMarkRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::user()->hasPermissionTo('عرض درجات الطلاب');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'enrollment_id' => 'sometimes|exists:student_enrollments,id',
            'subject_id' => 'sometimes|exists:subjects,id',
            'semester_id' => 'sometimes|exists:semesters,id',
            'section_id' => 'sometimes|exists:sections,id',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'enrollment_id.exists' => 'تسجيل الطالب المحدد غير موجود',
            'subject_id.exists' => 'المادة المحددة غير موجودة',
            'semester_id.exists' => 'الفصل الدراسي المحدد غير موجود',
            'section_id.exists' => 'القسم المحدد غير موجود',
        ];
    }
}
