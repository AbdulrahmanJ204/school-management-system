<?php

namespace App\Http\Controllers;

use App\Exceptions\PermissionException;
use App\Http\Requests\Admin\GetClassPeriodsBySectionRequest;
use App\Http\Requests\Admin\GetStudentReportRequest;
use App\Services\AdminService;
use App\Services\StudentReportService;
use Illuminate\Http\JsonResponse;

class AdminController extends Controller
{
    protected AdminService $adminService;
    protected StudentReportService $studentReportService;
    
    public function __construct(AdminService $adminService, StudentReportService $studentReportService)
    {
        $this->adminService = $adminService;
        $this->studentReportService = $studentReportService;
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

    /**
     * Generate comprehensive student report for admin
     * 
     * @param GetStudentReportRequest $request
     * @return JsonResponse
     * @throws PermissionException
     */
    public function getStudentReport(GetStudentReportRequest $request): JsonResponse
    {
        return $this->studentReportService->generateStudentReport($request);
    }
}
