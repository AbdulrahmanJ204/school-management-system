<?php

namespace App\Services\Files;

use App\Enums\StringsManager\Files\FileStr;
use App\Enums\StringsManager\Files\FileApi;
use App\Enums\StringsManager\QueryParams;

trait InitFiles
{





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
