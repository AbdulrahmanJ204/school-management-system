<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\GradeRequest;
use App\Http\Resources\GradeResource;
use App\Models\Grade;
use App\Services\GradeService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class GradeController extends Controller
{
    protected GradeService $gradeService;

    public function __construct(GradeService $gradeService)
    {
        $this->gradeService = $gradeService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return $this->gradeService->listGrade();
    }

    public function store(GradeRequest $request)
    {
        return $this->gradeService->createGrade($request);
    }

    public function show(Grade $grade)
    {
        return $this->gradeService->showGrade($grade);
    }

    public function update(Request $request, Grade $grade)
    {
        return $this->gradeService->updateGrade($request, $grade);
    }

    public function destroy(Grade $grade)
    {
        return $this->gradeService->destroyGrade($grade);
    }

    public function trashed()
    {
        return $this->gradeService->listTrashedGrades();
    }

    public function restore($id)
    {
        return $this->gradeService->restoreGrade($id);
    }

    public function forceDelete($id)
    {
        return $this->gradeService->forceDeleteGrade($id);
    }
}
