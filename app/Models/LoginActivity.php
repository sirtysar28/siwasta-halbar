<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Traits\HasActivityLog;

class LoginActivity extends Model
{
    use HasActivityLog;

    protected $fillable = [
        'user_id', 'ip_address', 'device', 'browser', 'platform',
        'login_at', 'logout_at',
    ];

    protected $casts = [
        'login_at' => 'datetime',
        'logout_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
