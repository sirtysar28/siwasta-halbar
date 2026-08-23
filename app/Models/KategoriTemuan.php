<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Traits\HasActivityLog;

class KategoriTemuan extends Model
{
    use HasActivityLog;

    protected $table = 'kategori_temuan';

    protected $fillable = ['nama', 'kode', 'keterangan'];

    public function findings(): HasMany
    {
        return $this->hasMany(Finding::class);
    }
}
