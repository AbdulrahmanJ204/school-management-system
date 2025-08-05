<?php

namespace App\Services\News;

use App\Enums\StringsManager\NewsStr;
use App\Helpers\ResponseHelper;
use App\Http\Resources\NewsResource;
use App\Models\NewsTarget;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

trait UpdateNews
{
    public function update($request, $news): JsonResponse
    {

        $data = $request->validated();
        $updateData = [];
        if ($request->filled($this->apiTitle)) {
            $updateData['title'] = $data[$this->apiTitle];
        }
        if ($request->filled($this->apiContent)) {
            $content = $this->handleContent($data[$this->apiContent]);
            $updateData['content'] = $content;
        }
        if ($request->hasFile($this->apiPhoto)) {
            $photoPath = $this->handlePhoto($request, $news->photo);
            $updateData['photo'] = $photoPath;
        }
        if ($request->filled($this->apiRemovePhoto) && $data[$this->apiRemovePhoto]) {
            if ($news->photo) {
                Storage::disk($this->storageDisk)->delete($news->photo);
                $updateData['photo'] = null;
            }

        }


        $this->updateNewsTargetsOnUpdate($request, $data, $news);
        $news->update($updateData);
        $news->load('targets.section.grade', 'targets.grade');
        return ResponseHelper::jsonResponse(NewsResource::make($news), __(NewsStr::messageUpdated->value));
    }

    private function updateSections($news, $data): void
    {
        $user = auth()->user();
        $news->targets()->gradeTargets()->delete();
        $news->targets()->generalTargets()->delete();

        $existingSections = $news->targets()
            ->sectionTargets()
            ->pluck('section_id')
            ->toArray();


        $sectionsToDelete = array_diff($existingSections, $data[$this->apiSectionIds]);
        $sectionsToAdd = array_diff($data[$this->apiSectionIds], $existingSections);
        if (!empty($sectionsToDelete)) {
            $news->targets()
                ->sectionTargets()
                ->inSections($sectionsToDelete)
                ->delete();
        }
        // TODO : check if Sections are a complete grade.
        foreach ($sectionsToAdd as $section_id) {
            NewsTarget::create([
                'news_id' => $news->id,
                'grade_id' => null,
                'section_id' => $section_id,
                'created_by' => $user->id,
            ]);
        }
    }


    private function updateGrades($news, $data): void
    {
        $user = auth()->user();

        $news->targets()->sectionTargets()->delete();
        $news->targets()->generalTargets()->delete();
        $existingGrades =
            $news->targets()
                ->gradeTargets()
                ->pluck('grade_id')
                ->toArray();

        $gradesToDelete = array_diff($existingGrades, $data[$this->apiGradeIds]);
        $gradesToAdd = array_diff($data[$this->apiGradeIds], $existingGrades);
        if(!empty($gradesToDelete)) {
            $news
                ->targets()
                ->gradeTargets()
                ->inGrades($gradesToDelete)
                ->delete();
        }

        foreach ($gradesToAdd as $grade_id) {
            NewsTarget::create([
                'news_id' => $news->id,
                'grade_id' => $grade_id,
                'section_id' => null,
                'created_by' => $user->id,
            ]);
        }
    }


    private function updateNewsTargetsOnUpdate($request, $data, $news): void
    {
        $user = auth()->user();
        if ($request->filled($this->apiSectionIds)) {
            $this->updateSections($news, $data);
        } else if ($request->filled($this->apiGradeIds)) {
            $this->updateGrades($news, $data);
        } else if ($request->filled($this->apiIsGeneral) && $data[$this->apiIsGeneral]) {
            $news->targets()->delete();
            NewsTarget::create([
                'news_id' => $news->id,
                'grade_id' => null,
                'section_id' => null,
                'created_by' => $user->id,
            ]);
        }
    }

}
