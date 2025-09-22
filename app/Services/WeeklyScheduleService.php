<?php

namespace App\Services;

use App\Enums\WeekDay;
use App\Http\Resources\BulkScheduleResponseResource;
use App\Http\Resources\ScheduleListResource;
use App\Models\ClassPeriod;
use App\Models\Schedule;
use App\Models\Section;
use App\Models\TeacherSectionSubject;
use App\Models\TimeTable;
use Exception;
use Illuminate\Support\Facades\DB;

class WeeklyScheduleService
{
    /**
     * Get schedules for a section and timetable with comprehensive data
     */
    public function getSchedulesForSection(int $sectionId, int $timetableId, ?string $weekDay = null): ScheduleListResource
    {
        // Validate section and timetable exist
        $section = Section::findOrFail($sectionId);
        $timetable = TimeTable::findOrFail($timetableId);

        // Build query for schedules
        $query = Schedule::with([
            'classPeriod',
            'teacherSectionSubject.teacher.user',
            'teacherSectionSubject.subject',
            'teacherSectionSubject.section'
        ])->where('timetable_id', $timetableId)
            ->whereHas('teacherSectionSubject', function ($q) use ($sectionId) {
                $q->where('section_id', $sectionId);
            });

        if ($weekDay) {
            $query->where('week_day', $weekDay);
        }

        $schedules = $query->orderBy('week_day')
            ->orderBy('class_period_id')
            ->get();

        // Get class periods for the section's school shift
        $classPeriods = $this->getClassPeriodsForSection($sectionId);

        // Organize data by week days
        $weekDays = $this->organizeSchedulesByWeekDays($schedules, $classPeriods);

        // Prepare response data
        $responseData = (object) [
            'section_id' => $sectionId,
            'timetable_id' => $timetableId,
            'schedules' => $schedules,
            'week_days' => $weekDays,
            'class_periods' => $classPeriods,
        ];

        return new ScheduleListResource($responseData);
    }

    /**
     * Replace all schedules for a section and timetable (clear existing + create new)
     * @throws Exception
     */
    public function createOrUpdateBulkSchedules(array $data): BulkScheduleResponseResource
    {
        $sectionId = $data['section_id'];
        $timetableId = $data['timetable_id'];
        $schedules = $data['schedules'];

        // Validate section and timetable
        $section = Section::findOrFail($sectionId);
        $timetable = TimeTable::findOrFail($timetableId);

        // Validate teacher-section-subject assignments belong to the section
        $this->validateTeacherSectionSubjectAssignments($schedules, $sectionId);

        // Check for scheduling conflicts within the new schedules
        $this->checkSchedulingConflictsForReplacement($schedules, $timetableId, $sectionId);

        try {
            DB::beginTransaction();

            // Get count of existing schedules before deletion
            $existingSchedulesCount = Schedule::where('timetable_id', $timetableId)
                ->whereHas('teacherSectionSubject', function ($q) use ($sectionId) {
                    $q->where('section_id', $sectionId);
                })->count();

            // Check for scheduling conflicts excluding current section schedules
            $this->checkSchedulingConflictsExcludingSection($schedules, $timetableId, $sectionId);

            // Delete all existing schedules for this section and timetable
            Schedule::where('timetable_id', $timetableId)
                ->whereHas('teacherSectionSubject', function ($q) use ($sectionId) {
                    $q->where('section_id', $sectionId);
                })->delete();

            // Create new schedules
            $createdSchedules = [];
            $createdCount = 0;

            foreach ($schedules as $scheduleData) {
                $schedule = Schedule::create([
                    'class_period_id' => $scheduleData['class_period_id'],
                    'teacher_section_subject_id' => $scheduleData['teacher_section_subject_id'],
                    'timetable_id' => $timetableId,
                    'week_day' => $scheduleData['week_day'],
                    'created_by' => Auth::user()->id,
                ]);

                $createdSchedules[] = $schedule;
                $createdCount++;
            }

            DB::commit();

            $responseData = (object) [
                'section_id' => $sectionId,
                'timetable_id' => $timetableId,
                'deleted_count' => $existingSchedulesCount,
                'created_count' => $createdCount,
                'schedules' => $createdSchedules,
            ];

            return new BulkScheduleResponseResource($responseData);

        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Get class periods for a section
     */
    private function getClassPeriodsForSection(int $sectionId)
    {
        // First try to get class periods through school shift targets
        $classPeriods = ClassPeriod::whereHas('schoolShift.targets', function ($q) use ($sectionId) {
            $q->where('section_id', $sectionId);
        })->orderBy('period_order')->get();

        // If no class periods found through school shift targets,
        // get all class periods that are used in schedules for this section
        if ($classPeriods->isEmpty()) {
            $classPeriods = ClassPeriod::whereHas('schedules', function ($q) use ($sectionId) {
                $q->whereHas('teacherSectionSubject', function ($subQ) use ($sectionId) {
                    $subQ->where('section_id', $sectionId);
                });
            })->orderBy('period_order')->get();
        }

        // If still no class periods found, get all class periods (fallback)
        if ($classPeriods->isEmpty()) {
            $classPeriods = ClassPeriod::orderBy('period_order')->get();
        }

        return $classPeriods;
    }

    /**
     * Organize schedules by week days
     */
    private function organizeSchedulesByWeekDays($schedules, $classPeriods): array
    {
        $weekDays = [];
        $weekDayValues = WeekDay::values();

        foreach ($weekDayValues as $day) {
            $daySchedules = $schedules->where('week_day', $day);
            $organizedSchedules = [];

            foreach ($classPeriods as $period) {
                $periodSchedule = $daySchedules->where('class_period_id', $period->id)->first();
                if ($periodSchedule) {
                    $organizedSchedules[] = [
                        'period_order' => $period->period_order,
                        'period_name' => $period->name,
                        'subject' => $periodSchedule->teacherSectionSubject->subject->name ?? 'N/A',
                        'teacher' => $periodSchedule->teacherSectionSubject->teacher->user->name ?? 'N/A'
                    ];
                }
            }

            $weekDays[] = [
                'name' => ucfirst($day),
                'schedules' => $organizedSchedules
            ];
        }

        return $weekDays;
    }

    /**
     * Validate teacher-section-subject assignments belong to the section
     * @throws Exception
     */
    private function validateTeacherSectionSubjectAssignments(array $schedules, int $sectionId): void
    {
        $teacherSectionSubjectIds = collect($schedules)->pluck('teacher_section_subject_id')->unique();

        $invalidAssignments = TeacherSectionSubject::whereIn('id', $teacherSectionSubjectIds)
            ->where('section_id', '!=', $sectionId)
            ->pluck('id');

        if ($invalidAssignments->count() > 0) {
            throw new Exception('Some teacher-section-subject assignments do not belong to the specified section.');
        }
    }

    /**
     * Check for scheduling conflicts excluding schedules for a specific section
     * @throws Exception
     */
    private function checkSchedulingConflictsExcludingSection(array $schedules, int $timetableId, int $excludeSectionId): void
    {
        // Check for teacher double-booking with other sections only
        foreach ($schedules as $schedule) {
            $conflictingSchedule = Schedule::where('timetable_id', $timetableId)
                ->where('week_day', $schedule['week_day'])
                ->where('class_period_id', $schedule['class_period_id'])
                ->whereHas('teacherSectionSubject', function ($q) use ($schedule, $excludeSectionId) {
                    // Same teacher
                    $q->where('teacher_id', function ($subQ) use ($schedule) {
                        $subQ->select('teacher_id')
                            ->from('teacher_section_subjects')
                            ->where('id', $schedule['teacher_section_subject_id']);
                    })
                        // But different section (exclude current section)
                        ->where('section_id', '!=', $excludeSectionId);
                })->first();

            if ($conflictingSchedule) {
                // Get teacher info for better error message
                $teacherSectionSubject = TeacherSectionSubject::with('teacher.user', 'section')
                    ->find($schedule['teacher_section_subject_id']);

                $conflictingSectionSubject = $conflictingSchedule->teacherSectionSubject;

                throw new Exception(
                    'Teacher "' . $teacherSectionSubject->teacher->user->first_name . ' ' .
                    $teacherSectionSubject->teacher->user->last_name .
                    '" is already assigned to section "' . $conflictingSectionSubject->section->title .
                    '" during ' . ucfirst($schedule['week_day']) . ' at the same time slot.'
                );
            }
        }
    }

    /**
     * Check for scheduling conflicts only within the new schedules (for replacement)
     * @throws Exception
     */
    private function checkSchedulingConflictsForReplacement(array $schedules, int $timetableId, int $sectionId): void
    {
        // Check for teacher double-booking within the new schedules only
        foreach ($schedules as $index => $schedule) {
            // Get teacher ID for current schedule
            $currentTeacherSectionSubject = TeacherSectionSubject::find($schedule['teacher_section_subject_id']);
            if (!$currentTeacherSectionSubject) {
                continue;
            }

            // Check against all other schedules in the same array
            foreach ($schedules as $otherIndex => $otherSchedule) {
                if ($index === $otherIndex) {
                    continue; // Skip self
                }

                $otherTeacherSectionSubject = TeacherSectionSubject::find($otherSchedule['teacher_section_subject_id']);
                if (!$otherTeacherSectionSubject) {
                    continue;
                }

                // Check if same teacher is assigned to different periods on same day
                if ($currentTeacherSectionSubject->teacher_id === $otherTeacherSectionSubject->teacher_id &&
                    $schedule['week_day'] === $otherSchedule['week_day'] &&
                    $schedule['class_period_id'] === $otherSchedule['class_period_id']) {

                    throw new Exception(
                        'Teacher "' . $currentTeacherSectionSubject->teacher->user->first_name . ' ' .
                        $currentTeacherSectionSubject->teacher->user->last_name .
                        '" is assigned to the same time slot multiple times on ' .
                        ucfirst($schedule['week_day']) . '.'
                    );
                }

                // Check if same period and day has multiple different teachers (shouldn't happen but good to check)
                if ($schedule['class_period_id'] === $otherSchedule['class_period_id'] &&
                    $schedule['week_day'] === $otherSchedule['week_day'] &&
                    $currentTeacherSectionSubject->teacher_id !== $otherTeacherSectionSubject->teacher_id) {

                    throw new Exception(
                        'Multiple different teachers assigned to the same period on ' .
                        ucfirst($schedule['week_day']) . '. This is not allowed.'
                    );
                }
            }
        }
    }
}
