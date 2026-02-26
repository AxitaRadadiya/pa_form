<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;
use App\Models\Activity;

trait LogsModelActivity
{
    public static function bootLogsModelActivity()
    {
        static::created(function ($model) {
            try {
                $user = Auth::user();
                $userName = $user->name ?? ($user->email ?? 'System');
                $tenantId = null;
                if (function_exists('tenant')) {
                    try { $tenantId = tenant()->id ?? null; } catch (\Throwable $e) { $tenantId = null; }
                }

                Activity::create([
                    'log_name' => class_basename($model),
                    'description' => "{$userName} created " . class_basename($model) . " ID: {$model->getKey()}",
                    'subject_type' => get_class($model),
                    'subject_id' => $model->getKey(),
                    'causer_type' => $user ? get_class($user) : null,
                    'causer_id' => $user ? $user->getKey() : null,
                    'properties' => ['attributes' => $model->getAttributes()],
                    'tenant_id' => $tenantId,
                ]);
            } catch (\Throwable $e) {
                // do nothing
            }
        });

        static::updated(function ($model) {
            try {
                $user = Auth::user();
                $userName = $user->name ?? ($user->email ?? 'System');
                $tenantId = null;
                if (function_exists('tenant')) {
                    try { $tenantId = tenant()->id ?? null; } catch (\Throwable $e) { $tenantId = null; }
                }

                $dirty = $model->getDirty();
                $old = [];
                foreach ($dirty as $field => $newValue) {
                    $old[$field] = $model->getOriginal($field);
                }

                Activity::create([
                    'log_name' => class_basename($model),
                    'description' => "{$userName} updated " . class_basename($model) . " ID: {$model->getKey()}",
                    'subject_type' => get_class($model),
                    'subject_id' => $model->getKey(),
                    'causer_type' => $user ? get_class($user) : null,
                    'causer_id' => $user ? $user->getKey() : null,
                    'properties' => ['attributes' => $model->getAttributes(), 'old' => $old],
                    'tenant_id' => $tenantId,
                ]);
            } catch (\Throwable $e) {
                // do nothing
            }
        });

        static::deleted(function ($model) {
            try {
                $user = Auth::user();
                $userName = $user->name ?? ($user->email ?? 'System');
                $tenantId = null;
                if (function_exists('tenant')) {
                    try { $tenantId = tenant()->id ?? null; } catch (\Throwable $e) { $tenantId = null; }
                }

                Activity::create([
                    'log_name' => class_basename($model),
                    'description' => "{$userName} deleted " . class_basename($model) . " ID: {$model->getKey()}",
                    'subject_type' => get_class($model),
                    'subject_id' => $model->getKey(),
                    'causer_type' => $user ? get_class($user) : null,
                    'causer_id' => $user ? $user->getKey() : null,
                    'properties' => ['old' => $model->getAttributes()],
                    'tenant_id' => $tenantId,
                ]);
            } catch (\Throwable $e) {
                // do nothing
            }
        });
    }
}
