<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Traits\HasActivityLog;

class FollowUp extends Model
{
    use HasActivityLog;

    protected $fillable = [
        'recommendation_id', 'tanggal', 'uraian', 'persentase',
        'catatan', 'status', 'created_by',
    ];

    protected $casts = ['tanggal' => 'date'];

    public function recommendation(): BelongsTo
    {
        return $this->belongsTo(Recommendation::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approvals(): HasMany
    {
        return $this->hasMany(Approval::class);
    }

    public function documents()
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'pending' => '<span class="badge bg-secondary">Belum Diverifikasi</span>',
            'disetujui' => '<span class="badge bg-success">Disetujui</span>',
            'revisi' => '<span class="badge bg-warning text-dark">Revisi</span>',
            'ditolak' => '<span class="badge bg-danger">Ditolak</span>',
            default => '<span class="badge bg-secondary">' . $this->status . '</span>',
        };
    }
}
