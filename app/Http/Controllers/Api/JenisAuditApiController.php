<?php

namespace App\Http\Controllers\Api;

use App\Models\JenisAudit;

class JenisAuditApiController extends BaseApiController
{
    protected string $model = JenisAudit::class;
    protected array $searchable = ['nama', 'kode'];
    protected array $rules = [
        'nama' => 'required|string|max:255',
        'kode' => 'required|unique:jenis_audit,kode',
        'keterangan' => 'nullable|string',
        'is_active' => 'boolean',
    ];

    public function update(\Illuminate\Http\Request $request, $id)
    {
        $this->rules['kode'] = 'required|unique:jenis_audit,kode,' . $id;
        return parent::update($request, $id);
    }
}
