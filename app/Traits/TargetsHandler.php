<?php

namespace App\Traits;

use App\Models\News;
use App\Models\NewsTarget;
use App\Models\SchoolShiftTarget;
use App\Models\Year;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

trait TargetsHandler
{

    private function handleTargetsOnCreate($request, $data, $model, $targetsClass): void
    {
        $userId = $request->user()->id;

        // TODO : Check if a selected sections make a grade.
        // The above Note will make headache if used with files because
        // teachers are included in the equation, so may be would do it for news.

        if ($request->filled($this->apiSectionIds)) {
            foreach ($data[$this->apiSectionIds] as $section_id) {
                $targetsClass::create([
                    $model->getForeignKey() => $model->id,
                    'grade_id' => null,
                    'section_id' => $section_id,
                    'created_by' => $userId,
                ]);
            }
        } else if ($request->filled($this->apiGradeIds)) {
            foreach ($data[$this->apiGradeIds] as $grade_id) {
                $targetsClass::create([
                    $model->getForeignKey() => $model->id,
                    'grade_id' => $grade_id,
                    'section_id' => null,
                    'created_by' => $userId,
                ]);
            }
        } else {
            $targetsClass::create([
                $model->getForeignKey() => $model->id,
                'grade_id' => null,
                'section_id' => null,
                'created_by' => $userId,
            ]);
        }
    }

    private function adminUpdateTargets($request, $data, $model, $targetsClass): void
    {
        $user = $request->user();
        if ($request->filled($this->apiSectionIds)) {
            $this->updateSections(
                userId: $user->id,
                data: $data,
                model: $model,
                targetsClass: $targetsClass,
            );
        } else if ($request->filled($this->apiGradeIds)) {
            $this->updateGrades(
                userId: $user->id,
                data: $data,
                model: $model,
                targetsClass: $targetsClass
            );
        } else if ($request->filled($this->apiIsGeneral) && $data[$this->apiIsGeneral]) {
            $this->updateGeneralTargets(
                userId: $user->id,
                model: $model,
                targetsClass: $targetsClass,
            );
        }
    }

    private function updateGrades($userId, $data, $model, $targetsClass): void
    {
        $model->targets()->sectionTargets()->delete();
        $model->targets()->generalTargets()->delete();
        $existingGrades =
            $model->targets()
                ->gradeTargets()
                ->pluck('grade_id')
                ->toArray();

        $gradesToDelete = array_diff($existingGrades, $data[$this->apiGradeIds]);
        $gradesToAdd = array_diff($data[$this->apiGradeIds], $existingGrades);
        if (!empty($gradesToDelete)) {
            $model
                ->targets()
                ->gradeTargets()
                ->inGrades($gradesToDelete)
                ->delete();
        }

        foreach ($gradesToAdd as $grade_id) {
            $targetsClass::create([
                $model->getForeignKey() => $model->id,
                'grade_id' => $grade_id,
                'section_id' => null,
                'created_by' => $userId,
            ]);
        }

    }

    public function updateGeneralTargets($userId, $model, $targetsClass): void
    {
        $alreadyGeneral = $model
            ->targets()
            ->generalTargets()
            ->exists();
        if ($alreadyGeneral) {
            return;
        }
        $model->targets()->delete();
        $targetsClass::create([
            $model->getForeignKey() => $model->id,
            'grade_id' => null,
            'section_id' => null,
            'created_by' => $userId,
        ]);
    }


    private function updateSections($userId, $data, $model, $targetsClass): void
    {

        $model->targets()->gradeTargets()->delete();
        $model->targets()->generalTargets()->delete();

        $existingSections = $model->targets()
            ->sectionTargets()
            ->pluck('section_id')
            ->toArray();


        $sectionsToDelete = array_diff($existingSections, $data[$this->apiSectionIds]);
        $sectionsToAdd = array_diff($data[$this->apiSectionIds], $existingSections);
        if (!empty($sectionsToDelete)) {
            $model->targets()
                ->sectionTargets()
                ->inSections($sectionsToDelete)
                ->delete();
        }

        // TODO : check if Sections are a complete grade. same note as above
        foreach ($sectionsToAdd as $section_id) {
            $targetsClass::create([
                $model->getForeignKey() => $model->id,
                'grade_id' => null,
                'section_id' => $section_id,
                'created_by' => $userId,
            ]);
        }
    }

}
