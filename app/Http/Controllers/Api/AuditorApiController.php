<?php

namespace App\Http\Controllers\Api;

use App\Models\Auditor;

class AuditorApiController extends BaseApiController
{
    protected string $model = Auditor::class;
    protected array $searchable = ['nama', 'instansi', 'email'];
    protected array $rules = [
        'nama' => 'required|string|max:255',
        'nip' => 'nullable|string|max:50',
        'jabatan' => 'nullable|string|max:255',
        'instansi' => 'nullable|string|max:255',
        'email' => 'nullable|email',
        'telepon' => 'nullable|string|max:30',
        'is_active' => 'boolean',
    ];
}
