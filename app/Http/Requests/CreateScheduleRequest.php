<?php

namespace App\Http\Requests;

use App\Enums\Permissions\TimetablePermission;
use Illuminate\Foundation\Http\FormRequest;

class CreateScheduleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->hasPermissionTo(TimetablePermission::create_schedule->value);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'class_period_id'            => 'required|exists:class_periods,id',
            'teacher_section_subject_id' => 'required|exists:teacher_section_subjects,id',
            'timetable_id'               => 'required|exists:time_tables,id',
            'week_day'                   => 'required|in:' . implode(',', \App\Enums\WeekDay::values()),
        ];
    }
}
