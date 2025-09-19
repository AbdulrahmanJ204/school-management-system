<?php

namespace App\Services;


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
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use App\Models\StudentEnrollment;
use App\Models\Semester;
use App\Models\Year;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\PersonalAccessToken;
use Spatie\Permission\Models\Role;
use Throwable;
use Illuminate\Support\Facades\Auth;

class AuthService
{

    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * @throws PermissionException
     * @throws ImageUploadFailed
     */
    public function register($request): JsonResponse
    {
        $admin = Auth::user();

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
            } catch (Exception) {
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

        DB::transaction(function () use ($admin, $credentials, $userTypeName, &$user) {

            $user = User::create($credentials);

            // Assign role based on user type
            $roleName = match ($userTypeName) {
                'teacher' => 'Teacher',
                'student' => 'Student',
		'admin'=>'Admin',
            };

            $role = Role::where('name', $roleName)->first();
            if ($role) {
                $user->assignRole($role);
            }

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
                    'general_id' => $credentials['general_id'],
                    'is_active' => $credentials['is_active']
                ])
            };

            // Create enrollment for student with last_year_gpa and grade_id
            if ($userTypeName === 'student') {
                // Get the first semester of the current active year
                $activeYear = Year::where('is_active', true)->first();
                if ($activeYear) {
                    $firstSemester = Semester::where('year_id', $activeYear->id)
                        ->orderBy('start_date', 'asc')
                        ->first();

                    if ($firstSemester) {
                        StudentEnrollment::create([
                            'student_id' => $user->student->id,
                            'grade_id' => $credentials['grade_id'],
                            'section_id' => null, // section is null as requested
                            'semester_id' => $firstSemester->id,
                            'year_id' => $activeYear->id,
                            'last_year_gpa' => $credentials['last_year_gpa'],
                            'created_by' => $admin->id,
                        ]);
                    }
                }
            }
        });

        return ResponseHelper::jsonResponse(
            new UserResource($user),
            __('messages.user.created'),
            201,
        );
    }

    /**
     * @throws InvalidUserTypeException
     * @throws Throwable
     * @throws InvalidPasswordException
     * @throws MustPassUserTypeException
     * @throws InvalidUserException
     * @throws UserNotFoundException
     */
    public function login($request, $user_type): JsonResponse
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

        $user = User::with(['roles.permissions', 'devices'])->find($userId);

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
            // Find or create device for this user based on device data
            $deviceData = [
                'brand' => $credentials['brand'] ?? null,
                'device' => $credentials['device'] ?? null,
                'manufacturer' => $credentials['manufacturer'] ?? null,
                'model' => $credentials['model'] ?? null,
                'product' => $credentials['product'] ?? null,
                'name' => $credentials['name'] ?? null,
                'identifier' => $credentials['identifier'] ?? null,
                'os_version' => $credentials['os_version'] ?? null,
                'os_name' => $credentials['os_name'] ?? null,
            ];

            $device = $user->findOrCreateDevice($deviceData);

            $accessToken = $user->createToken('access_token', ['access']);
            $refreshToken = $user->createToken('refresh_token', ['refresh']);

            $accessToken->accessToken->expires_at = now()->addDays(3);
            $accessToken->accessToken->device_id = $device->id;
            $accessToken->accessToken->save();

            $refreshToken->accessToken->expires_at = now()->addDays(30);
            $refreshToken->accessToken->device_id = $device->id;
            $refreshToken->accessToken->save();

            if(isset($credentials['fcm_token'])){
//                dd($credentials['fcm_token']);
                $user->update(['fcm_token' => $credentials['fcm_token']]);
            }

            DB::commit();

            $user->access_token = $accessToken->plainTextToken;
            $user->refresh_token = $refreshToken->plainTextToken;
            return ResponseHelper::jsonResponse([
                'user' => new UserResource($user),
            ], __('messages.auth.login'));

        } catch (Throwable $e) {
            DB::rollBack();

            throw $e;
        }
    }
    public function refresh(Request $request): JsonResponse
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
        );
    }
    public function logout(Request $request): JsonResponse
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
        );
    }

    /**
     * @throws PermissionException
     */
    public function changePassword($request): JsonResponse
    {
        $user = Auth::user();

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
        );
    }

    public function forgotPassword($request): JsonResponse
    {
        $request->validated();

        Password::sendResetLink($request->only('email'));

        return ResponseHelper::jsonResponse(null, __('messages.auth.reset_link_sent'));
    }

    public function resetPassword($request): JsonResponse
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
            ? ResponseHelper::jsonResponse(null, __('messages.auth.password_changed'))
            : ResponseHelper::jsonResponse(null, __('messages.auth.invalid_token'), 400, false);
    }
}
