<?php

namespace App\Http\Requests;

use App\Enums\Permissions\TimetablePermission;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class UpdateSchoolShiftRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::user()->hasPermissionTo(TimetablePermission::update->value);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        $schoolShiftId = $this->route('school_shift');

        return [
            'name' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('school_shifts', 'name')->ignore($schoolShiftId)
            ],
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after:start_time',
            'is_active' => 'nullable|boolean',
            'section_ids' => 'nullable|array',
            'section_ids.*' => 'exists:sections,id',
            'grade_ids' => [
                'nullable',
                'array',
                'missing_with:section_ids',
            ],
            'grade_ids.*' => 'exists:grades,id',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.unique' => 'اسم الشيفت مستخدم بالفعل.',
            'end_time.after' => 'وقت الانتهاء يجب أن يكون بعد وقت البدء.',
            'section_ids.*.exists' => 'احد الأقسام المحددة غير موجود.',
            'grade_ids.*.exists' => 'احد الصفوف المحددة غير موجود.',
        ];
    }
}
