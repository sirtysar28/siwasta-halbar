<?php

namespace App\Http\Controllers\Api;

use App\Models\Finding;

class FindingApiController extends BaseApiController
{
    protected string $model = Finding::class;
    protected array $searchable = ['nomor', 'judul'];
    protected array $with = ['audit', 'opd', 'kategoriTemuan', 'recommendations'];
    protected array $rules = [
        'audit_id' => 'required|exists:audits,id',
        'nomor' => 'required|string|max:100',
        'judul' => 'required|string|max:255',
        'opd_id' => 'required|exists:opd,id',
        'nilai' => 'required|numeric|min:0',
        'kategori_temuan_id' => 'required|exists:kategori_temuan,id',
        'tingkat_risiko' => 'required|in:rendah,sedang,tinggi,sangat_tinggi',
        'deskripsi' => 'nullable|string',
        'status' => 'in:baru,diproses,selesai,ditolak,revisi',
    ];

    public function store(\Illuminate\Http\Request $request)
    {
        $data = $request->validate($this->rules);
        $data['created_by'] = auth()->id();
        return $this->success(Finding::create($data), 'Temuan dibuat', 201);
    }
}
