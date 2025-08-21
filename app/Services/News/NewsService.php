<?php

namespace App\Services\News;

use App\Traits\TargetsHandler;

class NewsService
{
    use InitNews, NewsHelpers,
        ListNews, ShowNews,
        StoreNews, UpdateNews,
        RestoreNews, SoftDeleteNews,
        ForceDeleteNews , TargetsHandler;


    private string $apiTitle;
    private string $apiContent;
    private string $apiPhoto;
    private string $apiSectionIds;
    private string $apiGradeIds;
    private string $apiIsGeneral;
    private string $storageDisk;
    private string $imagesPath;
    private string $queryYear;
    private string $apiRemovePhoto;
    private string $querySection;
    private string $queryGrade;
    private string $queryGeneral;


    public function __construct()
    {
        $this->apiKeys();
        $this->generalVariables();
    }

}
