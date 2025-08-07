<?php

namespace App\Services\News;

use App\Enums\StringsManager\NewsStr;

trait InitNews
{
    /**
     * @return void
     */
    private function apiKeys(): void
    {
        $this->apiTitle = NewsStr::apiTitle->value;
        $this->apiContent = NewsStr::apiContent->value;
        $this->apiIsGeneral = NewsStr::apiIsGeneral->value;
        $this->apiPhoto = NewsStr::apiPhoto->value;
        $this->apiRemovePhoto = NewsStr::apiRemovePhoto->value;
        $this->queryYear = NewsStr::queryYear->value;
        $this->apiSectionIds = NewsStr::apiSectionIds->value;
        $this->apiGradeIds = NewsStr::apiGradeIds->value;
    }


    /**
     * @return void
     */
    private function generalVariables(): void
    {
        $this->storageDisk = NewsStr::StorageDisk->value;
        $this->imagesPath = NewsStr::newsPath->value;
    }

}
