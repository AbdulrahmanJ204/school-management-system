<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateNewsRequest;
use App\Http\Resources\NewsResource;
use App\Models\News;
use App\Services\NewsService;
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
    public function index()
    {

        return $this->newsService->getNews();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

        dd('hhhhHHHHHHHHH');
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
        return NewsResource::make($news);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
