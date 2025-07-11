<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateAndUpdateQuizRequest;
use App\Services\QuizService;

class QuizController extends Controller
{
    protected $quizService;

    public function __construct(QuizService $quizService)
    {
        $this->quizService = $quizService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateAndUpdateQuizRequest $request)
    {
        return $this->quizService->create($request);
    }

    public function activate(int $id)
    {
        return $this->quizService->activate($id);
    }
    public function deactivate(int $id)
    {
        return $this->quizService->deactivate($id);
    }
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CreateAndUpdateQuizRequest $request, int $id)
    {
        return $this->quizService->update($request, $id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        return $this->quizService->destroy($id);
    }
}
