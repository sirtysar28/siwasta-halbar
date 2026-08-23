<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Traits\HasActivityLog;

class NotificationCustom extends Model
{
    use HasActivityLog;

    protected $table = 'notifications_custom';

    protected $fillable = [
        'user_id', 'tipe', 'judul', 'pesan', 'url',
        'dibaca', 'is_broadcast', 'dibaca_pada',
    ];

    protected $casts = [
        'dibaca' => 'boolean',
        'is_broadcast' => 'boolean',
        'dibaca_pada' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getTipeIconAttribute(): string
    {
        return match ($this->tipe) {
            'success' => 'bi-check-circle-fill text-success',
            'warning' => 'bi-exclamation-triangle-fill text-warning',
            'danger' => 'bi-x-circle-fill text-danger',
            'reminder' => 'bi-clock-fill text-info',
            default => 'bi-info-circle-fill text-primary',
        };
    }
}
