<?php

namespace App\Http\Controllers;

use App\Exceptions\PermissionException;
use App\Http\Requests\GradeRequest;
use App\Models\Grade;
use App\Services\GradeService;
use Illuminate\Http\JsonResponse;

class GradeController extends Controller
{
    protected GradeService $gradeService;

    public function __construct(GradeService $gradeService)
    {
        $this->gradeService = $gradeService;
    }

    /**
     * Display a listing of the resource.
     * @throws PermissionException
     */
    public function index(): JsonResponse
    {
        return $this->gradeService->listGrade();
    }

    /**
     * @throws PermissionException
     */
    public function store(GradeRequest $request): JsonResponse
    {
        return $this->gradeService->createGrade($request);
    }

    /**
     * @throws PermissionException
     */
    public function show(Grade $grade): JsonResponse
    {
        return $this->gradeService->showGrade($grade);
    }

    /**
     * @throws PermissionException
     */
    public function update(GradeRequest $request, Grade $grade): JsonResponse
    {
        return $this->gradeService->updateGrade($request, $grade);
    }

    /**
     * @throws PermissionException
     */
    public function destroy(Grade $grade): JsonResponse
    {
        return $this->gradeService->destroyGrade($grade);
    }

    /**
     * @throws PermissionException
     */
    public function trashed(): JsonResponse
    {
        return $this->gradeService->listTrashedGrades();
    }

    /**
     * @throws PermissionException
     */
    public function restore($id): JsonResponse
    {
        return $this->gradeService->restoreGrade($id);
    }

    /**
     * @throws PermissionException
     */
    public function forceDelete($id): JsonResponse
    {
        return $this->gradeService->forceDeleteGrade($id);
    }
}
