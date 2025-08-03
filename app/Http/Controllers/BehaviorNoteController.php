<?php

namespace App\Http\Controllers;

use App\Http\Requests\BehaviorNoteRequest;
use App\Http\Resources\BehaviorNoteResource;
use App\Services\BehaviorNoteService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BehaviorNoteController extends Controller
{
    protected BehaviorNoteService $behaviorNoteService;

    public function __construct(BehaviorNoteService $behaviorNoteService)
    {
        $this->behaviorNoteService = $behaviorNoteService;
    }

    public function index(): JsonResponse
    {
        return $this->behaviorNoteService->listBehaviorNotes();
    }

    public function trashed(): JsonResponse
    {
        return $this->behaviorNoteService->listTrashedBehaviorNotes();
    }

    public function store(BehaviorNoteRequest $request): JsonResponse
    {
        return $this->behaviorNoteService->createBehaviorNote($request);
    }

    public function show($id): JsonResponse
    {
        return $this->behaviorNoteService->showBehaviorNote($id);
    }

    public function update(BehaviorNoteRequest $request, $id): JsonResponse
    {
        return $this->behaviorNoteService->updateBehaviorNote($request, $id);
    }

    public function destroy($id): JsonResponse
    {
        return $this->behaviorNoteService->deleteBehaviorNote($id);
    }

    public function restore($id): JsonResponse
    {
        return $this->behaviorNoteService->restoreBehaviorNote($id);
    }

    public function forceDelete($id): JsonResponse
    {
        return $this->behaviorNoteService->forceDeleteBehaviorNote($id);
    }

    public function getByStudent($studentId): JsonResponse
    {
        return $this->behaviorNoteService->getByStudent($studentId);
    }

    public function getBySchoolDay($schoolDayId): JsonResponse
    {
        return $this->behaviorNoteService->getBySchoolDay($schoolDayId);
    }
}
