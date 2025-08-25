<?php

namespace App\Http\Requests;

class TrackClassSessionAttendanceRequest extends BaseRequest
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
            'date' => [
                'sometimes',
                'date',
                'date_format:Y-m-d'
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'date.date' => 'التاريخ يجب أن يكون تاريخاً صحيحاً',
            'date.date_format' => 'التاريخ يجب أن يكون بصيغة Y-m-d'
        ];
    }
}

