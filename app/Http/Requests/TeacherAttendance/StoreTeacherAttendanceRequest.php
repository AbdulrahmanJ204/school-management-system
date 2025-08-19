<?php

namespace App\Http\Requests\TeacherAttendance;

use App\Http\Requests\BaseRequest;

class StoreTeacherAttendanceRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->hasPermissionTo('إضافة حضور المعلمين');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'teacher_id' => 'required|exists:teachers,id',
            'class_session_id' => 'required|exists:class_sessions,id',
            'status' => 'required|in:Excused absence,Unexcused absence,Late',
        ];
    }
}
