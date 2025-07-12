<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Services\AuthService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }
    public function register(RegisterRequest $request)
    {
        return $this->authService->register($request);
    }
    public function login(LoginRequest $request)
    {
        $role = strtolower(request()->query('role'));

        return $this->authService->login($request, $role);
    }
    public function refresh(Request $request)
    {
        return $this->authService->refresh($request);
    }
    public function logout(Request $request)
    {
        return $this->authService->logout($request);
    }
    public function changePassword(ChangePasswordRequest $request)
    {
        return $this->authService->changePassword($request);
    }
    public function forgotPassword(ForgotPasswordRequest $request)
    {
        return $this->authService->forgotPassword($request);
    }
    public function resetPassword(ResetPasswordRequest $request)
    {
        return $this->authService->resetPassword($request);
    }
}
