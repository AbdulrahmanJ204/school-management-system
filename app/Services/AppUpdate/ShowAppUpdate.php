<?php

namespace App\Services\AppUpdate;

use App\Http\Resources\AppUpdateResource;
use App\Models\AppUpdate;
use Illuminate\Http\JsonResponse;

trait ShowAppUpdate
{
    /**
     * Display the specified app update
     */
    public function show(AppUpdate $appUpdate): JsonResponse
    {
        try {
            return response()->json([
                'message' => 'App update retrieved successfully',
                'data' => new AppUpdateResource($appUpdate),
                'status_code' => 200
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve app update',
                'error' => $e->getMessage(),
                'status_code' => 500
            ], 500);
        }
    }
}
