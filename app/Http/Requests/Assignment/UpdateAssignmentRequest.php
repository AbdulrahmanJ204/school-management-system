<?php

namespace App\Http\Requests\Assignment;

use App\Http\Requests\BaseRequest;
use Illuminate\Contracts\Validation\ValidationRule;

class UpdateAssignmentRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->user_type === 'teacher';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'assigned_session_id' => 'sometimes|exists:class_sessions,id',
            'due_session_id' => 'nullable|exists:class_sessions,id',
            'type' => 'sometimes|in:homework,oral,quiz',
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg',
            'subject_id' => 'sometimes|exists:subjects,id',
            'section_id' => 'sometimes|exists:sections,id',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'assigned_session_id.exists' => 'جلسة التكليف غير موجودة',
            'due_session_id.exists' => 'جلسة التسليم غير موجودة',
            'type.in' => 'نوع التكليف يجب أن يكون: homework, oral, quiz',
            'title.string' => 'عنوان التكليف يجب أن يكون نصاً',
            'title.max' => 'عنوان التكليف يجب أن لا يتجاوز 255 حرف',
            'description.string' => 'وصف التكليف يجب أن يكون نصاً',
            'photo.image' => 'الصورة يجب أن تكون صورة',
            'photo.mimes' => 'الصورة يجب أن تكون من نوع jpeg, png, jpg',
            'subject_id.exists' => 'المادة غير موجودة',
            'section_id.exists' => 'الشعبة غير موجودة',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $user = auth()->user();
            $teacher = $user->teacher;
            
            if (!$teacher) {
                $validator->errors()->add('user', 'المستخدم ليس معلماً');
                return;
            }

            // Check if teacher has access to this subject and section (if provided)
            if ($this->has('subject_id') && $this->has('section_id')) {
                $hasAccess = $teacher->teacherSectionSubjects()
                    ->where('subject_id', $this->subject_id)
                    ->where('section_id', $this->section_id)
                    ->where('is_active', true)
                    ->exists();

                if (!$hasAccess) {
                    $validator->errors()->add('access', 'ليس لديك صلاحية للوصول لهذه المادة والشعبة');
                }
            }

            // Check if assigned session belongs to teacher (if provided)
            if ($this->has('assigned_session_id')) {
                $assignedSession = \App\Models\ClassSession::find($this->assigned_session_id);
                if ($assignedSession && $assignedSession->teacher_id !== $teacher->id) {
                    $validator->errors()->add('assigned_session_id', 'جلسة التكليف لا تنتمي لك');
                }
            }

            // Check if due session belongs to teacher (if provided)
            if ($this->has('due_session_id')) {
                $dueSession = \App\Models\ClassSession::find($this->due_session_id);
                if ($dueSession && $dueSession->teacher_id !== $teacher->id) {
                    $validator->errors()->add('due_session_id', 'جلسة التسليم لا تنتمي لك');
                }
            }
        });
    }
}
