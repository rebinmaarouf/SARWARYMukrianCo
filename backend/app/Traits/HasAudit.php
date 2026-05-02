<?php

namespace App\Traits;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

trait HasAudit
{
    public static function bootHasAudit()
    {
        static::created(function ($model) {
            $model->logActivity('created', null, $model->getAttributes());
        });

        static::updated(function ($model) {
            $before = array_intersect_key($model->getOriginal(), $model->getDirty());
            $after = $model->getDirty();
            
            // Don't log if only timestamps changed
            unset($before['updated_at'], $after['updated_at']);
            
            if (count($after) > 0) {
                $model->logActivity('updated', $before, $after);
            }
        });

        static::deleted(function ($model) {
            $action = method_exists($model, 'isForceDeleting') && $model->isForceDeleting() ? 'permanently_deleted' : 'deleted';
            $model->logActivity($action, $model->getAttributes(), null);
        });

        if (method_exists(static::class, 'restored')) {
            static::restored(function ($model) {
                $model->logActivity('restored', null, $model->getAttributes());
            });
        }
    }

    protected function logActivity($action, $before, $after)
    {
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'model_type' => get_class($this),
            'model_id' => $this->id,
            'before' => $before,
            'after' => $after,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
