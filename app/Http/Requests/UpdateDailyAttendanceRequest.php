<?php

namespace App\Http\Requests;

use App\Http\Requests\BaseRequest;

class UpdateDailyAttendanceRequest extends BaseRequest
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
            'students.*.status' => 'required|in:present,justified_absent,absent,lateness',
        ];
    }
}
