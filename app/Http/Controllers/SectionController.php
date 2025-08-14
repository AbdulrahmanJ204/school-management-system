<?php

namespace App\Http\Controllers;

use App\Exceptions\PermissionException;
use App\Http\Requests\SectionRequest;
use App\Models\Section;
use App\Services\SectionService;
use Illuminate\Http\JsonResponse;

class SectionController extends Controller
{
    protected SectionService $sectionService;

    public function __construct(SectionService $sectionService)
    {
        $this->sectionService = $sectionService;
    }

    /**
     * Display a listing of the resource.
     * @throws PermissionException
     */
    public function index(): JsonResponse
    {
        return $this->sectionService->listSection();
    }

    /**
     * @throws PermissionException
     */
    public function store(SectionRequest $request): JsonResponse
    {
        return $this->sectionService->createSection($request);
    }

    /**
     * @throws PermissionException
     */
    public function show(Section $section): JsonResponse
    {
        return $this->sectionService->showSection($section);
    }

    /**
     * @throws PermissionException
     */
    public function update(SectionRequest $request, Section $section): JsonResponse
    {
        return $this->sectionService->updateSection($request, $section);
    }

    /**
     * @throws PermissionException
     */
    public function destroy(Section $section): JsonResponse
    {
        return $this->sectionService->destroySection($section);
    }

    /**
     * @throws PermissionException
     */
    public function trashed(): JsonResponse
    {
        return $this->sectionService->listTrashedSections();
    }

    /**
     * @throws PermissionException
     */
    public function restore($id): JsonResponse
    {
        return $this->sectionService->restoreSection($id);
    }

    /**
     * @throws PermissionException
     */
    public function forceDelete($id): JsonResponse
    {
        return $this->sectionService->forceDeleteSection($id);
    }
}
