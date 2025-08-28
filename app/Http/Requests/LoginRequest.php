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
            'brand'       => 'nullable|string',
            'device'      => 'nullable|string',
            'manufacturer'=> 'nullable|string',
            'model'       => 'nullable|string',
            'product'     => 'nullable|string',
            'name'        => 'nullable|string',
            'identifier'  => 'nullable|string',
            'os_version'  => 'nullable|string',
            'os_name'     => 'nullable|string',
            'fcm_token'     => 'nullable|string',
        ];
    }
}
