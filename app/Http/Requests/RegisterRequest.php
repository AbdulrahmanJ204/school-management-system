<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class RegisterRequest extends BaseRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'first_name' => 'required|string|max:30',
            'last_name' => 'required|string|max:30',
            'father_name' => 'required|string|max:30',
            'email' => 'required_unless:role,student|email|unique:users,email|prohibited_if:role,student',
            'password' => 'required_unless:role,student|string|min:8|confirmed|prohibited_if:role,student',
            'role' => 'required|in:admin,teacher,student',
            'gender' => 'required|in:male,female',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'birth_date'   => 'required|date|before:today',
            'phone'        => 'required|string|unique:users,phone|regex:/^[0-9+\-\s()]*$/|min:7|max:20',
            'grandfather'  => 'required_if:role,student|prohibited_unless:role,student|string|max:255',
            'general_id' => 'required_if:role,student|prohibited_unless:role,student|string|max:50|unique:students,general_id',
            'is_active' => 'required_if:role,student|in:0,1',
        ];
    }
}
