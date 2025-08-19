<?php

namespace App\Http\Requests\TeacherAttendance;

use App\Http\Requests\BaseRequest;

class ListTeacherAttendanceRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->hasPermissionTo('عرض حضور المعلمين');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'teacher_id' => 'sometimes|exists:teachers,id',
            'class_session_id' => 'sometimes|exists:class_sessions,id',
            'status' => 'sometimes|in:Excused absence,Unexcused absence,Late',
            'date_from' => 'sometimes|date',
            'date_to' => 'sometimes|date|after_or_equal:date_from',
        ];
    }
}
