<?php

namespace App\Http\Requests\news;

use App\Enums\NewsPermission;
use App\Http\Requests\BaseRequest;
use Illuminate\Foundation\Http\FormRequest;

class StoreNewsRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->hasPermissionTo(NewsPermission::create->value);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'photo' => 'nullable|image|max:4096',
            'section_ids' => 'nullable|array',
            'section_ids.*' => 'exists:sections,id',
            'grade_ids' => 'nullable|array',
            'grade_ids.*' => 'exists:grades,id',
        ];
    }
}
