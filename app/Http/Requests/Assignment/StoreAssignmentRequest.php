<?php

namespace App\Http\Requests\Assignment;

use App\Http\Requests\BaseRequest;
use Illuminate\Support\Facades\Auth;

class StoreAssignmentRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::user()->hasPermissionTo('انشاء واجب');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'assigned_session_id' => 'required|exists:class_sessions,id',
            'due_session_id' => 'nullable|exists:class_sessions,id',
            'type' => 'required|in:homework,oral,quiz,project',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'photo' => 'sometimes|image|max:4096',
            'subject_id' => 'required|exists:subjects,id',
            'section_id' => 'required|exists:sections,id',
        ];
    }
}
