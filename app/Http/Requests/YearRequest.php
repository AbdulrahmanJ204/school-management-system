<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class YearRequest extends BaseRequest
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
        $yearId = $this->route('year')?->id;

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('years', 'name')->ignore($yearId),
                ],
            'start_date' => [
                'required',
                'date',
                'before:end_date'
            ],
            'end_date' => [
                'required',
                'date',
                'after:start_date'
            ]
        ];
    }

    /**
     * Get custom error messages.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'اسم السنة الدراسية مطلوب',
            'name.unique' => 'اسم السنة الدراسية موجود مسبقاً',
            'name.max' => 'اسم السنة الدراسية يجب أن يكون أقل من 255 حرف',
            'start_date.required' => 'تاريخ بداية السنة الدراسية مطلوب',
            'start_date.date' => 'تاريخ بداية السنة الدراسية غير صحيح',
            'start_date.before' => 'تاريخ بداية السنة يجب أن يكون قبل تاريخ النهاية',
            'end_date.required' => 'تاريخ نهاية السنة الدراسية مطلوب',
            'end_date.date' => 'تاريخ نهاية السنة الدراسية غير صحيح',
            'end_date.after' => 'تاريخ نهاية السنة يجب أن يكون بعد تاريخ البداية',
            'is_active.boolean' => 'حالة تفعيل السنة يجب أن تكون true أو false'
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('is_active')) {
            $this->merge([
                'is_active' => filter_var($this->is_active, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? false
            ]);
        }
    }
}
