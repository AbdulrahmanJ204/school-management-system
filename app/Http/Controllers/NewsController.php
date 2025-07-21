<?php

namespace App\Http\Controllers;

use App\Http\Requests\news\CreateNewsRequest;
use App\Http\Requests\news\UpdateNewsRequest;
use App\Models\News;
use App\Services\NewsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    protected  $newsService;
    public function __construct(NewsService $newsService){
    $this->newsService = $newsService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(): ?JsonResponse
    {

        return $this->newsService->getNews();
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateNewsRequest $request)
    {

        return $this->newsService->createNews($request);
    }

    /**
     * Display the specified resource.
     */
    public function show(News $news)
    {
        return $this->newsService->showNews($news);
    }



    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateNewsRequest $request, News $news)
    {
        return $this->newsService->updateNews($request, $news);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(News $news): JsonResponse
    {

        return $this->newsService->deleteNews($news);
    }
}
