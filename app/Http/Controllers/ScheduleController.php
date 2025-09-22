<?php

namespace App\Http\Controllers;

use App\Enums\WeekDay;
use App\Helpers\ResponseHelper;
use App\Http\Requests\BulkCreateScheduleRequest;
use App\Services\ScheduleService;
use App\Services\WeeklyScheduleService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    protected ScheduleService $scheduleService;
    protected WeeklyScheduleService $weeklyScheduleService;

    public function __construct(ScheduleService $scheduleService, WeeklyScheduleService $weeklyScheduleService)
    {
        $this->scheduleService = $scheduleService;
        $this->weeklyScheduleService = $weeklyScheduleService;
    }

    /**
     * Get schedules for a section and timetable with comprehensive data
     */
    public function getSchedulesForSection(Request $request): JsonResponse
    {
        $request->validate([
            'section_id' => 'required|integer|exists:sections,id',
            'timetable_id' => 'required|integer|exists:time_tables,id',
            'week_day' => 'nullable|string|in:' . implode(',', WeekDay::values()),
        ]);

        $sectionId = $request->input('section_id');
        $timetableId = $request->input('timetable_id');
        $weekDay = $request->input('week_day');

        try {
            $result = $this->weeklyScheduleService->getSchedulesForSection($sectionId, $timetableId, $weekDay);

            return ResponseHelper::jsonResponse(
                $result,
                'Schedules retrieved successfully'
            );
        } catch (Exception $e) {
            return ResponseHelper::jsonResponse(
                null,
                $e->getMessage(),
                400
            );
        }
    }

    /**
     * Replace all schedules for a section (clear existing + create new)
     */
    public function createOrUpdateBulkSchedules(BulkCreateScheduleRequest $request): JsonResponse
    {
        try {
            $result = $this->weeklyScheduleService->createOrUpdateBulkSchedules($request->validated());

            $message = 'All schedules replaced successfully';
            if ($result->deleted_count > 0) {
                $message = "Replaced {$result->deleted_count} existing schedules with {$result->created_count} new schedules";
            }

            return ResponseHelper::jsonResponse(
                $result,
                $message
            );
        } catch (Exception $e) {
            return ResponseHelper::jsonResponse(
                null,
                $e->getMessage(),
                400
            );
        }
    }
}
