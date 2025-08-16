<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateSchoolShiftRequest;
use App\Http\Requests\UpdateSchoolShiftRequest;
use App\Services\SchoolShiftService;

class SchoolShiftController extends Controller
{
    protected $schoolshiftService;

    public function __construct(SchoolShiftService $schoolShiftService)
    {
        $this->schoolshiftService = $schoolShiftService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return $this->schoolshiftService->list();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateSchoolShiftRequest $request)
    {
        return $this->schoolshiftService->create($request);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return $this->schoolshiftService->get($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSchoolShiftRequest $request, $id)
    {
        return $this->schoolshiftService->update($request, $id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        return $this->schoolshiftService->delete($id);
    }
}
