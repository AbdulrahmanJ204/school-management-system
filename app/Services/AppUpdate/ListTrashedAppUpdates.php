<?php

namespace App\Services\AppUpdate;

use App\Http\Resources\AppUpdateResource;
use App\Models\AppUpdate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

trait ListTrashedAppUpdates
{
    /**
     * Display a listing of trashed (soft deleted) app updates
     */
    public function listTrashed(Request $request): JsonResponse
    {
        try {
            $query = AppUpdate::onlyTrashed();

            // Filter by platform if provided
            if ($request->has($this->queryPlatform)) {
                $query->byPlatform($request->input($this->queryPlatform));
            }

            $trashedUpdates = $query->orderBy('deleted_at', 'desc')->get();

            return response()->json([
                'message' => 'Trashed app updates retrieved successfully',
                'data' => [
                    'trashed_updates' => AppUpdateResource::collection($trashedUpdates)
                ],
                'status_code' => 200
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve trashed app updates',
                'error' => $e->getMessage(),
                'status_code' => 500
            ], 500);
        }
    }
}
