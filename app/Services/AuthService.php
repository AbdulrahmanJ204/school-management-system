<?php

namespace App\Services;

use App\Exceptions\DeviceAlreadyExistsException;
use App\Exceptions\ImageUploadFailed;
use App\Exceptions\InvalidPasswordException;
use App\Exceptions\InvalidUserTypeException;
use App\Exceptions\InvalidUserException;
use App\Exceptions\MustPassUserTypeException;
use App\Exceptions\PermissionException;
use App\Exceptions\UserNotFoundException;
use App\Helpers\ResponseHelper;
use App\Http\Resources\UserResource;
use App\Models\Admin;
use App\Models\Device_info;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\PersonalAccessToken;

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

        if (!$admin->hasPermissionTo('انشاء مستخدم')) {
            throw new PermissionException();
        }

        $credentials = $request->validated();

        if ($request->hasFile('image'))
        {
            try {
                $image = $request->file('image');
                $imageName = $image->hashName();
                $imagePath = 'user_images/' . $imageName;

                if (!Storage::disk('public')->exists($imagePath))
                {
                    $image->storeAs('user_images', $imageName, 'public');
                }
                $credentials['image'] = $imagePath;
            } catch (\Exception $e) {
                throw new ImageUploadFailed();
            }
        }
        else {
            $credentials['image'] = 'user_images/default.png';
        }

        $userTypeName =  $credentials['user_type'];

        if($request->user_type !== 'student')
            $credentials['password'] = Hash::make($credentials['password']);

        $user = null;

        DB::transaction(function () use ($admin, $credentials, $userTypeName) {

            $user = User::create($credentials);

            match ($userTypeName) {
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
                    'mother' => $credentials['mother'],
                    'general_id' => $credentials['general_id'],
                    'is_active' => $credentials['is_active']
                ])
            };
        });

        return ResponseHelper::jsonResponse(
            new UserResource($user),
            __('messages.user.created'),
            201,
            true
        );
    }
    public function login($request, $user_type)
    {
        $credentials = $request->validated();

        $username = $credentials['user_name'];
        $expectedUserType = strtolower($user_type);

        if (!$expectedUserType) {
            throw new MustPassUserTypeException();
        }

        if (!in_array($expectedUserType, ['admin', 'teacher', 'student'])) {
            throw new InvalidUserTypeException();
        }

        $prefixMap = [
            'admin' => 'Adm_',
            'teacher' => 'Tch_',
            'student' => 'Std_',
        ];

        $expectedPrefix = $prefixMap[$expectedUserType];

        if (!str_starts_with($username, $expectedPrefix)) {
            throw new InvalidUserException(__('messages.auth.invalid_username_prefix'));
        }

        $idPart = str_replace($expectedPrefix, '', $username);

        if (!is_numeric($idPart)) {
            throw new InvalidUserException(__('messages.auth.invalid_user_id'));
        }

        $userId = (int) $idPart;

        $user = User::find($userId);

        if (!$user) {
            throw new UserNotFoundException();
        }

        if ($user->user_type !== $expectedUserType) {
            throw new InvalidUserTypeException();
        }

        if (!Hash::check($credentials['password'], $user->password)) {
            throw new InvalidPasswordException();
        }

        $user->update(['last_login' => now()]);

        DB::beginTransaction();

        try {
            $device = Device_info::where('device_id', $credentials['device_id'])->first();

            if ($device) {
                // Check if the same device info is reused
                $conflict =
                    $device->platform !== $credentials['platform'] ||
                    $device->type     !== $credentials['device_type'] ||
                    $device->name     !== $credentials['device_name'];

                if ($conflict) {
                    throw new DeviceAlreadyExistsException(); // Someone reused the same device_id for another device
                }

                // If device exists and matches — just sync (if not already linked)
                $user->devices()->syncWithoutDetaching([$device->id]);
            } else {
                // Device doesn't exist — create it and attach to user
                $device = Device_info::create([
                    'device_id' => $credentials['device_id'],
                    'platform'  => $credentials['platform'],
                    'type'      => $credentials['device_type'],
                    'name'      => $credentials['device_name'],
                ]);

                $user->devices()->attach($device->id);
            }
            // 5. Generate tokens
            $accessToken = $user->createToken('access_token', ['access']);
            $refreshToken = $user->createToken('refresh_token', ['refresh']);

            // 6. Set token expiration and device ID
            $accessToken->accessToken->expires_at = now()->addMinutes(60);
            $accessToken->accessToken->device_id = $device->id;
            $accessToken->accessToken->save();

            $refreshToken->accessToken->expires_at = now()->addDays(30);
            $refreshToken->accessToken->device_id = $device->id;
            $refreshToken->accessToken->save();

            DB::commit();

            // 7. Return success response
            return ResponseHelper::jsonResponse([
                'user' => new UserResource($user),
                'access_token'  => $accessToken->plainTextToken,
                'refresh_token' => $refreshToken->plainTextToken,
            ], __('messages.auth.login'));

        } catch (\Throwable $e) {
            DB::rollBack();

            throw $e;
        }
    }
    public function refresh(Request $request)
    {
        $refreshToken = $request->bearerToken();

        $token = PersonalAccessToken::findToken($refreshToken);

        if (!$token || !in_array('refresh', $token->abilities ?? [])) {
            return ResponseHelper::jsonResponse(null, __('messages.auth.invalid_token'), 401, false);
        }

        $user = $token->tokenable;
        $deviceId = $token->device_id;

        $token->delete();

        $newAccessToken = $user->createToken('access_token', ['access']);
        $newRefreshToken = $user->createToken('refresh_token', ['refresh']);

        $newAccessToken->accessToken->expires_at = now()->addMinutes(60);
        $newAccessToken->accessToken->device_id = $deviceId;
        $newAccessToken->accessToken->save();

        $newRefreshToken->accessToken->expires_at = now()->addDays(30);
        $newRefreshToken->accessToken->device_id = $deviceId;
        $newRefreshToken->accessToken->save();

        return ResponseHelper::jsonResponse(
            [
                'new_access_token' => $newAccessToken->plainTextToken,
                'new_refresh_token' => $newRefreshToken->plainTextToken
            ],
            __('messages.auth.refresh'),
            200,
            true
        );
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
