<?php

namespace App\Http\Requests\Assignment;

use App\Http\Requests\BaseRequest;

class UpdateAssignmentRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->hasPermissionTo('تعديل واجب');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'assigned_session_id' => 'sometimes|exists:class_sessions,id',
            'due_session_id' => 'sometimes|exists:class_sessions,id',
            'type' => 'sometimes|in:homework,oral,quiz,project',
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'photo' => 'sometimes|image|max:4096',
            'subject_id' => 'sometimes|exists:subjects,id',
            'section_id' => 'sometimes|exists:sections,id',
        ];
    }
}
