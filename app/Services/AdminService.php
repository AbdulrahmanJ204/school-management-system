<?php

namespace App\Services;

use App\Enums\ClassPeriodType;
use App\Exceptions\PermissionException;
use App\Helpers\ResponseHelper;
use App\Http\Resources\ClassPeriodResource;
use App\Http\Resources\UserResource;
use App\Http\Requests\Admin\GetClassPeriodsBySectionRequest;
use App\Models\ClassPeriod;
use App\Models\Section;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class AdminService
{
    /**
     * @throws PermissionException
     */
    public function listAdmins(): JsonResponse
    {
        if (!auth()->user()->hasPermissionTo('عرض المشرفين')) {
            throw new PermissionException();
        }

        $admins = User::where('user_type', 'admin')
            ->with(['admin'])
            ->orderBy('first_name', 'asc')
            ->paginate(50);

        return ResponseHelper::jsonResponse(
            UserResource::collection($admins),
            __('messages.admin.listed'),
            200,
            true,
            $admins->lastPage(),
            $admins->total()
        );
    }

    /**
     * Get study class periods by section
     * 
     * @param GetClassPeriodsBySectionRequest $request
     * @return JsonResponse
     * @throws PermissionException
     */
    public function getClassPeriodsBySection(GetClassPeriodsBySectionRequest $request): JsonResponse
    {
        // Check admin permissions
        if (!auth()->user()->hasPermissionTo('عرض فترات دراسية')) {
            throw new PermissionException();
        }

        $sectionId = $request->section_id;

        // Check if section exists
        $section = Section::find($sectionId);
        if (!$section) {
            return ResponseHelper::jsonResponse(
                null,
                __('messages.section.not-found'),
                404
            );
        }

        // Get class periods through the relationship chain
        // Section -> SchoolShiftTarget -> SchoolShift -> ClassPeriod
        $classPeriods = ClassPeriod::whereHas('schoolShift.targets', function ($query) use ($sectionId, $section) {
                $query->where(function ($q) use ($sectionId, $section) {
                    // Match either: this target is for the specific section,
                    // OR this target is for the whole grade (grade_id and school_shift_id, but section_id is null)
                    $q->where('section_id', $sectionId)
                      ->orWhere(function ($q2) use ($section) {
                          $q2->whereNull('section_id')
                             ->where('grade_id', $section->grade_id);
                      });
                });
            })
            ->where('type', ClassPeriodType::STUDY)
            ->with('schoolShift')
            ->orderBy('period_order', 'asc')
            ->get();

        if ($classPeriods->isEmpty()) {
            return ResponseHelper::jsonResponse(
                ['class_periods' => []],
                __('messages.admin.class_periods.no_periods_found'),
                200
            );
        }

        return ResponseHelper::jsonResponse(
            ['class_periods' => ClassPeriodResource::collection($classPeriods)],
            __('messages.admin.class_periods.list_by_section'),
            200
        );
    }
}
