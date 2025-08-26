<?php

namespace App\Http\Requests;

class GetAttendanceHistoryRequest extends BaseRequest
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
            'subject_id' => [
                'sometimes',
                'integer',
                'exists:subjects,id'
            ],
            'section_id' => [
                'sometimes',
                'integer',
                'exists:sections,id'
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'subject_id.integer' => 'معرف المادة يجب أن يكون رقماً صحيحاً',
            'subject_id.exists' => 'المادة المحددة غير موجودة',
            
            'section_id.integer' => 'معرف الشعبة يجب أن يكون رقماً صحيحاً',
            'section_id.exists' => 'الشعبة المحددة غير موجودة'
        ];
    }
}

