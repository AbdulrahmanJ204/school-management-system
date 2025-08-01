<?php

namespace App\Http\Requests\news;

use App\Enums\Permissions\NewsPermission;
use App\Http\Requests\BaseRequest;

class UpdateNewsRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->can(NewsPermission::update->value);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
           'title' => 'sometimes|string|max:255',
            'content' => 'sometimes|string',
            'photo' => 'sometimes|image|max:4096',
            'section_ids' => 'sometimes|array',
            'section_ids.*' => 'exists:sections,id',
            'grade_ids' => 'sometimes|array',
            'grade_ids.*' => 'exists:grades,id',
            'is_global' => 'sometimes|boolean',
        ];
    }
}
