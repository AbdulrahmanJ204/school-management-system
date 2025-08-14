<?php
namespace App\Services\Files;
use App\Enums\StringsManager\FileStr;

trait InitFiles {
    /**
     * @return void
     */
    private function apiKeys(): void
    {
        $this->apiTitle = FileStr::apiTitle->value;
        $this->apiDescription = FileStr::apiDescription->value;
        $this->apiSubjectId = FileStr::apiSubjectId->value;
        $this->apiIsGeneral = FileStr::apiIsGeneral->value;
        $this->apiType = FileStr::apiType->value;
        $this->apiFile = FileStr::apiFile->value;
        $this->apiNoSubject = FileStr::apiNoSubject->value;
        $this->apiSectionIds = FileStr::apiSectionIds->value;
        $this->apiGradeIds = FileStr::apiGradeIds->value;
        $this->queryYear = FileStr::queryYear->value;
        $this->querySubject = FileStr::querySubject->value;
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
