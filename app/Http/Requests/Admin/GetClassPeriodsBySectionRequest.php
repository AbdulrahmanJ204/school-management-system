<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\BaseRequest;
use Illuminate\Contracts\Validation\ValidationRule;

class GetClassPeriodsBySectionRequest extends BaseRequest
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
            'section_id' => [
                'required',
                'integer',
                'exists:sections,id'
            ]
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'section_id.required' => 'معرف الشعبة مطلوب',
            'section_id.integer' => 'معرف الشعبة يجب أن يكون رقماً صحيحاً',
            'section_id.exists' => 'الشعبة غير موجودة',
        ];
    }
}
