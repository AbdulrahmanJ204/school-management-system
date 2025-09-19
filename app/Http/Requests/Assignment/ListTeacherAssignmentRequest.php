<?php

namespace App\Http\Requests\Assignment;

use App\Http\Requests\BaseRequest;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Auth;

class ListTeacherAssignmentRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::user()->user_type === 'teacher';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'subject_id' => 'nullable|exists:subjects,id',
            'section_id' => 'nullable|exists:sections,id',
            'type' => 'nullable|in:homework,oral,quiz',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'subject_id.exists' => 'المادة غير موجودة',
            'section_id.exists' => 'الشعبة غير موجودة',
            'type.in' => 'نوع التكليف يجب أن يكون: homework, oral, quiz',
            'date_from.date' => 'تاريخ البداية يجب أن يكون تاريخاً صحيحاً',
            'date_to.date' => 'تاريخ النهاية يجب أن يكون تاريخاً صحيحاً',
            'date_to.after_or_equal' => 'تاريخ النهاية يجب أن يكون بعد أو يساوي تاريخ البداية',
        ];
    }
}

