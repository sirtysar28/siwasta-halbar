<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Traits\HasActivityLog;

class Opd extends Model
{
    use HasActivityLog;

    protected $table = 'opd';

    protected $fillable = ['kode', 'nama', 'kepala', 'alamat', 'email', 'telepon', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function findings(): HasMany
    {
        return $this->hasMany(Finding::class);
    }

    public function audits()
    {
        return $this->belongsToMany(Audit::class, 'audit_opd');
    }

    public function getStatusWarnaAttribute(): string
    {
        return $this->is_active ? 'success' : 'danger';
    }
}
