<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Traits\HasActivityLog;

class TahunAudit extends Model
{
    use HasActivityLog;

    protected $table = 'tahun_audit';

    protected $fillable = ['tahun', 'keterangan', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function audits(): HasMany
    {
        return $this->hasMany(Audit::class);
    }
}
