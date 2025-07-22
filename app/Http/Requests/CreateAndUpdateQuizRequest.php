<?php

namespace App\Http\Requests;

class CreateAndUpdateQuizRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth('api')->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string',
            'is_active' => 'prohibited',
            'taken_at' => 'prohibited',
            'targets'                 => 'required|array|min:1',
            'targets.*.subject_id'    => 'required|exists:subjects,id',
            'targets.*.section_id'    => 'required|exists:sections,id',
            'targets.*.semester_id'   => 'required|exists:semesters,id',
        ];
    }
}
