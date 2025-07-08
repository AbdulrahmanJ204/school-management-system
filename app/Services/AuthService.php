<?php

namespace App\Services;

use App\Exceptions\DeviceAlreadyExistsException;
use App\Exceptions\ImageUploadFailed;
use App\Exceptions\InvalidPasswordException;
use App\Exceptions\PermissionException;
use App\Helpers\ResponseHelper;
use App\Helpers\RoleHelper;
use App\Models\Admin;
use App\Models\Device_info;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Laravel\Sanctum\PersonalAccessToken;
use Spatie\Permission\Models\Role;


class AuthService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }
    public function register($request)
    {
        $admin = auth()->user();

        if (!$admin->hasPermissionTo('create_user')) {
            throw new PermissionException();
        }

        $credentials = $request->validated();

        if( $request->hasFile('image')) {
            try {
                $credentials['image'] = $request->file('image')->store('user_images', 'public');
            } catch (\Exception $e) {
                throw new ImageUploadFailed();
            }
        }
        else {
            $credentials['image'] = 'user_images/default.png';
        }

        $roleName =  $credentials['role'];
        $guardName = RoleHelper::getGuardForRole($roleName);

        if($request->role !== 'student')
            $credentials['password'] = Hash::make($credentials['password']);

        DB::transaction(function () use ($admin, $credentials, $roleName, $guardName) {

            $user = User::create($credentials);

            $role = Role::where('name', $roleName)->where('guard_name', $guardName)->firstOrFail();
            $user->assignRole($role);

            match ($roleName) {
                'admin' => Admin::create([
                    'user_id' => $user->id,
                    'created_by' => $admin->id,
                ]),
                'teacher' => Teacher::create([
                    'user_id' => $user->id,
                    'created_by' => $admin->id,
                ]),
                'student' => Student::create([
                    'user_id' => $user->id,
                    'created_by' => $admin->id,
                    'grandfather'=> $credentials['grandfather'],
                    'general_id'      => $credentials['general_id'],
                    'is_active' => $credentials['is_active']
                ])
            };
        });

        return ResponseHelper::jsonResponse(
            null,
            __('messages.user.created'),
            201,
            true
        );
    }
    public function login($request)
    {
        $credentials = $request->validated();

        $user = User::where('email', $credentials['email'])->firstOrFail();

        if (!Hash::check($credentials['password'], $user->password)) {
            throw new InvalidPasswordException();
        }

        // Update last login time
        $user->update(['last_login' => now()]);

        DB::beginTransaction();

        try {
            $device = Device_info::where('device_id', $credentials['device_id'])->first();

            // If device exists, check if it belongs to the user already
            if ($device && $user->devices->contains($device->id)) {
                throw new DeviceAlreadyExistsException();
            }

            // If device doesn't exist, create it
            else {
                $device = Device_info::create([
                    'device_id' => $credentials['device_id'],
                    'platform'  => $credentials['platform'],
                    'type'      => $credentials['device_type'],
                    'name'      => $credentials['device_name'],
                ]);
            }

            // Attach device to the user (now we’re sure it’s not duplicated)
            $user->devices()->attach($device->id);

            // 5. Generate tokens
            $accessToken = $user->createToken('access_token', ['access']);
            $refreshToken = $user->createToken('refresh_token', ['refresh']);

            // 6. Set token expiration and device ID
            $accessToken->accessToken->expires_at = now()->addMinutes(60);
            $accessToken->accessToken->device_id = $device->id;
            $accessToken->accessToken->save();

            $refreshToken->accessToken->expires_at = now()->addMinutes(3600);
            $refreshToken->accessToken->device_id = $device->id;
            $refreshToken->accessToken->save();

            DB::commit();

            // 7. Return success response
            return ResponseHelper::jsonResponse([
                'access_token'  => $accessToken->plainTextToken,
                'refresh_token' => $refreshToken->plainTextToken
            ], __('messages.auth.login'));

        } catch (\Throwable $e) {
            DB::rollBack();

            // Re-throw expected exceptions (like DeviceAlreadyExists)
            throw $e;
        }
    }
    public function logout(Request $request)
    {
        $token = $request->user()->currentAccessToken();
        $deviceId = $token->device_id;

        if ($token->expires_at?->isPast()|!$token || !in_array('access', $token->abilities ?? [])) {
            return ResponseHelper::jsonResponse(null, __('messages.auth.invalid_token'), 401, false);
        }

        $token->delete();

        $request->user()->tokens()
            ->where('device_id', $deviceId)
            ->whereJsonContains('abilities', 'refresh')
            ->delete();

        return ResponseHelper::jsonResponse(
            null,
            __('messages.auth.logout'),
            200,
            true
        );
    }
    public function changePassword($request)
    {
        $user = auth()->user();

        if (!$user->hasPermissionTo('change_password')) {
            throw new PermissionException();
        }

        $credentials = $request->validated();

        if (!Hash::check($credentials['current_password'], $user->password)) {
            return ResponseHelper::jsonResponse(
                null,
                __('messages.auth.invalid_password'), // You can define this in your lang file
                401,
                false
            );
        }

        $user->update([
            'password' => Hash::make($credentials['new_password']),
        ]);

        return ResponseHelper::jsonResponse(
            null,
            __('messages.auth.password_changed'),
            200,
            true
        );
    }

    public function forgotPassword($request)
    {
        $request->validated();

        Password::sendResetLink($request->only('email'));

        return ResponseHelper::jsonResponse(null, __('messages.auth.reset_link_sent'), 200, true);
    }

    public function resetPassword($request)
    {
        $request->validated();

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->save();

                $user->tokens()->delete();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? ResponseHelper::jsonResponse(null, __('messages.auth.password_changed'), 200, true)
            : ResponseHelper::jsonResponse(null, __('messages.auth.invalid_token'), 400, false);
    }
}