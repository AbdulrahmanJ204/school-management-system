<?php

namespace App\Http\Controllers;

use App\Exceptions\ImageUploadFailed;
use App\Exceptions\PermissionException;
use App\Exceptions\UserNotFoundException;
use App\Http\Requests\UpdateRequest;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * @throws UserNotFoundException
     */
    public function show($id): JsonResponse
    {
        return $this->userService->getUser($id);
    }

    /**
     * @throws PermissionException
     */
    public function getStaff(): JsonResponse
    {
        return $this->userService->listAdminsAndTeachers();
    }

    /**
     * @throws PermissionException
     * @throws ImageUploadFailed
     * @throws UserNotFoundException
     */
    public function update(UpdateRequest $request, $id): JsonResponse
    {
        return $this->userService->updateUser($request, $id);
    }

    /**
     * @throws PermissionException
     * @throws UserNotFoundException
     */
    public function destroy($id): JsonResponse
    {
        return $this->userService->deleteUser($id);
    }
}
