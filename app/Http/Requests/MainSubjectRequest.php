<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rule;

class MainSubjectRequest extends BaseRequest
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
        $mainSubjectId = $this->route('mainSubject') ? $this->route('mainSubject')->id : null;

        return [
            'grade_id' => ['required', 'integer', 'exists:grades,id'],
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('main_subjects', 'name')
                    ->where('grade_id', $this->grade_id)
                    ->ignore($mainSubjectId)
            ],
            'code' => [
                'required',
                'string',
                'max:10',
                Rule::unique('main_subjects', 'code')
                    ->ignore($mainSubjectId)
            ],
            'success_rate' => [
                'required',
                'integer',
                'min:0',
                'max:100'
            ]
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'grade_id.required' => 'حقل الصف مطلوب',
            'grade_id.integer' => 'حقل الصف يجب أن يكون رقماً صحيحاً',
            'grade_id.exists' => 'الصف المحدد غير موجود',

            'name.required' => 'اسم المادة الرئيسية مطلوب',
            'name.string' => 'اسم المادة الرئيسية يجب أن يكون نصاً',
            'name.max' => 'اسم المادة الرئيسية يجب ألا يتجاوز 255 حرفاً',
            'name.unique' => 'اسم المادة الرئيسية موجود مسبقاً لهذا الصف',

            'code.required' => 'رمز المادة الرئيسية مطلوب',
            'code.string' => 'رمز المادة الرئيسية يجب أن يكون نصاً',
            'code.max' => 'رمز المادة الرئيسية يجب ألا يتجاوز 10 أحرف',
            'code.unique' => 'رمز المادة الرئيسية موجود مسبقاً',

            'success_rate.required' => 'معدل النجاح مطلوب',
            'success_rate.integer' => 'معدل النجاح يجب أن يكون رقماً صحيحاً',
            'success_rate.min' => 'معدل النجاح يجب أن يكون على الأقل 0',
            'success_rate.max' => 'معدل النجاح يجب ألا يتجاوز 100',
        ];
    }
}
