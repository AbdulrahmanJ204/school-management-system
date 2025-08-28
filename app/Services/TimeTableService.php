<?php

namespace App\Services;

use App\Enums\Permissions\TimetablePermission;
use App\Exceptions\PermissionException;
use App\Exceptions\TimetableNotFoundException;
use App\Helpers\AuthHelper;
use App\Helpers\ResponseHelper;
use App\Http\Resources\TimeTableResource;
use App\Models\TimeTable;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Throwable;

class TimeTableService
{
    /**
     * @throws PermissionException
     */
    public function create($request): JsonResponse
    {
        $user = auth()->user();

        AuthHelper::authorize(TimetablePermission::create_timetable->value);

        $credentials = $request->validated();

        try {
            DB::beginTransaction();

            $timetable = Timetable::create([
                'title'      => $credentials['title'] ?? null,
                'valid_from' => $credentials['valid_from'],
                'valid_to'   => $credentials['valid_to'],
                'is_active'  => $credentials['is_active'],
                'created_by' => $user->id,
            ]);

            DB::commit();

            return ResponseHelper::jsonResponse(
                new TimetableResource($timetable),
                __('messages.timetable.created'),
                201
            );
        } catch (Throwable $e) {
            DB::rollBack();
            return ResponseHelper::jsonResponse(
                null,
                $e->getMessage(),
                400,
                false
            );
        }
    }

    /**
     * @throws PermissionException
     * @throws TimetableNotFoundException
     */
    public function update($request, $id): JsonResponse
    {
        AuthHelper::authorize(TimetablePermission::update_timetable->value);

        $timetable = Timetable::findOrFail($id);

        if (!$timetable) {
            throw new TimetableNotFoundException();
        }

        $credentials = $request->validated();

        try {
            DB::beginTransaction();

            $timetable->update([
                'title'      => $credentials['title'] ?? $timetable->title,
                'valid_from' => $credentials['valid_from'] ?? $timetable->valid_from,
                'valid_to'   => $credentials['valid_to'] ?? $timetable->valid_to,
                'is_active'  => $credentials['is_active'] ?? $timetable->is_active,
            ]);

            DB::commit();

            return ResponseHelper::jsonResponse(
                new TimetableResource($timetable),
                __('messages.timetable.updated')
            );

        } catch (Throwable $e) {
            DB::rollBack();
            return ResponseHelper::jsonResponse(
                null,
                $e->getMessage(),
                400,
                false
            );
        }
    }

    /**
     * @throws PermissionException
     * @throws TimetableNotFoundException
     */
    public function delete($id): JsonResponse
    {
        AuthHelper::authorize(TimetablePermission::delete_timetable->value);

        $timetable = Timetable::findOrFail($id);
        if (!$timetable) {
            throw new TimetableNotFoundException();
        }

        try {
            DB::beginTransaction();

            $timetable->delete();

            DB::commit();

            return ResponseHelper::jsonResponse(
                null,
                __('messages.timetable.deleted')
            );

        } catch (Throwable $e) {
            DB::rollBack();
            return ResponseHelper::jsonResponse(
                null,
                $e->getMessage(),
                400,
                false
            );
        }
    }

    /**
     * @throws PermissionException
     * @throws TimetableNotFoundException
     */
    public function get($id): JsonResponse
    {
        AuthHelper::authorize(TimetablePermission::get_timetable->value);

        $timetable = Timetable::findOrFail($id);

        if (!$timetable) {
            throw new TimetableNotFoundException();
        }

        return ResponseHelper::jsonResponse(
            new TimetableResource($timetable),
            __('messages.timetable.get')
        );
    }

    /**
     * @throws PermissionException
     */
    public function list(): JsonResponse
    {
        AuthHelper::authorize(TimetablePermission::list_timetable->value);

        $timetables = Timetable::all();

        return ResponseHelper::jsonResponse(
            TimetableResource::collection($timetables),
            __('messages.timetable.list')
        );
    }
}
