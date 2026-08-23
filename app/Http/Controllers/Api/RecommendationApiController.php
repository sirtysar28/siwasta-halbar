<?php

namespace App\Http\Controllers\Api;

use App\Models\Recommendation;

class RecommendationApiController extends BaseApiController
{
    protected string $model = Recommendation::class;
    protected array $searchable = ['nomor', 'isi', 'pic'];
    protected array $with = ['finding.opd', 'followUps'];
    protected array $rules = [
        'finding_id' => 'required|exists:findings,id',
        'nomor' => 'required|string|max:100',
        'isi' => 'required|string',
        'deadline' => 'required|date',
        'pic' => 'nullable|string|max:255',
        'persentase' => 'integer|min:0|max:100',
        'status' => 'in:baru,diproses,selesai,revisi,ditolak',
    ];

    public function store(\Illuminate\Http\Request $request)
    {
        $data = $request->validate($this->rules);
        $data['created_by'] = auth()->id();
        return $this->success(Recommendation::create($data), 'Rekomendasi dibuat', 201);
    }
}
