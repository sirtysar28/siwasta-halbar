<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use App\Models\Traits\HasActivityLog;

class Document extends Model
{
    use HasActivityLog;

    protected $fillable = [
        'documentable_id', 'documentable_type', 'kategori', 'nama_asli',
        'nama_file', 'path', 'extensi', 'ukuran', 'versi',
        'tanda_tangan_digital', 'qr_code_token', 'uploaded_by',
    ];

    public function documentable(): MorphTo
    {
        return $this->morphTo();
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function getIconAttribute(): string
    {
        return match (strtolower($this->extensi)) {
            'pdf' => 'bi-file-earmark-pdf',
            'jpg', 'jpeg', 'png', 'gif', 'webp' => 'bi-file-earmark-image',
            'xls', 'xlsx', 'csv' => 'bi-file-earmark-spreadsheet',
            'doc', 'docx' => 'bi-file-earmark-word',
            'mp4', 'avi', 'mov' => 'bi-file-earmark-play',
            default => 'bi-file-earmark',
        };
    }

    public function getUkuranHumanAttribute(): string
    {
        $bytes = $this->ukuran;
        if ($bytes >= 1073741824) return number_format($bytes / 1073741824, 2) . ' GB';
        if ($bytes >= 1048576) return number_format($bytes / 1048576, 2) . ' MB';
        if ($bytes >= 1024) return number_format($bytes / 1024, 2) . ' KB';
        return $bytes . ' B';
    }

    public function getUrlAttribute(): string
    {
        return asset('storage/' . $this->path);
    }
}
