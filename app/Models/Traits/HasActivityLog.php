<?php

namespace App\Models\Traits;

use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

trait HasActivityLog
{
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->useLogName(class_basename($this))
            ->setDescriptionForEvent(fn(string $eventName) => "{$eventName} data " . class_basename($this));
    }
}
