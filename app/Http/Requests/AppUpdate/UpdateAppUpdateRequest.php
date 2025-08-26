<?php

namespace App\Http\Requests\AppUpdate;

use App\Enums\Platform;
use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class UpdateAppUpdateRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->user_type === 'admin';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'version' => 'sometimes|string|regex:/^\d+\.\d+\.\d+$/',
            'platform' => ['sometimes', 'string', Rule::in(Platform::values())],
            'url' => 'sometimes|url|max:500',
            'change_log' => 'nullable|string|max:1000',
            'is_force_update' => 'sometimes|string|in:true,false',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'version.regex' => 'The version must be in semantic versioning format (e.g., 1.2.3).',
            'platform.in' => 'The platform must be one of: ' . implode(', ', Platform::values()),
            'url.url' => 'The URL must be a valid URL.',
            'change_log.max' => 'The change log may not be greater than 1000 characters.',
        ];
    }
}
