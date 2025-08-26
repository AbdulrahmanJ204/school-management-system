<?php

namespace App\Services\AppUpdate;

use App\Http\Requests\AppUpdate\UpdateAppUpdateRequest;
use App\Http\Resources\AppUpdateResource;
use App\Models\AppUpdate;
use Illuminate\Http\JsonResponse;

trait UpdateAppUpdate
{
    /**
     * Update the specified app update
     */
    public function update(UpdateAppUpdateRequest $request, AppUpdate $appUpdate): JsonResponse
    {
        try {
            $updateData = [];

            if ($request->has($this->apiVersion)) {
                $updateData[$this->apiVersion] = $request->input($this->apiVersion);
            }
            if ($request->has($this->apiPlatform)) {
                $updateData[$this->apiPlatform] = $request->input($this->apiPlatform);
            }
            if ($request->has($this->apiUrl)) {
                $updateData[$this->apiUrl] = $request->input($this->apiUrl);
            }
            if ($request->has($this->apiChangeLog)) {
                $updateData[$this->apiChangeLog] = $request->input($this->apiChangeLog);
            }
            if ($request->has($this->apiIsForceUpdate)) {
                $updateData[$this->apiIsForceUpdate] = $request->input($this->apiIsForceUpdate) == 'true' ? true : false;
            }

            $appUpdate->update($updateData);

            return response()->json([
                'message' => 'App update updated successfully',
                'data' => new AppUpdateResource($appUpdate->fresh()),
                'status_code' => 200
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update app update',
                'error' => $e->getMessage(),
                'status_code' => 500
            ], 500);
        }
    }
}
