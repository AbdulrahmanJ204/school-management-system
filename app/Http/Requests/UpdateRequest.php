<?php

namespace App\Http\Requests;

use App\Exceptions\UserNotFoundException;
use App\Models\User;
use Illuminate\Validation\Rule;

class UpdateRequest extends BaseRequest
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
        $userId = $this->route('user');
        $user = User::select(['id', 'email', 'phone', 'role'])->find($userId);

        if (!$user) {
            throw new UserNotFoundException();
        }

        return [
            'first_name' => 'nullable|string|max:30', // Changed from sometimes to nullable
            'last_name' => 'nullable|string|max:30',
            'father_name' => 'nullable|string|max:30',
            'email' => [
                'nullable', // Changed from sometimes
                'email',
                Rule::unique('users')->ignore($user->id)->whereNull('deleted_at')
                    ->when(
                        $this->filled('email') && $this->input('email') !== $user->email,
                        fn ($rule) => $rule,
                        fn ($rule) => $rule->where('id', '!=', $this->route('user'))
                    ),
            ],
            'password' => 'prohibited',
            'role' => 'prohibited',
            'gender' => 'nullable|in:male,female',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'birth_date' => 'nullable|date|before:today|date_format:Y-m-d|regex:/^\d{4}-\d{2}-\d{2}$/',
            'phone' => [
                'nullable',
                'string',
                'regex:/^[0-9+\-\s()]*$/',
                'min:7',
                'max:20',
                Rule::unique('users')->ignore($user->id)->whereNull('deleted_at')
                    ->when(
                        $this->filled('phone') && $this->input('phone') !== $user->phone,
                        fn ($rule) => $rule,
                        fn ($rule) => $rule->where('id', '!=', $this->route('user'))
                    ),
            ],
            'grandfather' => [
                'nullable',
                'required_if:role,student',
                'prohibited_unless:role,student',
                'string',
                'max:255'
            ],
            'general_id' => [
                'nullable',
                'required_if:role,student',
                'prohibited_unless:role,student',
                'string',
                'max:50',
                Rule::unique('students', 'general_id')->ignore($user->id, 'user_id')
            ],
            'is_active' => 'nullable|required_if:role,student|boolean',
        ];
    }
}
