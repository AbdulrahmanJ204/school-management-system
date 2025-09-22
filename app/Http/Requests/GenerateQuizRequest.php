<?php

namespace App\Http\Requests;

class GenerateQuizRequest extends BaseRequest
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
            'is_active' => 'sometimes',
            'taken_at' => 'prohibited',
            'name' => 'required|string|max:255|unique:quizzes,name',
            'full_score'  => 'required|integer|min:1',
            'quiz_photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'grade_id'    => 'required|exists:grades,id',
            'subject_id'  => 'required|exists:subjects,id',
            'semester_id' => 'sometimes|exists:semesters,id',
            'section_ids' => 'nullable|array',
            'section_ids.*' => 'exists:sections,id',
            'text_to_extract_from' => 'required|string|min:10',
            'multiple_choice_count' => 'required|integer|min:1|max:50',
            'true_false_count' => 'required|integer|min:1|max:50',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'text_to_extract_from.required' => 'النص المطلوب استخراج الأسئلة منه مطلوب',
            'text_to_extract_from.min' => 'النص يجب أن يكون على الأقل 10 أحرف',
            'multiple_choice_count.required' => 'عدد أسئلة الاختيار المتعدد مطلوب',
            'multiple_choice_count.min' => 'عدد أسئلة الاختيار المتعدد يجب أن يكون على الأقل 1',
            'multiple_choice_count.max' => 'عدد أسئلة الاختيار المتعدد يجب أن لا يتجاوز 50',
            'true_false_count.required' => 'عدد أسئلة صح وخطأ مطلوب',
            'true_false_count.min' => 'عدد أسئلة صح وخطأ يجب أن يكون على الأقل 1',
            'true_false_count.max' => 'عدد أسئلة صح وخطأ يجب أن لا يتجاوز 50',
        ];
    }
}
