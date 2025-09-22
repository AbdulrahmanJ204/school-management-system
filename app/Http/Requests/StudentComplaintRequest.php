<?php

namespace App\Http\Requests;

class StudentComplaintRequest extends BaseRequest
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
        $rules = [
            'title' => [
                'required',
                'string',
                'max:255'
            ],
            'content' => [
                'required',
                'string',
                'max:2000'
            ],
        ];

        // For update requests, make fields optional except id
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules['id'] = ['required', 'integer', 'exists:complaints,id'];
            $rules['title'][0] = 'sometimes';
            $rules['content'][0] = 'sometimes';
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'id.required' => 'معرف الشكوى مطلوب',
            'id.integer' => 'معرف الشكوى يجب أن يكون رقماً صحيحاً',
            'id.exists' => 'الشكوى المحددة غير موجودة',

            'title.required' => 'عنوان الشكوى مطلوب',
            'title.string' => 'عنوان الشكوى يجب أن يكون نصاً',
            'title.max' => 'عنوان الشكوى يجب أن لا يتجاوز 255 حرف',

            'content.required' => 'وصف الشكوى مطلوب',
            'content.string' => 'وصف الشكوى يجب أن يكون نصاً',
            'content.max' => 'وصف الشكوى يجب أن لا يتجاوز 2000 حرف',
        ];
    }
}
