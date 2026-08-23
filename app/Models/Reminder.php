<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Traits\HasActivityLog;

class Reminder extends Model
{
    use HasActivityLog;

    protected $fillable = [
        'recommendation_id', 'hari_sebelum', 'tanggal_kirim',
        'is_sent', 'sent_at', 'media',
    ];

    protected $casts = [
        'tanggal_kirim' => 'date',
        'is_sent' => 'boolean',
        'sent_at' => 'datetime',
    ];

    public function recommendation(): BelongsTo
    {
        return $this->belongsTo(Recommendation::class);
    }
}
