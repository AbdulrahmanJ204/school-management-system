<?php

namespace App\Services\AppUpdate;

use App\Http\Requests\AppUpdate\StoreAppUpdateRequest;
use App\Http\Resources\AppUpdateResource;
use App\Models\AppUpdate;
use Illuminate\Http\JsonResponse;

trait StoreAppUpdate
{
    /**
     * Store a newly created app update
     */
    public function store(StoreAppUpdateRequest $request): JsonResponse
    {
        try {
            $appUpdate = AppUpdate::create([
                $this->apiVersion => $request->input($this->apiVersion),
                $this->apiPlatform => $request->input($this->apiPlatform),
                $this->apiUrl => $request->input($this->apiUrl),
                $this->apiChangeLog => $request->input($this->apiChangeLog),
                $this->apiIsForceUpdate => $request->input($this->apiIsForceUpdate) == 'true' ? true : false,
                'created_by' => auth()->id(),
            ]);

            return response()->json([
                'message' => 'App update created successfully',
                'data' => new AppUpdateResource($appUpdate),
                'status_code' => 201
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create app update',
                'error' => $e->getMessage(),
                'status_code' => 500
            ], 500);
        }
    }
}
