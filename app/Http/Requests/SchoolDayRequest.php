<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class SchoolDayRequest extends BaseRequest
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
        $schoolDayId = $this->route('school_day')?->id;

        return [
            'date' => [
                'required',
                'date',
                Rule::unique('school_days', 'date')
                    ->where('semester_id', $this->semester_id)
                    ->ignore($schoolDayId),
            ],
            'semester_id' => [
                'required',
                'exists:semesters,id'
            ],
            'type' => [
                'required',
                'in:study,exam,holiday'
            ]
        ];
    }

    /**
     * Get custom error messages.
     */
    public function messages(): array
    {
        return [
            'date.required' => 'تاريخ اليوم الدراسي مطلوب',
            'date.date' => 'تاريخ اليوم الدراسي غير صحيح',
            'date.unique' => 'هذا التاريخ موجود مسبقاً في هذا الفصل الدراسي',
            'semester_id.required' => 'الفصل الدراسي مطلوب',
            'semester_id.exists' => 'الفصل الدراسي المحدد غير موجود',
            'type.required' => 'نوع اليوم الدراسي مطلوب',
            'type.in' => 'نوع اليوم الدراسي يجب أن يكون إما دراسة أو امتحان'
        ];
    }
}
