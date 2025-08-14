<?php

namespace App\Services\News;

use App\Enums\StringsManager\NewsStr;
use App\Helpers\ResponseHelper;
use App\Http\Resources\NewsResource;
use App\Models\News;
use App\Models\NewsTarget;
use App\Traits\TargetsHandler;
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
                if (Storage::disk($this->storageDisk)->exists($news->photo)) {
                    Storage::disk($this->storageDisk)->delete($news->photo);
                }
                $updateData['photo'] = null;
            }

        }


        $this->adminUpdateTargets(
            request: $request,
            data: $data,
            model: $news,
            targetsClass: NewsTarget::class);
        $news->update($updateData);
        $news->load('targets.section.grade', 'targets.grade');
        return ResponseHelper::jsonResponse(NewsResource::make($news), __(NewsStr::messageUpdated->value));
    }


}
