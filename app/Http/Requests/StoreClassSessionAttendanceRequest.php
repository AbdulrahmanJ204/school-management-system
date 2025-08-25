<?php

namespace App\Http\Requests;

class StoreClassSessionAttendanceRequest extends BaseRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'attendances' => [
                'required',
                'array',
                'min:1'
            ],
            'attendances.*.student_id' => [
                'required',
                'integer',
                'exists:students,id'
            ],
            'attendances.*.status' => [
                'required',
                'string',
                'in:present,absent,late'
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'attendances.required' => 'بيانات الحضور مطلوبة',
            'attendances.array' => 'بيانات الحضور يجب أن تكون مصفوفة',
            'attendances.min' => 'يجب إدخال بيانات حضور طالب واحد على الأقل',
            
            'attendances.*.student_id.required' => 'معرف الطالب مطلوب',
            'attendances.*.student_id.integer' => 'معرف الطالب يجب أن يكون رقماً صحيحاً',
            'attendances.*.student_id.exists' => 'الطالب المحدد غير موجود',
            
            'attendances.*.status.required' => 'حالة الحضور مطلوبة',
            'attendances.*.status.string' => 'حالة الحضور يجب أن تكون نصاً',
            'attendances.*.status.in' => 'حالة الحضور يجب أن تكون إحدى القيم: present, absent, late'
        ];
    }
}

