<?php

namespace App\Http\Controllers;

use App\Http\Requests\SubjectRequest;
use App\Models\Subject;
use App\Services\SubjectService;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    protected SubjectService $subjectService;

    public function __construct(SubjectService $subjectService)
    {
        $this->subjectService = $subjectService;
    }

    /**
     * Display a listing of the subjects.
     */
    public function index()
    {
        return $this->subjectService->listSubjects();
    }

    /**
     * Store a newly created subject in storage.
     */
    public function store(SubjectRequest $request)
    {
        return $this->subjectService->createSubject($request);
    }

    /**
     * Display the specified subject.
     */
    public function show(Subject $subject)
    {
        return $this->subjectService->showSubject($subject);
    }

    /**
     * Update the specified subject in storage.
     */
    public function update(SubjectRequest $request, Subject $subject)
    {
        return $this->subjectService->updateSubject($request, $subject);
    }

    /**
     * Remove the specified subject from storage.
     */
    public function destroy(Subject $subject)
    {
        return $this->subjectService->destroySubject($subject);
    }
}
