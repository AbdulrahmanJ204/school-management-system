<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\SchoolDayRequest;
use App\Models\SchoolDay;
use App\Services\SchoolDayService;
use Illuminate\Http\Request;

class SchoolDayController extends Controller
{
    protected SchoolDayService $schoolDayService;

    public function __construct(SchoolDayService $schoolDayService)
    {
        $this->schoolDayService = $schoolDayService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return $this->schoolDayService->listSchoolDay();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SchoolDayRequest $request)
    {
        return $this->schoolDayService->createSchoolDay($request);
    }

//        todo after (behaviorNotes, behaviorNotes, assignments, studentAttendances, teacherAttendances, news)

//    public function show(SchoolDay $schoolDay)
//    {
//        return $this->schoolDayService->showSchoolDay($schoolDay);
//    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SchoolDay $schoolDay)
    {
        return $this->schoolDayService->updateSchoolDay($request, $schoolDay);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SchoolDay $schoolDay)
    {
        return $this->schoolDayService->destroySchoolDay($schoolDay);
    }
}
