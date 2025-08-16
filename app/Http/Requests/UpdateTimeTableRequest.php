<?php

namespace App\Http\Requests;

use App\Enums\Permissions\TimetablePermission;
use Illuminate\Foundation\Http\FormRequest;

class UpdateTimeTableRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->hasPermissionTo(TimetablePermission::update_timetable->value);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'valid_from' => 'nullable|date|before:valid_to',
            'valid_to'   => 'nullable|date|after:valid_from',
            'is_active'  => 'nullable|boolean',
        ];
    }
}
