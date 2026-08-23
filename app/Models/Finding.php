<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use App\Models\Traits\HasActivityLog;

class Finding extends Model
{
    use HasActivityLog;

    protected $fillable = [
        'audit_id', 'nomor', 'judul', 'opd_id', 'nilai',
        'kategori_temuan_id', 'tingkat_risiko', 'deskripsi', 'status', 'created_by',
    ];

    protected $casts = ['nilai' => 'decimal:2'];

    public function audit(): BelongsTo
    {
        return $this->belongsTo(Audit::class);
    }

    public function opd(): BelongsTo
    {
        return $this->belongsTo(Opd::class);
    }

    public function kategoriTemuan(): BelongsTo
    {
        return $this->belongsTo(KategoriTemuan::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function recommendations(): HasMany
    {
        return $this->hasMany(Recommendation::class);
    }

    public function documents(): MorphMany
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    public function getProgressRataAttribute(): float
    {
        $recs = $this->recommendations;
        if ($recs->isEmpty()) return 0;
        return round($recs->avg('persentase'), 1);
    }

    public function getRisikoBadgeAttribute(): string
    {
        return match ($this->tingkat_risiko) {
            'rendah' => '<span class="badge bg-info text-dark">Rendah</span>',
            'sedang' => '<span class="badge bg-warning text-dark">Sedang</span>',
            'tinggi' => '<span class="badge bg-danger">Tinggi</span>',
            'sangat_tinggi' => '<span class="badge bg-danger">Sangat Tinggi</span>',
            default => '<span class="badge bg-secondary">' . $this->tingkat_risiko . '</span>',
        };
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'baru' => '<span class="badge bg-secondary">Baru</span>',
            'diproses' => '<span class="badge bg-warning text-dark">Diproses</span>',
            'selesai' => '<span class="badge bg-success">Selesai</span>',
            'ditolak' => '<span class="badge bg-danger">Ditolak</span>',
            'revisi' => '<span class="badge bg-info text-dark">Revisi</span>',
            default => '<span class="badge bg-secondary">' . $this->status . '</span>',
        };
    }
}
