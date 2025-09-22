<?php

namespace App\Http\Requests\TeacherAttendance;

use App\Http\Requests\BaseRequest;
use Illuminate\Foundation\Http\FormRequest;

class TeacherAttendanceReportRequest extends BaseRequest
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
            // No parameters required - teacher_id comes from token, year is current year
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            // No validation messages needed
        ];
    }
}

