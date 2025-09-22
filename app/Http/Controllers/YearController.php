<?php

namespace App\Http\Controllers;

use App\Exceptions\PermissionException;
use App\Http\Requests\YearRequest;
use App\Models\Year;
use App\Services\YearService;
use Illuminate\Http\JsonResponse;

class YearController extends Controller
{
    protected YearService $yearService;

    public function __construct(YearService $yearService)
    {
        $this->yearService = $yearService;
    }

    /**
     * Display a listing of the resource.
     * @throws PermissionException
     */
    public function index(): JsonResponse
    {
        return $this->yearService->listYear();
    }

    /**
     * @throws PermissionException
     */
    public function trashed(): JsonResponse
    {
        return $this->yearService->listTrashedYears();
    }

    /**
     * @throws PermissionException
     */
    public function store(YearRequest $request): JsonResponse
    {
        return $this->yearService->createYear($request);
    }

    /**
     * @throws PermissionException
     */
    public function show(Year $year): JsonResponse
    {
        return $this->yearService->showYear($year);
    }

    /**
     * @throws PermissionException
     */
    public function update(YearRequest $request, Year $year): JsonResponse
    {
        return $this->yearService->updateYear($request, $year);
    }

    /**
     * @throws PermissionException
     */
    public function destroy(Year $year): JsonResponse
    {
        return $this->yearService->destroyYear($year);
    }

    /**
     * @throws PermissionException
     */
    public function forceDelete($id): JsonResponse
    {
        return $this->yearService->forceDeleteYear($id);
    }

    /**
     * @throws PermissionException
     */
    public function restore($id): JsonResponse
    {
        return $this->yearService->restoreYear($id);
    }

    /**
     * @throws PermissionException
     */
    public function Active(Year $year): JsonResponse
    {
        return $this->yearService->ActiveYear($year);
    }

    /**
     * Display years with nested relationships (grades, sections, main subjects, subjects)
     * @throws PermissionException
     */
    public function withNestedData(): JsonResponse
    {
        return $this->yearService->getYearsWithNestedData();
    }
}
