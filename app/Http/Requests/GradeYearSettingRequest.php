<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GradeYearSettingRequest extends BaseRequest
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
        $settingId = $this->route('grade_year_setting') ? $this->route('grade_year_setting')->id : null;

        return [
            'year_id' => [
                'required',
                'integer',
                'exists:years,id'
            ],
            'grade_id' => [
                'required',
                'integer',
                'exists:grades,id'
            ],
            'max_failed_subjects' => [
                'required',
                'integer',
                'min:0',
                'max:20'
            ],
            'help_marks' => [
                'required',
                'integer',
                'min:0',
                'max:100'
            ]
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Check if setting already exists for this year and grade combination
            if (!$this->route('grade_year_setting')) {
                $existingSetting = \App\Models\GradeYearSetting::where('year_id', $this->year_id)
                    ->where('grade_id', $this->grade_id)
                    ->first();

                if ($existingSetting) {
                    $validator->errors()->add('combination', 'يوجد إعداد مسبق لهذا الصف في هذا العام الدراسي');
                }
            }
        });
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'year_id.required' => 'العام الدراسي مطلوب',
            'year_id.integer' => 'العام الدراسي يجب أن يكون رقماً صحيحاً',
            'year_id.exists' => 'العام الدراسي المحدد غير موجود',

            'grade_id.required' => 'الصف مطلوب',
            'grade_id.integer' => 'الصف يجب أن يكون رقماً صحيحاً',
            'grade_id.exists' => 'الصف المحدد غير موجود',

            'max_failed_subjects.required' => 'الحد الأقصى للمواد الراسبة مطلوب',
            'max_failed_subjects.integer' => 'الحد الأقصى للمواد الراسبة يجب أن يكون رقماً صحيحاً',
            'max_failed_subjects.min' => 'الحد الأقصى للمواد الراسبة يجب أن يكون على الأقل 0',
            'max_failed_subjects.max' => 'الحد الأقصى للمواد الراسبة يجب ألا يتجاوز 20',

            'help_marks.required' => 'درجات المساعدة مطلوبة',
            'help_marks.integer' => 'درجات المساعدة يجب أن تكون رقماً صحيحاً',
            'help_marks.min' => 'درجات المساعدة يجب أن تكون على الأقل 0',
            'help_marks.max' => 'درجات المساعدة يجب ألا تتجاوز 100',
        ];
    }
}
