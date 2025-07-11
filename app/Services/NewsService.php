<?php

namespace App\Services;

use App\Models\News;
use App\Models\NewsTarget;
use App\Models\Section;
use App\Models\Semester;
use App\Models\Student;
use App\Models\StudentEntrollment;
use Illuminate\Database\Query\Builder;

class NewsService
{

    public function getStudentNews($request)
    {
        // first get current semester
        $currentSemesterID = Semester::all('id')
            ->where('end_date', '=', 'null')
            ->firstOrFail();
        $userId = auth()->user()->id;
        $studentID = Student::all('id')
            ->where('user_id', '=', $userId)
            ->firstOrFail();
        $studentEnrollment = StudentEntrollment::all()
            ->where([
                ['student_id', '=', $studentID->id],
                ['semester_id', '=', $currentSemesterID]
            ])
            ->firstOrFail();
        $section = Section::all()
            ->where('id', '=', $studentEnrollment->section_id)
            ->firstOrFail();
        $gradeID = $section->grade_id;

        $newsTarget = NewsTarget::all()->where(function (Builder $query) {
            $query->whereNull('grade_id')
                ->whereNull('section_id');
        })
            ->orWhere('grade_id', '=', $gradeID)
            ->orWhere('section_id', '=', $section->id)
            ->get();
        // get all news where the news target is global ,
        // or the grade_id is same as grade_id in student current enrollment (current semester)
        // or the section_id is same as section_id in student current enrollment (current semester)


        return News::all(['id', 'title', 'content', 'date', 'photo'])->where('id' , '=' , $newsTarget->id);
    }
}
