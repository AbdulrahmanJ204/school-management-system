<?php

namespace App\Services\News;

class NewsService
{
    use InitNews, NewsHelpers,
        ListNews, ShowNews,
        StoreNews, UpdateNews,
        RestoreNews, SoftDeleteNews,
        ForceDeleteNews;


    private string $apiTitle;
    private string $apiContent;
    private string $apiPhoto;
    private string $apiSectionIds;
    private string $apiGradeIds;
    private string $apiIsGeneral;
    private string $storageDisk;
    private string $imagesPath;
    private string $apiYearId;
    private string $apiRemovePhoto;

    public function __construct()
    {
        $this->apiKeys();
        $this->generalVariables();
    }
}
