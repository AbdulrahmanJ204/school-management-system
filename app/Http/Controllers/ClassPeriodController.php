<?php

namespace App\Http\Controllers;

use App\Exceptions\PermissionException;
use App\Http\Requests\CreateClassPeriodRequest;
use App\Http\Requests\ListClassPeriodRequest;
use App\Http\Requests\UpdateClassPeriodRequest;
use App\Services\ClassPeriodService;
use Illuminate\Http\JsonResponse;

class ClassPeriodController extends Controller
{
    protected ClassPeriodService $classperiodService;

    public function __construct(ClassPeriodService $classperiodService)
    {
        $this->classperiodService = $classperiodService;
    }

    /**
     * Display a listing of the resource.
     * @throws PermissionException
     */
    public function index(ListClassPeriodRequest $request): JsonResponse
    {
        return $this->classperiodService->list($request);
    }

    /**
     * Store a newly created resource in storage.
     * @throws PermissionException
     */
    public function store(CreateClassPeriodRequest $request): JsonResponse
    {
        return $this->classperiodService->create($request);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): JsonResponse
    {
        return $this->classperiodService->get($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateClassPeriodRequest $request, int $id): JsonResponse
    {
        return $this->classperiodService->update($request, $id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        return $this->classperiodService->delete($id);
    }

    /**
     * Force delete the specified resource from storage.
     */
    public function forceDelete(int $id): JsonResponse
    {
        return $this->classperiodService->forceDelete($id);
    }
}
