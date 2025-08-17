<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateClassPeriodRequest;
use App\Http\Requests\UpdateClassPeriodRequest;
use App\Services\ClassPeriodService;

class ClassPeriodController extends Controller
{
    protected $classperiodService;

    public function __construct(ClassPeriodService $classperiodService)
    {
        $this->classperiodService = $classperiodService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return $this->classperiodService->list();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateClassPeriodRequest $request)
    {
        return $this->classperiodService->create($request);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        return $this->classperiodService->get($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateClassPeriodRequest $request, int $id)
    {
        return $this->classperiodService->update($request, $id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        return $this->classperiodService->delete($id);
    }
}
