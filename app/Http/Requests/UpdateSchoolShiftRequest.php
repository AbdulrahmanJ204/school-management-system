<?php

namespace App\Http\Requests;

use App\Enums\Permissions\TimetablePermission;
use Illuminate\Foundation\Http\FormRequest;

class UpdateSchoolShiftRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->hasPermissionTo(TimetablePermission::update->value);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'nullable|string|unique:school_shifts,name',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after:start_time',
            'is_active' => 'nullable|boolean',
            'targets'                => 'nullable|array',
            'targets.*.grade_id'     => 'nullable|exists:grades,id',
            'targets.*.section_id'   => 'nullable|exists:sections,id',
        ];
    }
}
