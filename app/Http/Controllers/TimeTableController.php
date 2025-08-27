<?php

namespace App\Http\Controllers;

use App\Exceptions\PermissionException;
use App\Http\Requests\CreateTimeTableRequest;
use App\Http\Requests\UpdateTimeTableRequest;
use App\Services\TimeTableService;
use Illuminate\Http\JsonResponse;

class TimeTableController extends Controller
{
    protected TimeTableService $timetableService;

    public function __construct(TimeTableService $timetableService)
    {
        $this->timetableService = $timetableService;
    }

    /**
     * Display a listing of the resource.
     * @throws PermissionException
     */
    public function index(): JsonResponse
    {
        return $this->timetableService->list();
    }

    /**
     * Store a newly created resource in storage.
     * @throws PermissionException
     */
    public function store(CreateTimeTableRequest $request): JsonResponse
    {
        return $this->timetableService->create($request);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): JsonResponse
    {
        return $this->timetableService->get($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTimeTableRequest $request, int $id): JsonResponse
    {
        return $this->timetableService->update($request, $id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        return $this->timetableService->delete($id);
    }
}
