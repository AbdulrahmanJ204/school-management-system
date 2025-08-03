<?php

namespace App\Http\Controllers;

use App\Http\Requests\StudyNoteRequest;
use App\Http\Resources\StudyNoteResource;
use App\Services\StudyNoteService;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Study Notes",
 *     description="API Endpoints for Study Note management"
 * )
 */
class StudyNoteController extends Controller
{
    protected $studyNoteService;

    public function __construct(StudyNoteService $studyNoteService)
    {
        $this->studyNoteService = $studyNoteService;
    }

    /**
     * @OA\Get(
     *     path="/study-notes",
     *     tags={"Study Notes"},
     *     summary="Get list of study notes",
     *     @OA\Response(
     *         response=200,
     *         description="Successful response"
     *     )
     * )
     */
    public function index()
    {
        return $this->studyNoteService->listStudyNotes();
    }

    /**
     * @OA\Get(
     *     path="/study-notes/trashed",
     *     tags={"Study Notes"},
     *     summary="Get list of trashed study notes",
     *     @OA\Response(
     *         response=200,
     *         description="Successful response"
     *     )
     * )
     */
    public function trashed()
    {
        return $this->studyNoteService->listTrashedStudyNotes();
    }

    /**
     * @OA\Post(
     *     path="/study-notes",
     *     tags={"Study Notes"},
     *     summary="Create a new study note",
     *     @OA\Response(
     *         response=201,
     *         description="Created successfully"
     *     )
     * )
     */
    public function store(StudyNoteRequest $request)
    {
        return $this->studyNoteService->createStudyNote($request);
    }

    /**
     * @OA\Get(
     *     path="/study-notes/{id}",
     *     tags={"Study Notes"},
     *     summary="Get a specific study note",
     *     @OA\Response(
     *         response=200,
     *         description="Successful response"
     *     )
     * )
     */
    public function show($id)
    {
        return $this->studyNoteService->showStudyNote($id);
    }

    /**
     * @OA\Put(
     *     path="/study-notes/{id}",
     *     tags={"Study Notes"},
     *     summary="Update a study note",
     *     @OA\Response(
     *         response=200,
     *         description="Updated successfully"
     *     )
     * )
     */
    public function update(StudyNoteRequest $request, $id)
    {
        return $this->studyNoteService->updateStudyNote($request, $id);
    }

    /**
     * @OA\Delete(
     *     path="/study-notes/{id}",
     *     tags={"Study Notes"},
     *     summary="Delete a study note",
     *     @OA\Response(
     *         response=200,
     *         description="Deleted successfully"
     *     )
     * )
     */
    public function destroy($id)
    {
        return $this->studyNoteService->deleteStudyNote($id);
    }

    /**
     * @OA\Patch(
     *     path="/study-notes/{id}/restore",
     *     tags={"Study Notes"},
     *     summary="Restore a trashed study note",
     *     @OA\Response(
     *         response=200,
     *         description="Restored successfully"
     *     )
     * )
     */
    public function restore($id)
    {
        return $this->studyNoteService->restoreStudyNote($id);
    }

    /**
     * @OA\Delete(
     *     path="/study-notes/{id}/force-delete",
     *     tags={"Study Notes"},
     *     summary="Force delete a study note",
     *     @OA\Response(
     *         response=200,
     *         description="Force deleted successfully"
     *     )
     * )
     */
    public function forceDelete($id)
    {
        return $this->studyNoteService->forceDeleteStudyNote($id);
    }

    /**
     * @OA\Get(
     *     path="/study-notes/student/{studentId}",
     *     tags={"Study Notes"},
     *     summary="Get study notes by student",
     *     @OA\Response(
     *         response=200,
     *         description="Successful response"
     *     )
     * )
     */
    public function getByStudent($studentId)
    {
        return $this->studyNoteService->getByStudent($studentId);
    }

    /**
     * @OA\Get(
     *     path="/study-notes/school-day/{schoolDayId}",
     *     tags={"Study Notes"},
     *     summary="Get study notes by school day",
     *     @OA\Response(
     *         response=200,
     *         description="Successful response"
     *     )
     * )
     */
    public function getBySchoolDay($schoolDayId)
    {
        return $this->studyNoteService->getBySchoolDay($schoolDayId);
    }

    /**
     * @OA\Get(
     *     path="/study-notes/subject/{subjectId}",
     *     tags={"Study Notes"},
     *     summary="Get study notes by subject",
     *     @OA\Response(
     *         response=200,
     *         description="Successful response"
     *     )
     * )
     */
    public function getBySubject($subjectId)
    {
        return $this->studyNoteService->getBySubject($subjectId);
    }
} 