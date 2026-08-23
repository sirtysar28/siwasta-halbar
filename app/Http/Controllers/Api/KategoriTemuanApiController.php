<?php

namespace App\Http\Controllers\Api;

use App\Models\KategoriTemuan;

class KategoriTemuanApiController extends BaseApiController
{
    protected string $model = KategoriTemuan::class;
    protected array $searchable = ['nama', 'kode'];
    protected array $rules = [
        'nama' => 'required|string|max:255',
        'kode' => 'required|unique:kategori_temuan,kode',
        'keterangan' => 'nullable|string',
    ];

    public function update(\Illuminate\Http\Request $request, $id)
    {
        $this->rules['kode'] = 'required|unique:kategori_temuan,kode,' . $id;
        return parent::update($request, $id);
    }
}
