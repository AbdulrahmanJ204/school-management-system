<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;

class TeacherClassSessionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && Auth::user()->user_type === 'teacher';
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'subject_id' => 'required|integer|exists:subjects,id',
            'section_id' => 'required|integer|exists:sections,id',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'subject_id.required' => 'معرف المادة مطلوب',
            'subject_id.integer' => 'معرف المادة يجب أن يكون رقماً صحيحاً',
            'subject_id.exists' => 'المادة غير موجودة',
            'section_id.required' => 'معرف الشعبة مطلوب',
            'section_id.integer' => 'معرف الشعبة يجب أن يكون رقماً صحيحاً',
            'section_id.exists' => 'الشعبة غير موجودة',
        ];
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'message' => 'معاملات مطلوبة مفقودة',
                'errors' => $validator->errors(),
                'status_code' => 422
            ], 422)
        );
    }
}
