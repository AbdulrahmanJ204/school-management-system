<?php

namespace App\Services\AppUpdate;

use App\Models\AppUpdate;
use Illuminate\Http\JsonResponse;

trait SoftDeleteAppUpdate
{
    /**
     * Soft delete the specified app update
     */
    public function softDelete(AppUpdate $appUpdate): JsonResponse
    {
        try {
            $appUpdate->delete();

            return response()->json([
                'message' => 'App update deleted successfully',
                'data' => null,
                'status_code' => 200
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete app update',
                'error' => $e->getMessage(),
                'status_code' => 500
            ], 500);
        }
    }
}
