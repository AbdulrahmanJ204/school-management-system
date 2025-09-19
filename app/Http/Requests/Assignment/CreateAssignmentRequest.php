<?php

namespace App\Http\Requests\Assignment;

use App\Http\Requests\BaseRequest;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Auth;

class CreateAssignmentRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::user()->user_type === 'teacher';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'assigned_session_id' => 'required|exists:class_sessions,id',
            'due_session_id' => 'nullable|exists:class_sessions,id',
            'type' => 'required|in:homework,oral,quiz',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg',
            'subject_id' => 'required|exists:subjects,id',
            'section_id' => 'required|exists:sections,id',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'assigned_session_id.required' => 'معرف جلسة التكليف مطلوب',
            'assigned_session_id.exists' => 'جلسة التكليف غير موجودة',
            'due_session_id.exists' => 'جلسة التسليم غير موجودة',
            'type.required' => 'نوع التكليف مطلوب',
            'type.in' => 'نوع التكليف يجب أن يكون: homework, oral, quiz',
            'title.required' => 'عنوان التكليف مطلوب',
            'title.string' => 'عنوان التكليف يجب أن يكون نصاً',
            'title.max' => 'عنوان التكليف يجب أن لا يتجاوز 255 حرف',
            'description.required' => 'وصف التكليف مطلوب',
            'description.string' => 'وصف التكليف يجب أن يكون نصاً',
            'photo.image' => 'الصورة يجب أن تكون صورة',
            'photo.mimes' => 'الصورة يجب أن تكون من نوع jpeg, png, jpg',
            'subject_id.required' => 'معرف المادة مطلوب',
            'subject_id.exists' => 'المادة غير موجودة',
            'section_id.required' => 'معرف الشعبة مطلوب',
            'section_id.exists' => 'الشعبة غير موجودة',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $user = Auth::user();
            $teacher = $user->teacher;
            
            if (!$teacher) {
                $validator->errors()->add('user', 'المستخدم ليس معلماً');
                return;
            }

            // Check if teacher has access to this subject and section
            $hasAccess = $teacher->teacherSectionSubjects()
                ->where('subject_id', $this->subject_id)
                ->where('section_id', $this->section_id)
                ->where('is_active', true)
                ->exists();

            if (!$hasAccess) {
                $validator->errors()->add('access', 'ليس لديك صلاحية للوصول لهذه المادة والشعبة');
            }

            // Check if assigned session belongs to teacher
            $assignedSession = \App\Models\ClassSession::find($this->assigned_session_id);
            if ($assignedSession && $assignedSession->teacher_id !== $teacher->id) {
                $validator->errors()->add('assigned_session_id', 'جلسة التكليف لا تنتمي لك');
            }

            // Check if due session belongs to teacher (if provided)
            if ($this->due_session_id) {
                $dueSession = \App\Models\ClassSession::find($this->due_session_id);
                if ($dueSession && $dueSession->teacher_id !== $teacher->id) {
                    $validator->errors()->add('due_session_id', 'جلسة التسليم لا تنتمي لك');
                }
            }
        });
    }
}

