<?php
namespace App\Services\Files;
use App\Enums\StringsManager\Files\FileStr;
use App\Enums\StringsManager\Files\FileApi;
use App\Enums\StringsManager\QueryParams;

trait InitFiles {
    /**
     * @return void
     */
    private function apiKeys(): void
    {
        $this->apiTitle = FileApi::apiTitle->value;
        $this->apiDescription = FileApi::apiDescription->value;
        $this->apiSubjectId = FileApi::apiSubjectId->value;
        $this->apiIsGeneral = FileApi::apiIsGeneral->value;
        $this->apiType = FileApi::apiType->value;
        $this->apiFile = FileApi::apiFile->value;
        $this->apiNoSubject = FileApi::apiNoSubject->value;
        $this->apiSectionIds = FileApi::apiSectionIds->value;
        $this->apiGradeIds = FileApi::apiGradeIds->value;
        $this->queryYear = QueryParams::Year->value;
        $this->queryType = QueryParams::Type->value;
        $this->querySubject = QueryParams::Subject->value;
        $this->queryGrade = QueryParams::Grade->value;
        $this->querySection = QueryParams::Section->value;
        $this->queryGeneral = QueryParams::General->value;
    }


    /**
     * @return void
     */
    private function generalVariables(): void
    {
        $this->storageDisk = FileStr::StorageDisk->value;
        $this->libraryPath = FileStr::LibraryPath->value;
        $this->generalPath = FileStr::GeneralPath->value;
    }

}
