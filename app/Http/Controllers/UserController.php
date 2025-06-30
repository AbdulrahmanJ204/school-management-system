<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateRequest;
use App\Services\UserService;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function show($id)
    {
        return $this->userService->getUser($id);
    }

    public function update(UpdateRequest $request, $id)
    {
        return $this->userService->updateUser($request, $id);
    }

    public function destroy($id)
    {
        return $this->userService->deleteUser($id);
    }
}
