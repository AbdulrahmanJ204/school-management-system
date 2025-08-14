<?php

namespace App\Services;

use App\Enums\PermissionEnum;
use App\Exceptions\PermissionException;
use App\Helpers\ResponseHelper;
use App\Http\Resources\MessageResource;
use App\Models\Message;
use App\Traits\HasPermissionChecks;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class MessageService
{
    use HasPermissionChecks;

    /**
     * @throws PermissionException
     */
    public function listMessages(): JsonResponse
    {
        $this->checkPermission(PermissionEnum::VIEW_MESSAGES);

        $messages = Message::with([
//            'user',
        ])
            ->orderBy('id', 'desc')
            ->paginate(15);

        return ResponseHelper::jsonResponse(
            MessageResource::collection($messages),
            __('messages.message.listed')
        );
    }

    /**
     * @throws PermissionException
     */
    public function listTrashedMessages(): JsonResponse
    {
        $this->checkPermission(PermissionEnum::MANAGE_DELETED_MESSAGES);

        $messages = Message::onlyTrashed()
            ->with([
//                'user',
            ])
            ->orderBy('id', 'desc')
            ->paginate(15);

        return ResponseHelper::jsonResponse(
            MessageResource::collection($messages),
            __('messages.message.listed')
        );
    }

    /**
     * @throws PermissionException
     */
    public function createMessage($request): JsonResponse
    {
        $this->checkPermission(PermissionEnum::CREATE_MESSAGE);

        $data = $request->validated();
        $data['created_by'] = auth()->id();

        $message = Message::create($data);

        return ResponseHelper::jsonResponse(
            new MessageResource($message->load([
//                'user',
            ])),
            __('messages.message.created'),
            ResponseAlias::HTTP_CREATED,
        );
    }

    /**
     * @throws PermissionException
     */
    public function showMessage($id): JsonResponse
    {
        $this->checkPermission(PermissionEnum::VIEW_MESSAGE);

        $message = Message::with([
//            'user',
        ])
            ->findOrFail($id);

        return ResponseHelper::jsonResponse(
            new MessageResource($message),
            __('messages.message.fetched')
        );
    }

    /**
     * @throws PermissionException
     */
    public function updateMessage($request, $id): JsonResponse
    {
        $this->checkPermission(PermissionEnum::UPDATE_MESSAGE);

        $message = Message::findOrFail($id);
        $data = $request->validated();

        $message->update($data);

        return ResponseHelper::jsonResponse(
            new MessageResource($message->load([
//                'user',
            ])),
            __('messages.message.updated')
        );
    }

    /**
     * @throws PermissionException
     */
    public function deleteMessage($id): JsonResponse
    {
        $this->checkPermission(PermissionEnum::DELETE_MESSAGE);

        $message = Message::findOrFail($id);
        $message->delete();

        return ResponseHelper::jsonResponse(
            null,
            __('messages.message.deleted')
        );
    }

    /**
     * @throws PermissionException
     */
    public function restoreMessage($id): JsonResponse
    {
        $this->checkPermission(PermissionEnum::MANAGE_DELETED_MESSAGES);

        $message = Message::onlyTrashed()->findOrFail($id);
        $message->restore();

        return ResponseHelper::jsonResponse(
            new MessageResource($message->load([
//                'user',
            ])),
            __('messages.message.restored')
        );
    }

    /**
     * @throws PermissionException
     */
    public function forceDeleteMessage($id): JsonResponse
    {
        $this->checkPermission(PermissionEnum::MANAGE_DELETED_MESSAGES);

//        $message = Message::onlyTrashed()->findOrFail($id);
        $message = Message::findOrFail($id);
        $message->forceDelete();

        return ResponseHelper::jsonResponse(
            null,
            __('messages.message.force_deleted')
        );
    }

    /**
     * @throws PermissionException
     */
    public function getByUser($userId): JsonResponse
    {
        $this->checkPermission(PermissionEnum::VIEW_MESSAGES);

        $messages = Message::where('user_id', $userId)
            ->with([
//                'user',
            ])
            ->orderBy('id', 'desc')
            ->paginate(15);

        return ResponseHelper::jsonResponse(
            MessageResource::collection($messages),
            __('messages.message.listed')
        );
    }
}
