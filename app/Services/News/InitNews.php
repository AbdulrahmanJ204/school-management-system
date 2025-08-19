<?php

namespace App\Services\News;

use App\Enums\StringsManager\NewsStr;
use App\Enums\StringsManager\QueryParams;

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
        $this->queryYear = QueryParams::Year->value;
        $this->apiSectionIds = NewsStr::apiSectionIds->value;
        $this->apiGradeIds = NewsStr::apiGradeIds->value;
        $this->queryGrade = QueryParams::Grade->value;
        $this->querySection = QueryParams::Section->value;
        $this->queryGeneral = QueryParams::General->value;

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
