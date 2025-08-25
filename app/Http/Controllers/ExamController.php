<?php

namespace App\Http\Controllers;

use App\Exceptions\PermissionException;
use App\Http\Requests\ExamFilterRequest;
use App\Http\Requests\ExamRequest;
use App\Services\ExamService;
use Illuminate\Http\JsonResponse;

class ExamController extends Controller
{
    protected ExamService $examService;

    public function __construct(ExamService $examService)
    {
        $this->examService = $examService;
    }

    /**
     * @throws PermissionException
     */
    public function index(ExamFilterRequest $request): JsonResponse
    {
        return $this->examService->listExams($request);
    }

    /**
     * @throws PermissionException
     */
    public function trashed(): JsonResponse
    {
        return $this->examService->listTrashedExams();
    }

    /**
     * @throws PermissionException
     */
    public function store(ExamRequest $request): JsonResponse
    {
        return $this->examService->createExam($request);
    }

    /**
     * @throws PermissionException
     */
    public function show($id): JsonResponse
    {
        return $this->examService->showExam($id);
    }

    /**
     * @throws PermissionException
     */
    public function update(ExamRequest $request, $id): JsonResponse
    {
        return $this->examService->updateExam($request, $id);
    }

    /**
     * @throws PermissionException
     */
    public function destroy($id): JsonResponse
    {
        return $this->examService->deleteExam($id);
    }

    /**
     * @throws PermissionException
     */
    public function restore($id): JsonResponse
    {
        return $this->examService->restoreExam($id);
    }

    /**
     * @throws PermissionException
     */
    public function forceDelete($id): JsonResponse
    {
        return $this->examService->forceDeleteExam($id);
    }


}
