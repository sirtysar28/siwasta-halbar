<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use App\Models\Traits\HasActivityLog;

class Audit extends Model
{
    use HasActivityLog;

    protected $fillable = [
        'nomor', 'judul', 'tanggal_mulai', 'tanggal_selesai',
        'jenis_audit_id', 'tahun_audit_id', 'auditor_id',
        'instansi', 'keterangan', 'status', 'created_by',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
    ];

    public function jenisAudit(): BelongsTo
    {
        return $this->belongsTo(JenisAudit::class);
    }

    public function tahunAudit(): BelongsTo
    {
        return $this->belongsTo(TahunAudit::class);
    }

    public function auditor(): BelongsTo
    {
        return $this->belongsTo(Auditor::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function opds(): BelongsToMany
    {
        return $this->belongsToMany(Opd::class, 'audit_opd');
    }

    public function findings(): HasMany
    {
        return $this->hasMany(Finding::class);
    }

    public function documents(): MorphMany
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'draft' => '<span class="badge bg-secondary">Draft</span>',
            'berjalan' => '<span class="badge bg-primary">Berjalan</span>',
            'selesai' => '<span class="badge bg-success">Selesai</span>',
            'ditutup' => '<span class="badge bg-dark">Ditutup</span>',
            default => '<span class="badge bg-secondary">' . $this->status . '</span>',
        };
    }
}
