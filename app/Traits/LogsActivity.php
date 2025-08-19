<?php

namespace App\Traits;

use App\Services\LoggingService;
use Illuminate\Database\Eloquent\Model;

trait LogsActivity
{
    /**
     * Boot the trait.
     */
    protected static function bootLogsActivity()
    {
        static::created(function (Model $model) {
            static::logActivity('created', $model);
        });

        static::updated(function (Model $model) {
            static::logActivity('updated', $model);
        });

        static::deleted(function (Model $model) {
            static::logActivity('deleted', $model);
        });
    }

    /**
     * Log an activity for the model.
     */
    protected static function logActivity(string $action, Model $model): void
    {
        $loggingService = app(LoggingService::class);
        
        $changes = null;
        if ($action === 'created') {
            $changes = [
                'data' => $model->getAttributes(),
            ];
        }

        if ($action === 'updated' && $model->wasChanged()) {
            $changes = [
                'before' => $model->getOriginal(),
                // 'after' => $model->getAttributes(),
                'changed' => $model->getChanges(),
            ];
        }

        $loggingService->logActivity(
            action: $action,
            tableName: $model->getTable(),
            recordId: $model->getKey(),
            changes: $changes
        );
    }

    /**
     * Get the activity logs for this model.
     */
    public function activityLogs()
    {
        return $this->hasMany(\App\Models\ActivityLog::class, 'record_id')
            ->where('table_name', $this->getTable());
    }
}
