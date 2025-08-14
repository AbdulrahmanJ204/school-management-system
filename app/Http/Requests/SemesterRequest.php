<?php

namespace App\Http\Requests;

use App\Models\Year;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rule;

class SemesterRequest extends BaseRequest
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
        $semesterId = $this->route('semester')?->id;

        return [
            'year_id' => [
                'required',
                'exists:years,id'
            ],
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('semesters')->where(function ($query) {
                    return $query->where('year_id', $this->year_id);
                })->ignore($semesterId)
            ],
            'start_date' => [
                'required',
                'date',
                'before:end_date',
                function ($attribute, $value, $fail) {
                    if ($this->year_id) {
                        $year = Year::findOrFail($this->year_id);
                        if ($year && $year->start_date && $value < $year->start_date->toDateString()) {
                            $fail('تاريخ بداية الفصل يجب أن يكون بعد تاريخ بداية السنة الدراسية');
                        }
                    }
                }
            ],
            'end_date' => [
                'required',
                'date',
                'after:start_date',
                function ($attribute, $value, $fail) {
                    if ($this->year_id) {
                        $year = Year::findOrFail($this->year_id);
                        if ($year && $year->end_date && $value > $year->end_date->toDateString()) {
                            $fail('تاريخ نهاية الفصل يجب أن يكون قبل تاريخ نهاية السنة الدراسية');
                        }
                    }
                }
            ]
        ];
    }

    /**
     * Get custom error messages.
     */
    public function messages(): array
    {
        return [
            'year_id.required' => 'السنة الدراسية مطلوبة',
            'year_id.exists' => 'السنة الدراسية المحددة غير موجودة',
            'name.required' => 'اسم الفصل الدراسي مطلوب',
            'name.unique' => 'اسم الفصل الدراسي موجود مسبقاً في نفس السنة',
            'name.max' => 'اسم الفصل الدراسي يجب أن يكون أقل من 255 حرف',
            'start_date.required' => 'تاريخ بداية الفصل الدراسي مطلوب',
            'start_date.date' => 'تاريخ بداية الفصل الدراسي غير صحيح',
            'start_date.before' => 'تاريخ بداية الفصل يجب أن يكون قبل تاريخ النهاية',
            'end_date.required' => 'تاريخ نهاية الفصل الدراسي مطلوب',
            'end_date.date' => 'تاريخ نهاية الفصل الدراسي غير صحيح',
            'end_date.after' => 'تاريخ نهاية الفصل يجب أن يكون بعد تاريخ البداية'
        ];
    }
}
