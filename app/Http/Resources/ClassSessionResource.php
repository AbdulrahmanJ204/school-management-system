<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClassSessionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'schedule_id' => $this->schedule_id,
            'school_day_id' => $this->school_day_id,
            'teacher_id' => $this->teacher_id,
            'subject_id' => $this->subject_id,
            'section_id' => $this->section_id,
            'class_period_id' => $this->class_period_id,
            'status' => $this->status,
            'total_students_count' => $this->total_students_count,
            'present_students_count' => $this->present_students_count,
            'created_by' => $this->created_by,

            // Relationships
            'schedule' => new ScheduleResource($this->whenLoaded('schedule')),
            'school_day' => new SchoolDayResource($this->whenLoaded('schoolDay')),
            //'teacher' => new TeacherResource($this->whenLoaded('teacher')),
            'subject' => new SubjectResource($this->whenLoaded('subject')),
            'section' => new SectionResource($this->whenLoaded('section')),
            'class_period' => new ClassPeriodResource($this->whenLoaded('classPeriod')),
            'created_by_user' => new UserResource($this->whenLoaded('createdBy')),

            // Related data
            //'student_attendances' => StudentAttendanceResource::collection($this->whenLoaded('studentAttendances')),
            //'assignments' => AssignmentResource::collection($this->whenLoaded('assignments')),
            'study_notes' => StudyNoteResource::collection($this->whenLoaded('studyNotes')),

            // Computed properties
            'attendance_percentage' => $this->when($this->total_students_count > 0, function () {
                return round(($this->present_students_count / $this->total_students_count) * 100, 2);
            }),

            'can_be_started' => $this->canBeStarted(),
            'is_today' => $this->schoolDay && $this->schoolDay->date->isToday(),

            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
