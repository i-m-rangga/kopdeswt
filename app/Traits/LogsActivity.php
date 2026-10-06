<?php

namespace App\Traits;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

trait LogsActivity
{
    protected static function bootLogsActivity(): void
    {
        static::created(function ($model) {
            $model->logActivity('create', $model->getLogData());
        });

        static::updated(function ($model) {
            $model->logActivity('update', $model->getLogData());
        });

        static::deleted(function ($model) {
            $model->logActivity('delete', $model->getLogData());
        });
    }

    protected function logActivity(string $action, array $data): void
    {
        // Skip if no user is authenticated (e.g., seeder, console)
        if (!Auth::check()) {
            return;
        }

        try {
            $logData = [
                'id_pengguna' => Auth::id(),
                'aktivitas' => $action,
                'modul' => $this->getLogModul(),
                'data_diproses' => $data,
                'waktu' => now(),
            ];

            ActivityLog::create($logData);
        } catch (\Exception $e) {
            // Fail silently - don't break the main operation if logging fails
            \Log::error('Activity log failed: ' . $e->getMessage());
        }
    }

    /**
     * Get the module name for logging (override in model)
     */
    protected function getLogModul(): string
    {
        return class_basename($this);
    }

    /**
     * Get data to be logged (override in model)
     * Return array with 'old' and/or 'new' keys
     */
    protected function getLogData(): array
    {
        $attributes = $this->getAttributes();

        // Remove sensitive/hidden fields
        $hidden = array_merge($this->getHidden(), ['password', 'remember_token']);
        foreach ($hidden as $field) {
            unset($attributes[$field]);
        }

        return [
            'new' => $attributes,
        ];
    }
}