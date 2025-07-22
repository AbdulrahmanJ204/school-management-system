<?php

namespace App\Http\Requests;

class  LoginRequest extends BaseRequest
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
            'email'       => 'required|email|exists:users,email',
            'password'    => 'required|string',
            'platform'    => 'required|string|in:android,ios,web,windows,macos,linux',      // Android, iOS, Web...
            'device_type' => 'required|string|in:mobile,desktop,tablet',      // Mobile, Desktop...
            'device_name' => 'required|string',      // e.g. iPhone 14
            'device_id'   => 'required|string',      // Unique identifier
        ];
    }
}
