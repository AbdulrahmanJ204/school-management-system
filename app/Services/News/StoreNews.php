<?php

namespace App\Services\News;

use App\Exceptions\ImageUploadFailed;
use App\Http\Resources\NewsResource;
use App\Models\News;
use App\Models\NewsTarget;
use App\Traits\TargetsHandler;
use Carbon\Carbon;

trait StoreNews
{


    /**
     * @throws ImageUploadFailed
     */
    public function store($request): NewsResource
    {

        $data = $request->validated();
        $photoPath = $this->handlePhoto($request);
        $publishDate = Carbon::now();
        $content = $this->handleContent($data[$this->apiContent]);
        $news = News::create([
            'title' => $data[$this->apiTitle],
            'content' => $content,
            'photo' => $photoPath,
            'publish_date' => $publishDate,
            'created_by' => $request->user()->id,
        ]);

        $this->handleTargetsOnCreate(
            request: $request,
            data: $data,
            model: $news,
            targetsClass: NewsTarget::class
        );
        // necessary to use load for relations here , because Autoload not work here I guess.
        $news->load('targets.grade', 'targets.section.grade');
        return NewsResource::make($news);
    }

}
