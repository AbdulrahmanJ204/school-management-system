<?php

namespace App\Services\Files;

// NOTES : FOR ADMIN , Files are done?


use App\Traits\TargetsHandler;

class FileService
{
    use InitFiles, FileHelpers,
        ListFiles, ShowFile,
        StoreFile, UpdateFile,
        RestoreFile, SoftDeleteFile,
        ForceDeleteFile, DownloadFile ,
        TargetsHandler;

    // API keys
    private string $apiTitle;
    private string $apiDescription;
    private string $apiSubjectId;
    private string $apiIsGeneral;
    private string $apiFile;
    private string $apiType;
    private string $apiNoSubject;
    private string $apiGradeIds;
    private string $apiSectionIds;
    // General Variables
    private string $storageDisk;
    private string $generalPath;
    private string $libraryPath;
    private string $queryYear;
    private string $querySubject;


    public function __construct()
    {
        $this->apiKeys();
        $this->generalVariables();
    }


}
