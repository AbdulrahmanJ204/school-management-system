<?php

namespace App\Http\Controllers;

use App\Exceptions\ImageUploadFailed;
use App\Exceptions\InvalidPasswordException;
use App\Exceptions\InvalidUserException;
use App\Exceptions\InvalidUserTypeException;
use App\Exceptions\MustPassUserTypeException;
use App\Exceptions\PermissionException;
use App\Exceptions\UserNotFoundException;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * @throws PermissionException
     * @throws ImageUploadFailed
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        return $this->authService->register($request);
    }

    /**
     * @throws InvalidUserTypeException
     * @throws \Throwable
     * @throws MustPassUserTypeException
     * @throws InvalidPasswordException
     * @throws UserNotFoundException
     * @throws InvalidUserException
     */
    public function login(LoginRequest $request): JsonResponse
    {
        return $this->authService->login($request);
    }
    public function refresh(Request $request): JsonResponse
    {
        return $this->authService->refresh($request);
    }
    public function logout(Request $request): JsonResponse
    {
        return $this->authService->logout($request);
    }

    /**
     * @throws PermissionException
     */
    public function changePassword(ChangePasswordRequest $request): JsonResponse
    {
        return $this->authService->changePassword($request);
    }
    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        return $this->authService->forgotPassword($request);
    }
    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        return $this->authService->resetPassword($request);
    }
}
