<?php

namespace App\Services\News;

use App\Exceptions\ImageUploadFailed;
use App\Http\Resources\NewsResource;
use App\Models\News;
use App\Models\NewsTarget;
use Carbon\Carbon;

trait StoreNews
{
    /**
     * @throws ImageUploadFailed
     */
    public function store($request): NewsResource
    {
        $user = auth()->user();
        $data = $request->validated();
        $photoPath = $this->handlePhoto($request);
        $publishDate = Carbon::now();
        $content = $this->handleContent($data[$this->apiContent]);
        $news = News::create([
            'title' => $data[$this->apiTitle],
            'content' => $content,
            'photo' => $photoPath,
            'publish_date' => $publishDate,
            'created_by' => $user->id,
        ]);

        $this->handleNewsTargetsOnCreate($request, $data, $news);
        // necessary to use load for relations here , because Autoload not work here I guess.
        $news->load('targets.grade', 'targets.section.grade');
        return NewsResource::make($news);
    }
    private function handleNewsTargetsOnCreate($request, $data, $news): array
    {
        $user = auth()->user();
        $targets = [];
        // TODO : Check if a selected sections make a grade.
        if ($request->filled($this->apiSectionIds)) {
            foreach ($data[$this->apiSectionIds] as $section_id) {
                $targets[] = NewsTarget::create([
                    'news_id' => $news->id,
                    'grade_id' => null,
                    'section_id' => $section_id,
                    'created_by' => $user->id,
                ]);
            }
        } else if ($request->filled($this->apiGradeIds)) {
            foreach ($data[$this->apiGradeIds] as $grade_id) {
                $targets[] = NewsTarget::create([
                    'news_id' => $news->id,
                    'grade_id' => $grade_id,
                    'section_id' => null,
                    'created_by' => $user->id,
                ]);
            }
        } else {
            NewsTarget::create([
                'news_id' => $news->id,
                'grade_id' => null,
                'section_id' => null,
                'created_by' => $user->id,
            ]);
        }
        return $targets;
    }
}
