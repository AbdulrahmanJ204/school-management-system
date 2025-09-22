<?php

namespace App\Http\Requests\BehaviorNote;

use App\Http\Requests\BaseRequest;
use Illuminate\Contracts\Validation\ValidationRule;

class UpdateBehaviorNoteRequest extends BaseRequest
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
            'school_day_id' => [
                'sometimes',
                'integer',
                'exists:school_days,id'
            ],
            'behavior_type' => [
                'sometimes',
                'string',
                'in:positive,negative'
            ],
            'note' => [
                'sometimes',
                'string',
                'max:1000'
            ]
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'school_day_id.sometimes' => 'اليوم الدراسي اختياري للتحديث',
            'school_day_id.integer' => 'اليوم الدراسي يجب أن يكون رقماً صحيحاً',
            'school_day_id.exists' => 'اليوم الدراسي المحدد غير موجود',

            'behavior_type.sometimes' => 'نوع السلوك اختياري للتحديث',
            'behavior_type.string' => 'نوع السلوك يجب أن يكون نصاً',
            'behavior_type.in' => 'نوع السلوك يجب أن يكون positive أو negative',

            'note.sometimes' => 'الملاحظة اختيارية للتحديث',
            'note.string' => 'الملاحظة يجب أن تكون نصاً',
            'note.max' => 'الملاحظة يجب أن لا تتجاوز 1000 حرف',
        ];
    }
}
