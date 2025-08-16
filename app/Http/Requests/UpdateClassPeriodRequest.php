<?php

namespace App\Http\Requests;

use App\Enums\Permissions\TimetablePermission;
use Illuminate\Foundation\Http\FormRequest;

class UpdateClassPeriodRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->hasPermissionTo(TimetablePermission::update_class_period->value);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name'             => 'nullable|string|max:255',
            'start_time'       => 'nullable|date_format:H:i',
            'end_time'         => 'nullable|date_format:H:i|after:start_time',
            'school_shift_id'  => 'nullable|exists:school_shifts,id',
            'period_order'     => 'nullable|integer|min:1',
            'type'             => 'nullable|in:' . implode(',', \App\Enums\ClassPeriodType::values()),
        ];
    }
}
