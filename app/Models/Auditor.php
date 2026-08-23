<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Traits\HasActivityLog;

class Auditor extends Model
{
    use HasActivityLog;

    protected $fillable = ['nama', 'nip', 'jabatan', 'instansi', 'email', 'telepon', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function audits(): HasMany
    {
        return $this->hasMany(Audit::class);
    }
}
