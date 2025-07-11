<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateQuestionRequest;
use App\Http\Requests\UpdateQuestionRequest;
use App\Services\QuestionService;

class QuestionController extends Controller
{
    protected $questionService;

    public function __construct(QuestionService $questionService)
    {
        return $this->questionService = $questionService;
    }

    public function create(CreateQuestionRequest $request, int $quiz_id)
    {
        return $this->questionService->create($request, $quiz_id);
    }

    public function update(UpdateQuestionRequest $request, int $quiz_id, int $question_id)
    {
        return $this->questionService->update($request, $quiz_id, $question_id);
    }
    public function destroy(int $quiz_id, int $question_id)
    {
        return $this->questionService->delete($quiz_id, $question_id);
    }
}
