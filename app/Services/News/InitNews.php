<?php

namespace App\Services\News;

use App\Enums\StringsManager\NewsStr;
use App\Enums\StringsManager\QueryParams;

trait InitNews
{
    /**
     * @return void
     */
    


    /**
     * @return void
     */
    private function generalVariables(): void
    {
        $this->storageDisk = NewsStr::StorageDisk->value;
        $this->imagesPath = NewsStr::newsPath->value;
    }

}
