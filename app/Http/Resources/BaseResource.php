<?php

namespace App\Http\Resources;


use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Base Resource Class
 * الفئة الأساسية لجميع الموارد
 * Contains common helper methods and utilities for all resources
 */
abstract class BaseResource extends JsonResource
{
    /**
     * Get formatted date string
     * الحصول على تاريخ منسق
     */
    protected function formatDate($date, $format = 'Y-m-d H:i:s'): ?string
    {
        return $date ? $date->format($format) : null;
    }

    /**
     * Get user name from created_by relationship
     * الحصول على اسم المستخدم من علاقة created_by
     */
    protected function getCreatedByName(): ?string
    {
        if ($this->relationLoaded('createdBy') && $this->createdBy) {
            return $this->createdBy->id . '-' .
                   trim($this->createdBy->first_name . ' ' . $this->createdBy->last_name);
        }
        return null;
    }

    /**
     * Safely load nested relationships
     * تحميل العلاقات المتداخلة بأمان
     */
    protected function safeLoad($relationship, $callback = null)
    {
        if ($this->relationLoaded($relationship)) {
            return $callback ? $callback($this->{$relationship}) : $this->{$relationship};
        }
        return null;
    }

    /**
     * Check if relationship is loaded and return resource
     * التحقق من تحميل العلاقة وإرجاع المورد
     */
    protected function whenLoadedResource($relationship, $resourceClass)
    {
        return $this->when(
            $this->relationLoaded($relationship),
            function () use ($relationship, $resourceClass) {
                return new $resourceClass($this->{$relationship});
            }
        );
    }

    /**
     * Check if relationship is loaded and return collection
     * التحقق من تحميل العلاقة وإرجاع مجموعة
     */
    protected function whenLoadedCollection($relationship, $resourceClass)
    {
        return $this->when(
            $this->relationLoaded($relationship),
            function () use ($relationship, $resourceClass) {
                return $resourceClass::collection($this->{$relationship});
            }
        );
    }

    /**
     * Check if relationship should be included based on request
     * التحقق من وجوب تضمين العلاقة بناءً على الطلب
     */
    protected function shouldIncludeRelationship(Request $request, string $relationship): bool
    {
        $includes = $request->query('include', '');
        
        if (empty($includes)) {
            return false;
        }
        
        // Split by comma to handle multiple includes
        $includeArray = array_map('trim', explode(',', $includes));
        
        // Check if the relationship is in the includes
        return in_array($relationship, $includeArray);
    }

    /**
     * Conditionally include relationship based on request
     * تضمين العلاقة بشكل مشروط بناءً على الطلب
     */
    protected function whenRequested(Request $request, string $relationship, $callback)
    {
        return $this->when(
            $this->shouldIncludeRelationship($request, $relationship),
            $callback
        );
    }

    /**
     * Load relationship only when explicitly requested
     * تحميل العلاقة فقط عند الطلب الصريح
     */
    protected function whenExplicitlyRequested(Request $request, string $relationship, $resourceClass)
    {
        return $this->when(
            $this->shouldIncludeRelationship($request, $relationship),
            function () use ($relationship, $resourceClass) {
                return $this->relationLoaded($relationship)
                    ? new $resourceClass($this->{$relationship})
                    : null;
            }
        );
    }

    /**
     * Load relationship collection only when explicitly requested
     * تحميل مجموعة العلاقات فقط عند الطلب الصريح
     */
    protected function whenExplicitlyRequestedCollection(Request $request, string $relationship, $resourceClass)
    {
        return $this->when(
            $this->shouldIncludeRelationship($request, $relationship),
            function () use ($relationship, $resourceClass) {
                return $this->relationLoaded($relationship)
                    ? $resourceClass::collection($this->{$relationship})
                    : null;
            }
        );
    }

    /**
     * Get current request instance
     * الحصول على مثيل الطلب الحالي
     */
    protected function getCurrentRequest(): ?Request
    {
        return app('request');
    }

    /**
     * Check if current request has specific include
     * التحقق من وجود include محدد في الطلب الحالي
     */
    protected function hasInclude(string $relationship): bool
    {
        $request = $this->getCurrentRequest();
        return $request ? $this->shouldIncludeRelationship($request, $relationship) : false;
    }
}
