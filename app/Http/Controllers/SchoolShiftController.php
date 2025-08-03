<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateSchoolShiftRequest;
use App\Services\SchoolShiftService;
use Illuminate\Http\Request;

class SchoolShiftController extends Controller
{
    protected $schoolshiftService;

    public function __construct(SchoolShiftService $schoolshiftService)
    {
        $this->schoolshiftService = $schoolshiftService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
