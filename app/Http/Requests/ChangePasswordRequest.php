<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;

class ChangePasswordRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth('api')->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'current_password' => 'required|string',
            'new_password' => 'required|string|confirmed|min:8',
        ];
    }
}
