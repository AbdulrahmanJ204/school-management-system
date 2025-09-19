<?php

namespace App\Services\Files;

// NOTES : FOR ADMIN , Files are done?


use App\Traits\TargetsHandler;

class FileService
{
    use InitFiles,
        FileHelpers,
        ListFiles,
        ShowFile,
        StoreFile,
        UpdateFile,
        RestoreFile,
        SoftDeleteFile,
        ForceDeleteFile,
        DownloadFile,
        TargetsHandler;


    public function __construct()
    {

        $this->generalVariables();
    }
}
