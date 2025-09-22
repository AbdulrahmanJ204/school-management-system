<?php

namespace App\Http\Requests\AppUpdate;

use App\Enums\Platform;
use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class CheckAppUpdateRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && in_array(Auth::user()->user_type, ['teacher', 'student']);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'version' => 'required|string|regex:/^\d+\.\d+\.\d+$/',
            'platform' => ['required', 'string', Rule::in(Platform::values())],
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
        ];
    }
}
