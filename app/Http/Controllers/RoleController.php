<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Services\RoleService;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    protected $roleService;

    public function __construct(RoleService $roleService)
    {
        $this->roleService = $roleService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return $this->roleService->list();
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateRoleRequest $request)
    {
        return $this->roleService->create($request);
    }
    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        return $this->roleService->getRole($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRoleRequest $request, int $id)
    {
        return $this->roleService->update($request, $id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        return $this->roleService->delete($id);
    }
}
