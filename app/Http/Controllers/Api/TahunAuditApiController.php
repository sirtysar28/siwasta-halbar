<?php

namespace App\Http\Controllers\Api;

use App\Models\TahunAudit;

class TahunAuditApiController extends BaseApiController
{
    protected string $model = TahunAudit::class;
    protected array $searchable = ['tahun'];
    protected array $rules = [
        'tahun' => 'required|integer|unique:tahun_audit,tahun',
        'keterangan' => 'nullable|string',
        'is_active' => 'boolean',
    ];

    public function update(\Illuminate\Http\Request $request, $id)
    {
        $this->rules['tahun'] = 'required|integer|unique:tahun_audit,tahun,' . $id;
        return parent::update($request, $id);
    }
}
