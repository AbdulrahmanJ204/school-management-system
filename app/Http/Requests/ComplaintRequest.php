<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;

class ComplaintRequest extends BaseRequest
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
            'user_id' => [
                'required',
                'integer',
                'exists:users,id'
            ],
            'title' => [
                'required',
                'string',
                'max:255'
            ],
            'content' => [
                'required',
                'string',
            ],
            'answer' => [
                'nullable',
                'string',
            ]
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'user_id.required' => 'المستخدم مطلوب',
            'user_id.integer' => 'المستخدم يجب أن يكون رقماً صحيحاً',
            'user_id.exists' => 'المستخدم المحدد غير موجود',

            'title.required' => 'العنوان مطلوب',
            'title.string' => 'العنوان يجب أن يكون نصاً',
            'title.max' => 'العنوان يجب أن لا يتجاوز 255 حرف',

            'content.required' => 'محتوى الشكوى مطلوب',
            'content.string' => 'محتوى الشكوى يجب أن يكون نصاً',

            'answer.nullable' => 'الرد اختياري',
            'answer.string' => 'الرد يجب أن يكون نصاً',
        ];
    }
} 