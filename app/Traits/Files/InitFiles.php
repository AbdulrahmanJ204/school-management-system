<?php
namespace App\Traits\Files;
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
    }

    /**
     * @return void
     */
    private function dbFields(): void
    {
        $this->dbTitle = 'title';
        $this->dbDescription = 'description';
        $this->dbSubjectId = 'subject_id';
        $this->dbFile = 'file';
        $this->dbSize = 'size';
        $this->dbType = 'type';
        $this->dbPublishDate = 'publish_date';
        $this->dbCreatedBy = 'created_by';
        $this->dbDeletedAt = 'deleted_at';
        $this->dbFileId = 'file_id';
        $this->dbGradeId = 'grade_id';
        $this->dbSectionId = 'section_id';
    }

    /**
     * @return void
     */
    private function generalVariables(): void
    {
        $this->storageDisk = 'public';
        $this->generalPath = FileStr::GeneralPath->value;
    }

}
