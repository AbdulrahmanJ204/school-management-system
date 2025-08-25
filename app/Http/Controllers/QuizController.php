<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateQuizRequest;
use App\Http\Requests\UpdateQuizRequest;
use App\Http\Requests\ListQuizzesRequest;
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
    public function index(ListQuizzesRequest $request)
    {
        return $this->quizService->listQuizzes($request);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateQuizRequest $request)
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
    public function show(int $id)
    {
        return $this->quizService->showQuiz($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateQuizRequest $request, int $id)
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
