<?php

namespace App\Services\AppUpdate;

trait InitAppUpdate
{
    private function apiKeys(): void
    {
        $this->apiVersion = 'version';
        $this->apiPlatform = 'platform';
        $this->apiUrl = 'url';
        $this->apiChangeLog = 'change_log';
        $this->apiIsForceUpdate = 'is_force_update';
        $this->queryPlatform = 'platform';
    }

    private function generalVariables(): void
    {
        // Add any general variables if needed
    }
}
