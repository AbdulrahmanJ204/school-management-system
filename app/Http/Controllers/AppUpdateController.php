<?php

namespace App\Http\Controllers;

use App\Http\Requests\AppUpdate\CheckAppUpdateRequest;
use App\Http\Requests\AppUpdate\StoreAppUpdateRequest;
use App\Http\Requests\AppUpdate\UpdateAppUpdateRequest;
use App\Models\AppUpdate;
use App\Services\AppUpdate\AppUpdateService;
use Illuminate\Http\JsonResponse;

class AppUpdateController extends Controller
{
    protected $appUpdateService;

    public function __construct(AppUpdateService $appUpdateService)
    {
        $this->appUpdateService = $appUpdateService;
    }

    /**
     * Display a listing of app updates (Admin only)
     */
    public function index(): JsonResponse
    {
        return $this->appUpdateService->index(request());
    }

    /**
     * Display a listing of trashed app updates (Admin only)
     */
    public function listTrashed(): JsonResponse
    {
        return $this->appUpdateService->listTrashed(request());
    }

    /**
     * Store a newly created app update (Admin only)
     */
    public function store(StoreAppUpdateRequest $request): JsonResponse
    {
        return $this->appUpdateService->store($request);
    }

    /**
     * Display the specified app update (Admin only)
     */
    public function show(AppUpdate $appUpdate): JsonResponse
    {
        return $this->appUpdateService->show($appUpdate);
    }

    /**
     * Update the specified app update (Admin only)
     */
    public function update(UpdateAppUpdateRequest $request, AppUpdate $appUpdate): JsonResponse
    {
        return $this->appUpdateService->update($request, $appUpdate);
    }

    /**
     * Remove the specified app update (Admin only)
     */
    public function destroy(AppUpdate $appUpdate): JsonResponse
    {
        return $this->appUpdateService->softDelete($appUpdate);
    }

    /**
     * Check for app updates (Teachers and Students)
     */
    public function check(CheckAppUpdateRequest $request): JsonResponse
    {
        return $this->appUpdateService->check($request);
    }

    /**
     * Restore a soft deleted app update (Admin only)
     */
    public function restore(int $id): JsonResponse
    {
        return $this->appUpdateService->restore($id);
    }

    /**
     * Permanently delete an app update (Admin only)
     */
    public function forceDelete(int $id): JsonResponse
    {
        return $this->appUpdateService->forceDelete($id);
    }
}
