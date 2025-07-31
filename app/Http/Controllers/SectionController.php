<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\SectionRequest;
use App\Http\Resources\SectionResource;
use App\Models\Section;
use App\Services\SectionService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SectionController extends Controller
{
    protected SectionService $sectionService;

    public function __construct(SectionService $sectionService)
    {
        $this->sectionService = $sectionService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return $this->sectionService->listSection();
    }

    public function store(SectionRequest $request)
    {
        return $this->sectionService->createSection($request);
    }

    public function show(Section $section)
    {
        return $this->sectionService->showSection($section);
    }

    public function update(SectionRequest $request, Section $section)
    {
        return $this->sectionService->updateSection($request, $section);
    }

    public function destroy(Section $section)
    {
        return $this->sectionService->destroySection($section);
    }

    public function trashed()
    {
        return $this->sectionService->listTrashedSections();
    }

    public function restore($id)
    {
        return $this->sectionService->restoreSection($id);
    }

    public function forceDelete($id)
    {
        return $this->sectionService->forceDeleteSection($id);
    }
}
