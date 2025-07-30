<?php

namespace App\Http\Controllers;

use App\Http\Requests\StudentMarkRequest;
use App\Models\StudentMark;
use App\Services\StudentMarkService;
use Illuminate\Http\Request;

class StudentMarkController extends Controller
{
    protected StudentMarkService $studentMarkService;

    public function __construct(StudentMarkService $studentMarkService)
    {
        $this->studentMarkService = $studentMarkService;
    }

    /**
     * Display a listing of the student marks.
     */
    public function index()
    {
        return $this->studentMarkService->listStudentMarks();
    }

    /**
     * Store a newly created student mark in storage.
     */
    public function store(StudentMarkRequest $request)
    {
        return $this->studentMarkService->createStudentMark($request);
    }

    /**
     * Display the specified student mark.
     */
    public function show(StudentMark $studentMark)
    {
        return $this->studentMarkService->showStudentMark($studentMark);
    }

    /**
     * Update the specified student mark in storage.
     */
    public function update(StudentMarkRequest $request, StudentMark $studentMark)
    {
        return $this->studentMarkService->updateStudentMark($request, $studentMark);
    }

    /**
     * Remove the specified student mark from storage.
     */
    public function destroy(StudentMark $studentMark)
    {
        return $this->studentMarkService->destroyStudentMark($studentMark);
    }

    /**
     * Get student marks by student enrollment.
     */
    public function getByEnrollment($enrollmentId)
    {
        return $this->studentMarkService->getMarksByEnrollment($enrollmentId);
    }

    /**
     * Get student marks by subject.
     */
    public function getBySubject($subjectId)
    {
        return $this->studentMarkService->getMarksBySubject($subjectId);
    }
} 