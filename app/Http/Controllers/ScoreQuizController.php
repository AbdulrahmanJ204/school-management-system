<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateScoreQuizRequest;
use App\Services\ScoreQuizService;

class ScoreQuizController extends Controller
{
    protected $scoreQuizService;
    public function __construct(ScoreQuizService $scoreQuizService)
    {
        $this->scoreQuizService = $scoreQuizService;
    }
    public function create(CreateScoreQuizRequest $request)
    {
        return $this->scoreQuizService->create($request);
    }
}
