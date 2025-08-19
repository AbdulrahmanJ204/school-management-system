<?php

namespace App\Http\Requests\StudentAttendance;

use App\Http\Requests\BaseRequest;

class StoreStudentAttendanceRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->hasPermissionTo('إضافة حضور الطلاب');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'student_id' => 'required|exists:students,id',
            'class_session_id' => 'required|exists:class_sessions,id',
            'status' => 'required|in:Excused absence,Unexcused absence,Late',
        ];
    }
}
