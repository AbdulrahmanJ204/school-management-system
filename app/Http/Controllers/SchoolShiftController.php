<?php

namespace App\Http\Controllers;

use App\Exceptions\PermissionException;
use App\Exceptions\SchoolShiftNotFoundException;
use App\Http\Requests\CreateSchoolShiftRequest;
use App\Http\Requests\UpdateSchoolShiftRequest;
use App\Services\SchoolShiftService;
use Illuminate\Http\JsonResponse;

class SchoolShiftController extends Controller
{
    protected SchoolShiftService $schoolshiftService;

    public function __construct(SchoolShiftService $schoolShiftService)
    {
        $this->schoolshiftService = $schoolShiftService;
    }

    /**
     * Display a listing of the resource.
     * @throws PermissionException
     */
    public function index(): JsonResponse
    {
        return $this->schoolshiftService->list();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateSchoolShiftRequest $request): JsonResponse
    {
        return $this->schoolshiftService->create($request);
    }

    /**
     * Display the specified resource.
     * @throws SchoolShiftNotFoundException|PermissionException
     */
    public function show(string $id): JsonResponse
    {
        return $this->schoolshiftService->get($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSchoolShiftRequest $request, $id): JsonResponse
    {
        return $this->schoolshiftService->update($request, $id);
    }

    /**
     * Remove the specified resource from storage.
     * @throws SchoolShiftNotFoundException|PermissionException
     */
    public function destroy(int $id): ?JsonResponse
    {
        return $this->schoolshiftService->delete($id);
    }
}
