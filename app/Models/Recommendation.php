<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Traits\HasActivityLog;

class Recommendation extends Model
{
    use HasActivityLog;

    protected $fillable = [
        'finding_id', 'nomor', 'isi', 'deadline', 'pic',
        'persentase', 'status', 'created_by',
    ];

    protected $casts = ['deadline' => 'date'];

    public function finding(): BelongsTo
    {
        return $this->belongsTo(Finding::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function followUps(): HasMany
    {
        return $this->hasMany(FollowUp::class);
    }

    public function reminders(): HasMany
    {
        return $this->hasMany(Reminder::class);
    }

    public function getHariTersisaAttribute(): int
    {
        return now()->startOfDay()->diffInDays($this->deadline, false);
    }

    public function getWarnaStatusAttribute(): string
    {
        if ($this->status === 'selesai') return 'success';
        $sisa = $this->hari_tersisa;
        if ($sisa < 0) return 'danger';      // lewat deadline (merah)
        if ($sisa <= 7) return 'danger';
        if ($sisa <= 30) return 'warning';   // dalam proses (kuning)
        return 'success';
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'baru' => '<span class="badge bg-secondary">Baru</span>',
            'diproses' => '<span class="badge bg-warning text-dark">Diproses</span>',
            'selesai' => '<span class="badge bg-success">Selesai</span>',
            'revisi' => '<span class="badge bg-info text-dark">Revisi</span>',
            'ditolak' => '<span class="badge bg-danger">Ditolak</span>',
            default => '<span class="badge bg-secondary">' . $this->status . '</span>',
        };
    }
}
