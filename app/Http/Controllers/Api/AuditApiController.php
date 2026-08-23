<?php

namespace App\Http\Controllers\Api;

use App\Models\Audit;
use App\Services\DocumentService;
use Illuminate\Http\Request;

class AuditApiController extends BaseApiController
{
    protected string $model = Audit::class;
    protected array $searchable = ['nomor', 'judul', 'instansi'];
    protected array $with = ['jenisAudit', 'tahunAudit', 'auditor', 'opds'];
    protected array $rules = [
        'nomor' => 'required|unique:audits,nomor',
        'judul' => 'required|string|max:255',
        'tanggal_mulai' => 'required|date',
        'tanggal_selesai' => 'nullable|date',
        'jenis_audit_id' => 'required|exists:jenis_audit,id',
        'tahun_audit_id' => 'required|exists:tahun_audit,id',
        'auditor_id' => 'required|exists:auditors,id',
        'instansi' => 'required|string|max:255',
        'keterangan' => 'nullable|string',
        'status' => 'in:draft,berjalan,selesai,ditutup',
    ];

    public function update(Request $request, $id)
    {
        $this->rules['nomor'] = 'required|unique:audits,nomor,' . $id;
        return parent::update($request, $id);
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules);
        $data['created_by'] = auth()->id();
        $audit = Audit::create($data);
        if ($opds = $request->input('opds')) $audit->opds()->sync($opds);
        return $this->success($audit->load($this->with), 'Audit dibuat', 201);
    }

    public function uploadDocument(Request $request, Audit $audit)
    {
        $request->validate(['file' => 'required|file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png|max:10240']);
        $doc = DocumentService::upload($request->file('file'), $audit, 'surat');
        return $this->success($doc, 'Dokumen diunggah', 201);
    }
}
