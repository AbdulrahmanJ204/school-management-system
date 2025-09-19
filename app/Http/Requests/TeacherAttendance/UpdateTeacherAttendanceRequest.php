<?php

namespace App\Http\Requests\TeacherAttendance;

use App\Http\Requests\BaseRequest;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Auth;

class UpdateTeacherAttendanceRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::user()->hasPermissionTo('تعديل حضور المعلمين');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'teacher_id' => 'sometimes|exists:teachers,id',
            'class_session_id' => 'sometimes|exists:class_sessions,id',
            'status' => 'sometimes|in:present,justified_absent,absent,lateness',
        ];
    }
}
