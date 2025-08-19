<?php

namespace App\Http\Requests\StudentAttendance;

use App\Http\Requests\BaseRequest;

class UpdateStudentAttendanceRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->hasPermissionTo('تعديل حضور الطلاب');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'student_id' => 'sometimes|exists:students,id',
            'class_session_id' => 'sometimes|exists:class_sessions,id',
            'status' => 'sometimes|in:Excused absence,Unexcused absence,Late',
        ];
    }
}
