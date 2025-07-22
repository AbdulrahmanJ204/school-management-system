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
            'user_name'   => 'required|string',
            'password'    => 'required|string',
            'platform'    => 'required|string|in:android,ios,web,windows,macos,linux',
            'device_type' => 'required|string|in:mobile,desktop,tablet',
            'device_name' => 'required|string',
            'device_id'   => 'required|string',
        ];
    }
}
