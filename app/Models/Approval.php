<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Traits\HasActivityLog;

class Approval extends Model
{
    use HasActivityLog;

    protected $fillable = ['follow_up_id', 'verifikator_id', 'status', 'catatan'];

    public function followUp(): BelongsTo
    {
        return $this->belongsTo(FollowUp::class);
    }

    public function verifikator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verifikator_id');
    }
}
