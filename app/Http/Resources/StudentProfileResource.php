<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            // Basic student information
            'firstName' => $this->resource['user']->first_name,
            'lastName' => $this->resource['user']->last_name,
            'fatherName' => $this->resource['user']->father_name,
            'gender' => $this->resource['user']->gender,
            'image' => $this->resource['user']->image,
            'birthDate' => $this->resource['user']->birth_date,
            'age' => $this->resource['age'],

            // Class information
            'className' => $this->resource['className'],
            'sectionNumber' => $this->resource['sectionNumber'],

            // Academic rankings
            'rankInSection' => $this->resource['rankInSection'],
            'rankAcrossSections' => $this->resource['rankAcrossSections'],

            // Academic performance
            'gpaPercentage' => $this->resource['gpaPercentage'],

            // Attendance statistics
            'attendancePercentage' => $this->resource['attendancePercentage'],
            'absencePercentage' => $this->resource['absencePercentage'],
            'justifiedAbsencePercentage' => $this->resource['justifiedAbsencePercentage'],
            'latenessPercentage' => $this->resource['latenessPercentage'],
            'oralPercentage' => $this->resource['oralPercentage'],
            'quizPercentage' => $this->resource['quizPercentage'],
        ];
    }
}
