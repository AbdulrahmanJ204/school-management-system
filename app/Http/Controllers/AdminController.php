<?php

namespace App\Http\Controllers;

use App\Exceptions\PermissionException;
use App\Http\Requests\Admin\GetClassPeriodsBySectionRequest;
use App\Services\AdminService;
use Illuminate\Http\JsonResponse;

class AdminController extends Controller
{
    protected AdminService $adminService;
    public function __construct(AdminService $adminService)
    {
        $this->adminService = $adminService;
    }

    /**
     * @throws PermissionException
     */
    public function show(): JsonResponse
    {
        return $this->adminService->listAdmins();
    }

    /**
     * Get study class periods by section
     * 
     * @param GetClassPeriodsBySectionRequest $request
     * @return JsonResponse
     * @throws PermissionException
     */
    public function getClassPeriodsBySection(GetClassPeriodsBySectionRequest $request): JsonResponse
    {
        return $this->adminService->getClassPeriodsBySection($request);
    }
}
