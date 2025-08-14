<?php

namespace App\Http\Controllers;

use App\Exceptions\PermissionException;
use App\Http\Requests\MainSubjectRequest;
use App\Http\Requests\CreateMainSubjectWithSubjectRequest;
use App\Models\MainSubject;
use App\Services\MainSubjectService;
use Exception;
use Illuminate\Http\JsonResponse;

class MainSubjectController extends Controller
{
    protected MainSubjectService $mainSubjectService;

    public function __construct(MainSubjectService $mainSubjectService)
    {
        $this->mainSubjectService = $mainSubjectService;
    }

    /**
     * Display a listing of the main subjects.
     * @throws PermissionException
     */
    public function index(): JsonResponse
    {
        return $this->mainSubjectService->listMainSubjects();
    }

    /**
     * Store a newly created main subject in storage.
     * @throws PermissionException
     */
    public function store(MainSubjectRequest $request): JsonResponse
    {
        return $this->mainSubjectService->createMainSubject($request);
    }

    /**
     * Display the specified main subject.
     * @throws PermissionException
     */
    public function show(MainSubject $mainSubject): JsonResponse
    {
        return $this->mainSubjectService->showMainSubject($mainSubject);
    }

    /**
     * Update the specified main subject in storage.
     * @throws PermissionException
     */
    public function update(MainSubjectRequest $request, MainSubject $mainSubject): JsonResponse
    {
        return $this->mainSubjectService->updateMainSubject($request, $mainSubject);
    }

    /**
     * Remove the specified main subject from storage.
     * @throws PermissionException
     */
    public function destroy(MainSubject $mainSubject): JsonResponse
    {
        return $this->mainSubjectService->destroyMainSubject($mainSubject);
    }

    /**
     * Display a listing of trashed main subjects.
     * @throws PermissionException
     */
    public function trashed(): JsonResponse
    {
        return $this->mainSubjectService->listTrashedMainSubjects();
    }

    /**
     * Restore the specified main subject from storage.
     * @throws PermissionException
     */
    public function restore($id): JsonResponse
    {
        return $this->mainSubjectService->restoreMainSubject($id);
    }

    /**
     * Force delete the specified main subject from storage.
     * @throws PermissionException
     */
    public function forceDelete($id): JsonResponse
    {
        return $this->mainSubjectService->forceDeleteMainSubject($id);
    }

    /**
     * Create a main subject with a single subject.
     * @throws Exception
     */
    public function createWithSubject(CreateMainSubjectWithSubjectRequest $request): JsonResponse
    {
        return $this->mainSubjectService->createMainSubjectWithSubject($request);
    }
}
