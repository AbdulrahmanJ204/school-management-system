<?php

namespace App\Http\Controllers;

use App\Exceptions\PermissionException;
use App\Http\Requests\News\ListNewsRequest;
use App\Http\Requests\News\StoreNewsRequest;
use App\Http\Requests\News\UpdateNewsRequest;
use App\Models\News;
use App\Services\News\NewsService;
use Illuminate\Http\JsonResponse;

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
    public function index(ListNewsRequest $request): ?JsonResponse
    {
        return $this->newsService->list($request);
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
        return $this->newsService->softDelete($news);
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
