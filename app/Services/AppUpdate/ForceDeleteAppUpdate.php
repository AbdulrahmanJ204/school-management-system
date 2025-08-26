<?php

namespace App\Services\AppUpdate;

use App\Models\AppUpdate;
use Illuminate\Http\JsonResponse;

trait ForceDeleteAppUpdate
{
    /**
     * Permanently delete the specified app update
     */
    public function forceDelete(int $id): JsonResponse
    {
        try {
            $appUpdate = AppUpdate::withTrashed()->findOrFail($id);
            $appUpdate->forceDelete();

            return response()->json([
                'message' => 'App update permanently deleted successfully',
                'data' => null,
                'status_code' => 200
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to permanently delete app update',
                'error' => $e->getMessage(),
                'status_code' => 500
            ], 500);
        }
    }
}
