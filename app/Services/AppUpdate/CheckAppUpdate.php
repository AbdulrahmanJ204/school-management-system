<?php

namespace App\Services\AppUpdate;

use App\Http\Requests\AppUpdate\CheckAppUpdateRequest;
use App\Http\Resources\AppUpdateCheckResource;
use Illuminate\Http\JsonResponse;

trait CheckAppUpdate
{
    /**
     * Check for app updates
     */
    public function check(CheckAppUpdateRequest $request): JsonResponse
    {
        try {
            $currentVersion = $request->input($this->apiVersion);
            $platform = $request->input($this->apiPlatform);

            // Get the latest app update for the platform
            $latestUpdate = $this->getLatestAppUpdate($platform);

            if (!$latestUpdate) {
                return response()->json([
                    'message' => 'No updates available',
                    'data' => new AppUpdateCheckResource(null),
                    'status_code' => 200
                ], 200);
            }

            // Check if an update is available
            if ($this->isUpdateAvailable($currentVersion, $latestUpdate->version)) {
                return response()->json([
                    'message' => 'Update check completed successfully',
                    'data' => new AppUpdateCheckResource($latestUpdate),
                    'status_code' => 200
                ], 200);
            }

            // No update available
            return response()->json([
                'message' => 'No updates available',
                'data' => new AppUpdateCheckResource(null),
                'status_code' => 200
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to check for updates',
                'error' => $e->getMessage(),
                'status_code' => 500
            ], 500);
        }
    }
}
