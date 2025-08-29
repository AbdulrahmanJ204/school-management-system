<?php

namespace App\Http\Requests;

use App\Http\Requests\BaseRequest;

class UpdateSessionsAttendanceRequest extends BaseRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'school_day_id' => 'required|exists:school_days,id',
            'section_id' => 'required|exists:sections,id',
            'students' => 'required|array',
            'students.*.id' => 'required|exists:students,id',
            'students.*.class_sessions' => 'required|array',
            'students.*.class_sessions.*.id' => 'required|exists:class_sessions,id',
            'students.*.class_sessions.*.status' => 'required|in:present,justified_absent,absent,lateness',
        ];
    }
}
