<?php

namespace App\Services\News;

use App\Exceptions\ImageUploadFailed;
use App\Models\Year;
use Illuminate\Support\Facades\Storage;

trait NewsHelpers
{

    /**
     * @param $request
     * @return int|mixed
     */
    private function getYearId($request): mixed
    {
        return $request->filled($this->apiYearId) ? $request->year_id : Year::active()->get()->first()->id;
    }

    private function handlePhoto($request, $deletePath = null): ?string
    {
        $photoPath = null;
        if ($request->hasFile($this->apiPhoto)) {
            try {
                if ($deletePath && Storage::disk($this->storageDisk)->exists($deletePath)) {
                    Storage::disk($this->storageDisk)->delete($deletePath);
                }
                $image = $request->file($this->apiPhoto);
                $imageName = $image->hashName();
                $imagePath = $this->imagesPath . '/' . $imageName;

                if (!Storage::disk($this->storageDisk)->exists($imagePath)) {
                    $image->storeAs($this->imagesPath, $imageName, $this->storageDisk);
                }
                $photoPath = $imagePath;

            } catch (\Exception $e) {
                throw new ImageUploadFailed();
            }
        }
        return $photoPath;
    }


    private function handleContent(mixed $content): mixed
    {
        // TODO : know how content structure would be sent from front end and edit this
        $decodedContent = json_decode($content, true);
        // TODO: Handle messages
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \InvalidArgumentException('Invalid JSON content provided');
        }
        if (!isset($decodedContent['ops']) || !is_array($decodedContent['ops'])) {
            throw new \InvalidArgumentException('Content must have ops array structure');
        }
        return $content;
    }
}
