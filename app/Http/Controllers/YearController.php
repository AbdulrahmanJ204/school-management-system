<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\YearRequest;
use App\Http\Resources\YearResource;
use App\Models\Year;
use App\Services\YearService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class YearController extends Controller
{
    protected YearService $yearService;
    public function __construct(YearService $yearService)
    {
        $this->yearService = $yearService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return $this->yearService->listYear();
    }

    public function store(YearRequest $request)
    {
        return $this->yearService->createYear($request);
    }

    public function show(Year $year)
    {
        return $this->yearService->showYear($year);
    }

    public function update(Request $request, Year $year)
    {
        return $this->yearService->updateYear($request, $year);
    }

    public function destroy(Year $year)
    {
        return $this->yearService->destroyYear($year);
    }

    public function Active(Year $year)
    {
        return $this->yearService->ActiveYear($year);
    }
}
