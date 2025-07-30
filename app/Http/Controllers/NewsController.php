<?php

namespace App\Http\Controllers;

use App\Exceptions\PermissionException;
use App\Http\Requests\news\StoreNewsRequest;
use App\Http\Requests\news\UpdateNewsRequest;
use App\Models\News;
use App\Services\NewsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    protected $newsService;

    public function __construct(NewsService $newsService)
    {
        $this->newsService = $newsService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): ?JsonResponse
    {
        return $this->newsService->listNews();
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreNewsRequest $request)
    {
        return $this->newsService->store($request);
    }


    /**
     * Display the specified resource.
     */
    public function show($newsId): JsonResponse
    {
        return $this->newsService->show($newsId);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateNewsRequest $request, News $news)
    {
        return $this->newsService->update($request, $news);
    }

    /**
     * Remove the specified resource from storage.
     * @throws PermissionException
     */
    public function destroy(News $news): JsonResponse
    {
        return $this->newsService->destroy($news);
    }

    /**
     * @throws PermissionException
     */
    public function restore($newsId): JsonResponse
    {
        return $this->newsService->restore($newsId);
    }

    /**
     * @throws PermissionException
     */
    public function delete($newsId): JsonResponse
    {
        return $this->newsService->delete($newsId);
    }
}
