<?php

namespace App\Http\Requests\Assignment;

use App\Http\Requests\BaseRequest;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Auth;

class ListAssignmentRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::user()->hasPermissionTo('عرض الواجبات');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'subject_id' => 'sometimes|exists:subjects,id',
            'section_id' => 'sometimes|exists:sections,id',
            'type' => 'sometimes|in:homework,oral,quiz,project',
            'assigned_session_id' => 'sometimes|exists:class_sessions,id',
            'due_session_id' => 'sometimes|exists:class_sessions,id',
            'date_from' => 'sometimes|date',
            'date_to' => 'sometimes|date|after_or_equal:date_from',
        ];
    }
}
