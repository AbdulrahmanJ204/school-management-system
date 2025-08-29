<?php

namespace App\Services;

use App\Enums\WeekDay;
use App\Models\ClassSession;
use App\Models\TeacherSectionSubject;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class TeacherClassSessionService
{
    /**
     * Get past week class sessions for a teacher
     */
    public function getPastWeekSessions(int $subjectId, int $sectionId): array
    {
        // Check if teacher is authorized to access this subject and section
        $this->validateTeacherAccess($subjectId, $sectionId);

        // Calculate date range: from 7 days ago to today (inclusive)
        $endDate = Carbon::today();
        $startDate = Carbon::today()->subDays(7);

        $sessions = $this->getSessionsInDateRange($subjectId, $sectionId, $startDate, $endDate);

        return $this->formatSessions($sessions);
    }

    /**
     * Get upcoming two weeks class sessions for a teacher
     */
    public function getUpcomingSessions(int $subjectId, int $sectionId): array
    {
        // Check if teacher is authorized to access this subject and section
        $this->validateTeacherAccess($subjectId, $sectionId);

        // Calculate date range: from tomorrow to 14 days ahead
        $startDate = Carbon::tomorrow();
        $endDate = Carbon::tomorrow()->addDays(14);

        $sessions = $this->getSessionsInDateRange($subjectId, $sectionId, $startDate, $endDate);

        return $this->formatSessions($sessions);
    }

    /**
     * Validate that the authenticated teacher has access to the specified subject and section
     */
    private function validateTeacherAccess(int $subjectId, int $sectionId): void
    {
        $teacherId = Auth::user()->teacher->id;

        $teacherAccess = TeacherSectionSubject::where('teacher_id', $teacherId)
            ->where('subject_id', $subjectId)
            ->where('section_id', $sectionId)
            ->where('is_active', true)
            ->first();

        if (!$teacherAccess) {
            abort(403, 'غير مصرح لك بالوصول لحصص هذه المادة والشعبة');
        }
    }

    /**
     * Get class sessions within a specific date range
     */
    private function getSessionsInDateRange(int $subjectId, int $sectionId, Carbon $startDate, Carbon $endDate): \Illuminate\Database\Eloquent\Collection
    {
        return ClassSession::with(['schoolDay', 'classPeriod'])
            ->where('subject_id', $subjectId)
            ->where('section_id', $sectionId)
            ->where('teacher_id', Auth::user()->teacher->id)
            ->whereHas('schoolDay', function ($query) use ($startDate, $endDate) {
                $query->whereBetween('date', [$startDate, $endDate]);
            })
            ->orderBy('school_day_id')
            ->orderBy('class_period_id')
            ->get();
    }

    /**
     * Format sessions for response with Arabic titles
     */
    private function formatSessions(\Illuminate\Database\Eloquent\Collection $sessions): array
    {
        if ($sessions->isEmpty()) {
            return [];
        }

        return $sessions->map(function ($session) {
            return [
                'id' => $session->id,
                'title' => $this->generateArabicTitle($session)
            ];
        })->toArray();
    }

    /**
     * Generate Arabic title for class session
     * Format: "{arabic_day_name} {day}/{month} {class_period_name}"
     */
    private function generateArabicTitle(ClassSession $session): string
    {
        $schoolDay = $session->schoolDay;
        $classPeriod = $session->classPeriod;

        // Get Arabic day name
        $dayOfWeek = strtolower($schoolDay->date->format('l'));
        $arabicDayName = WeekDay::arabic()[$dayOfWeek] ?? '';

        // Format date as day/month (without year)
        $formattedDate = $schoolDay->date->format('j/n');

        // Get class period name
        $periodName = $classPeriod->name;

        return "{$arabicDayName} {$formattedDate} {$periodName}";
    }
}
