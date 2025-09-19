<?php

namespace App\Services;

use App\Enums\Permissions\TimetablePermission;
use App\Exceptions\PermissionException;
use App\Exceptions\SchoolShiftNotFoundException;
use App\Helpers\AuthHelper;
use App\Helpers\ResponseHelper;
use App\Http\Resources\SchoolShiftResource;
use App\Models\SchoolShift;
use App\Models\SchoolShiftTarget;
use App\Models\Section;
use App\Traits\TargetsHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Throwable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SchoolShiftService
{
    use TargetsHandler;

   

   

    /**
     * @throws PermissionException
     */
    public function create($request): JsonResponse
    {
        $user = Auth::user();

        AuthHelper::authorize(TimetablePermission::create->value);

        $credentials = $request->validated();

        $credentials['created_by'] = $user->id;

        DB::beginTransaction();

        $shift = SchoolShift::create([
            'name'       => $credentials['name'],
            'start_time' => $credentials['start_time'],
            'end_time'   => $credentials['end_time'],
            'is_active'  => $credentials['is_active'],
            'created_by' => $user->id,
        ]);

        $this->handleTargetsOnCreate(
            request: $request,
            data: $credentials,
            model: $shift,
            targetsClass: SchoolShiftTarget::class
        );

        DB::commit();

        // Load the relationships before returning the resource
        $shift->load(['targets.grade', 'targets.section']);

        return ResponseHelper::jsonResponse(
            new SchoolShiftResource($shift),
            __('messages.school_shift.created'),
        );
    }

    /**
     * @throws Throwable
     * @throws PermissionException
     * @throws SchoolShiftNotFoundException
     */
    public function update($request, $id): JsonResponse
    {
        AuthHelper::authorize(TimetablePermission::update->value);

        $schoolShift = SchoolShift::with('targets')->find($id);

        if (!$schoolShift) {
            throw new SchoolShiftNotFoundException();
        }

        $credentials = $request->validated();

        try {
            DB::beginTransaction();

            // Update main shift fields
            $schoolShift->update([
                'name'       => $credentials['name'] ?? $schoolShift->name,
                'start_time' => $credentials['start_time'] ?? $schoolShift->start_time,
                'end_time'   => $credentials['end_time'] ?? $schoolShift->end_time,
                'is_active'  => $credentials['is_active'] ?? $schoolShift->is_active,
            ]);

            $this->adminUpdateTargets(
                request: $request,
                data: $credentials,
                model: $schoolShift,
                targetsClass: SchoolShiftTarget::class
            );

            DB::commit();

            $schoolShift->load(['targets.grade', 'targets.section']);

            return ResponseHelper::jsonResponse(
                new SchoolShiftResource($schoolShift),
                __('messages.school_shift.updated'),
                201
            );
        } catch (Throwable $e) {
            DB::rollBack();
            throw $e; // Re-throw so your global handler can catch it
        }
    }

    /**
     * @throws PermissionException
     * @throws SchoolShiftNotFoundException
     */
    public function delete($id)
    {
        AuthHelper::authorize(TimetablePermission::delete->value);

        $schoolShift = SchoolShift::find($id);

        if (!$schoolShift) {
            throw new SchoolShiftNotFoundException();
        }

        // Check if school shift has related data that would prevent deletion
        if (!$schoolShift->canBeDeleted()) {
            $reason = $schoolShift->getDeletionBlockReason();
            $details = $schoolShift->getDeletionBlockDetails();
            
            $message = __('messages.school_shift.' . $reason);
            if (!empty($details)) {
                $message .= ' (' . implode(', ', array_map(fn($key, $value) => "$key: $value", array_keys($details), $details)) . ')';
            }
            
            return ResponseHelper::jsonResponse(
                null,
                $message,
                400,
                false
            );
        }

        try {
            DB::beginTransaction();

            $schoolShift->delete();

            DB::commit();

            return ResponseHelper::jsonResponse(
                null,
                __('messages.school_shift.deleted'),
                200
            );
        } catch (Throwable $e) {
            DB::rollBack();
            
            // Log the actual error for debugging
            Log::error('School shift deletion failed: ' . $e->getMessage(), [
                'school_shift_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return ResponseHelper::jsonResponse(
                null,
                __('messages.school_shift.deletion_failed'),
                400,
                false
            );
        }
    }

    /**
     * @throws PermissionException
     * @throws SchoolShiftNotFoundException
     */
    public function get($id): JsonResponse
    {
        AuthHelper::authorize(TimetablePermission::get->value);

        $schoolShift = SchoolShift::find($id);

        if (!$schoolShift) {
            throw new SchoolShiftNotFoundException();
        }

        // Load the relationships before returning the resource
        $schoolShift->load(['targets.grade', 'targets.section']);

        return ResponseHelper::jsonResponse(
            new SchoolShiftResource($schoolShift),
            __('messages.school_shift.get')
        );
    }

    /**
     * @throws PermissionException
     */
    public function list(): JsonResponse
    {
        AuthHelper::authorize(TimetablePermission::list->value);

        $shifts = SchoolShift::with(['targets.grade', 'targets.section'])->latest()->get();

        return ResponseHelper::jsonResponse(
            SchoolShiftResource::collection($shifts),
            __('messages.school_shift.list')
        );
    }
}
