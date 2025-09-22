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
        if ($request->filled('title')) {
            $updateData['title'] = $data['title'];
        }
        if ($request->filled('content')) {
            $content = $this->handleContent($data['content']);
            $updateData['content'] = $content;
        }
        if ($request->hasFile('photo')) {
            $photoPath = $this->handlePhoto($request, $news->photo);
            $updateData['photo'] = $photoPath;
        }
        if ($request->filled('remove_photo') && $data['remove_photo']) {
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
