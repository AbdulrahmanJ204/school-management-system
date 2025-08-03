<?php

namespace App\Http\Controllers;

use App\Exceptions\PermissionException;
use App\Http\Requests\file\StoreFileRequest;
use App\Http\Requests\file\UpdateFileRequest;
use App\Models\File;
use App\Services\FileService;
use Illuminate\Http\JsonResponse;
use Throwable;

class FileController extends Controller
{
    protected FileService $fileService;
    public function __construct(FileService $fileService)
    {
        $this->fileService = $fileService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        return $this->fileService->list();
    }


    /**
     * Store a newly created resource in storage.
     * @throws PermissionException
     */
    public function store(StoreFileRequest $request): JsonResponse
    {
        return $this->fileService->store($request);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $file): JsonResponse
    {
        return $this->fileService->show($file);
    }

    /**
     * Update the specified resource in storage.
     * @throws PermissionException
     */
    public function update(UpdateFileRequest $request, File $file): JsonResponse
    {
        return $this->fileService->update($request, $file);
    }

    /**
     * Remove the specified resource from storage.
     * @throws PermissionException
     * @throws Throwable
     */
    public function destroy(File $file): JsonResponse
    {
        return $this->fileService->destroy($file);
    }

    /**
     * @throws PermissionException
     */
    public function restore($fileId): JsonResponse
    {
        return $this->fileService->restore($fileId);
    }

    public function download($fileId){
        return $this->fileService->download($fileId);
    }

    /**
     * @throws Throwable
     * @throws PermissionException
     */
    public function delete($fileId){
        return $this->fileService->delete($fileId);
    }
}
