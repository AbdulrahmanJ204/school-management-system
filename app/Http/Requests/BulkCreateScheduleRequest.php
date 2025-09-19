<?php

namespace App\Http\Requests;

use App\Enums\Permissions\TimetablePermission;
use App\Enums\WeekDay;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class BulkCreateScheduleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::user()->hasPermissionTo(TimetablePermission::create_schedule->value) ||
               Auth::user()->hasPermissionTo(TimetablePermission::update_schedule->value);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'section_id' => 'required|integer|exists:sections,id',
            'timetable_id' => 'required|integer|exists:time_tables,id',
            'schedules' => 'required|array|min:1',
            'schedules.*.id' => 'nullable|integer|exists:schedules,id',
            'schedules.*.class_period_id' => 'required|integer|exists:class_periods,id',
            'schedules.*.teacher_section_subject_id' => 'required|integer|exists:teacher_section_subjects,id',
            'schedules.*.week_day' => 'required|string|in:' . implode(',', WeekDay::values()),
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $timetableId = $this->input('timetable_id');
            
            if ($timetableId) {
                $timetable = \App\Models\TimeTable::find($timetableId);
                
                if ($timetable && $timetable->is_active) {
                    $validator->errors()->add('timetable_id', 
                        'Cannot modify schedules for an active timetable. Please deactivate the timetable first or use a different timetable.'
                    );
                }
            }
        });
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'section_id.required' => 'Section ID is required.',
            'section_id.exists' => 'The selected section does not exist.',
            'timetable_id.required' => 'Timetable ID is required.',
            'timetable_id.exists' => 'The selected timetable does not exist.',
            'schedules.required' => 'Schedules array is required.',
            'schedules.min' => 'At least one schedule must be provided.',
            'schedules.*.id.exists' => 'The selected schedule does not exist.',
            'schedules.*.class_period_id.required' => 'Class period ID is required for each schedule.',
            'schedules.*.class_period_id.exists' => 'The selected class period does not exist.',
            'schedules.*.teacher_section_subject_id.required' => 'Teacher section subject ID is required for each schedule.',
            'schedules.*.teacher_section_subject_id.exists' => 'The selected teacher section subject does not exist.',
            'schedules.*.week_day.required' => 'Week day is required for each schedule.',
            'schedules.*.week_day.in' => 'The selected week day is invalid.',
        ];
    }
}