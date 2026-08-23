<?php

namespace App\Http\Controllers\Api;

use App\Models\Opd;

class OpdApiController extends BaseApiController
{
    protected string $model = Opd::class;
    protected array $searchable = ['nama', 'kode', 'kepala'];
    protected array $with = [];
    protected array $rules = [
        'kode' => 'required|unique:opd,kode',
        'nama' => 'required|string|max:255',
        'kepala' => 'nullable|string|max:255',
        'alamat' => 'nullable|string',
        'email' => 'nullable|email',
        'telepon' => 'nullable|string|max:30',
        'is_active' => 'boolean',
    ];

    public function update(\Illuminate\Http\Request $request, $id)
    {
        $this->rules['kode'] = 'required|unique:opd,kode,' . $id;
        return parent::update($request, $id);
    }
}
