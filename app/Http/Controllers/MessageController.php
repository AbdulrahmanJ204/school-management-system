<?php

namespace App\Http\Controllers;

use App\Exceptions\PermissionException;
use App\Http\Requests\MessageRequest;
use App\Http\Resources\MessageResource;
use App\Services\MessageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    protected MessageService $messageService;

    public function __construct(MessageService $messageService)
    {
        $this->messageService = $messageService;
    }

    /**
     * @throws PermissionException
     */
    public function index(): JsonResponse
    {
        return $this->messageService->listMessages();
    }

    /**
     * @throws PermissionException
     */
    public function trashed(): JsonResponse
    {
        return $this->messageService->listTrashedMessages();
    }

    /**
     * @throws PermissionException
     */
    public function store(MessageRequest $request): JsonResponse
    {
        return $this->messageService->createMessage($request);
    }

    /**
     * @throws PermissionException
     */
    public function show($id): JsonResponse
    {
        return $this->messageService->showMessage($id);
    }

    /**
     * @throws PermissionException
     */
    public function update(MessageRequest $request, $id): JsonResponse
    {
        return $this->messageService->updateMessage($request, $id);
    }

    /**
     * @throws PermissionException
     */
    public function destroy($id): JsonResponse
    {
        return $this->messageService->deleteMessage($id);
    }

    /**
     * @throws PermissionException
     */
    public function restore($id): JsonResponse
    {
        return $this->messageService->restoreMessage($id);
    }

    /**
     * @throws PermissionException
     */
    public function forceDelete($id): JsonResponse
    {
        return $this->messageService->forceDeleteMessage($id);
    }

    /**
     * @throws PermissionException
     */
    public function getByUser($userId): JsonResponse
    {
        return $this->messageService->getByUser($userId);
    }
}
