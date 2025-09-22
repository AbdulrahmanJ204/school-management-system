<?php

namespace App\Services\AppUpdate;

use App\Traits\TargetsHandler;

class AppUpdateService
{
    use InitAppUpdate, AppUpdateHelpers,
        ListAppUpdates, ListTrashedAppUpdates, ShowAppUpdate,
        StoreAppUpdate, UpdateAppUpdate,
        RestoreAppUpdate, SoftDeleteAppUpdate,
        ForceDeleteAppUpdate, CheckAppUpdate,
        TargetsHandler;

    private string $apiVersion;
    private string $apiPlatform;
    private string $apiUrl;
    private string $apiChangeLog;
    private string $apiIsForceUpdate;
    private string $queryPlatform;

    public function __construct()
    {
        $this->apiKeys();
        $this->generalVariables();
    }
}
