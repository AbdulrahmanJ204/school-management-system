<?php

namespace App\Services\AppUpdate;

use App\Models\AppUpdate;
use Illuminate\Http\JsonResponse;

trait RestoreAppUpdate
{
    /**
     * Restore the specified soft deleted app update
     */
    public function restore(int $id): JsonResponse
    {
        try {
            $appUpdate = AppUpdate::withTrashed()->findOrFail($id);
            $appUpdate->restore();

            return response()->json([
                'message' => 'App update restored successfully',
                'data' => null,
                'status_code' => 200
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to restore app update',
                'error' => $e->getMessage(),
                'status_code' => 500
            ], 500);
        }
    }
}
