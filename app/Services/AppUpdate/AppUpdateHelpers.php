<?php

namespace App\Services\AppUpdate;

use App\Models\AppUpdate;

trait AppUpdateHelpers
{
    /**
     * Compare two semantic versions
     */
    private function compareVersions(string $version1, string $version2): int
    {
        $v1 = array_map('intval', explode('.', $version1));
        $v2 = array_map('intval', explode('.', $version2));

        for ($i = 0; $i < 3; $i++) {
            if (($v1[$i] ?? 0) > ($v2[$i] ?? 0)) {
                return 1;
            }
            if (($v1[$i] ?? 0) < ($v2[$i] ?? 0)) {
                return -1;
            }
        }

        return 0;
    }

    /**
     * Check if an update is available
     */
    private function isUpdateAvailable(string $currentVersion, string $latestVersion): bool
    {
        return $this->compareVersions($currentVersion, $latestVersion) < 0;
    }

    /**
     * Get the latest app update for a platform
     */
    private function getLatestAppUpdate(string $platform): ?AppUpdate
    {
        return AppUpdate::latestForPlatform($platform)->first();
    }
}
