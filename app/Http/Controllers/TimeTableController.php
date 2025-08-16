<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateTimeTableRequest;
use App\Http\Requests\UpdateTimeTableRequest;
use App\Services\TimeTableService;
use Illuminate\Http\Request;

class TimeTableController extends Controller
{
    protected $timetableService;

    public function __construct(TimeTableService $timetableService)
    {
        $this->timetableService = $timetableService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return $this->timetableService->list();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateTimeTableRequest $request)
    {
        return $this->timetableService->create($request);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        return $this->timetableService->get($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTimeTableRequest $request, int $id)
    {
        return $this->timetableService->update($request, $id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        return $this->timetableService->delete($id);
    }
}
