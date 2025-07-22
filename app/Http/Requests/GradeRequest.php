<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class GradeRequest extends BaseRequest
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
        $gradeId = $this->route('grade')?->id;

        return [
            'title' => [
                'required',
                'string',
                'max:255',
                Rule::unique('grades', 'title')->ignore($gradeId),
            ],
        ];
    }

    /**
     * Get custom error messages.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'عنوان الصف مطلوب',
            'title.unique' => 'عنوان الصف موجود مسبقاً',
            'title.max' => 'عنوان الصف يجب أن يكون أقل من 255 حرف',
        ];
    }
}
