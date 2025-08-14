<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rule;

class SectionRequest extends BaseRequest
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
        $sectionId = $this->route('section')?->id;

        return [
            'title' => [
                'required',
                'string',
                'max:255',
                Rule::unique('sections', 'title')
                    ->where('grade_id', $this->grade_id)
                    ->ignore($sectionId),
            ],
            'grade_id' => [
                'required',
                'integer',
                'exists:grades,id'
            ]
        ];
    }

    /**
     * Get custom error messages.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'اسم الشعبة مطلوب',
            'title.unique' => 'اسم الشعبة موجود مسبقاً لنفس الصف',
            'title.max' => 'اسم الشعبة يجب أن يكون أقل من 255 حرف',
            'grade_id.required' => 'الصف مطلوب',
            'grade_id.integer' => 'معرف الصف يجب أن يكون رقم صحيح',
            'grade_id.exists' => 'الصف المحدد غير موجود'
        ];
    }
}
