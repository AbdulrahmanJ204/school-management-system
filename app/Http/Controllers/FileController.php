<?php

namespace App\Http\Controllers;

use App\Http\Requests\file\StoreFileRequest;
use App\Http\Requests\file\UpdateFileRequest;
use App\Models\File;
use App\Services\FileService;

class FileController extends Controller
{
    protected $fileService;
    public function __construct(FileService $fileService){
        $this->fileService = $fileService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->fileService->getFiles();
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreFileRequest $request)
    {
        return $this->fileService->store($request);
    }

    /**
     * Display the specified resource.
     */
    public function show(File $file)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateFileRequest $request, File $file)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(File $file)
    {
        //
    }
}
