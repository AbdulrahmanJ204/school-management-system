<?php

namespace App\Services\AppUpdate;

use App\Http\Resources\AppUpdateResource;
use App\Models\AppUpdate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

trait ListAppUpdates
{
    /**
     * Display a listing of app updates
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = AppUpdate::query();

            // Filter by platform if provided
            if ($request->has($this->queryPlatform)) {
                $query->byPlatform($request->input($this->queryPlatform));
            }

            $appUpdates = $query->orderBy('created_at', 'desc')->get();

            return response()->json([
                'message' => 'App updates retrieved successfully',
                'data' => [
                    'updates' => AppUpdateResource::collection($appUpdates)
                ],
                'status_code' => 200
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve app updates',
                'error' => $e->getMessage(),
                'status_code' => 500
            ], 500);
        }
    }
}
