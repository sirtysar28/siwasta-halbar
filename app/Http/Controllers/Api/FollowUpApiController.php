<?php

namespace App\Http\Controllers\Api;

use App\Models\Approval;
use App\Models\FollowUp;
use App\Services\DocumentService;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class FollowUpApiController extends BaseApiController
{
    protected string $model = FollowUp::class;
    protected array $searchable = ['uraian'];
    protected array $with = ['recommendation.finding.opd', 'documents', 'approvals.verifikator', 'creator'];
    protected array $rules = [
        'recommendation_id' => 'required|exists:recommendations,id',
        'tanggal' => 'required|date',
        'uraian' => 'required|string',
        'persentase' => 'required|integer|min:0|max:100',
        'catatan' => 'nullable|string',
        'status' => 'in:pending,disetujui,revisi,ditolak',
    ];

    public function store(Request $request)
    {
        $data = $request->validate($this->rules);
        $data['created_by'] = auth()->id();
        $data['status'] = $data['status'] ?? 'pending';
        $fu = FollowUp::create($data);

        $rec = $fu->recommendation;
        if ($data['persentase'] > $rec->persentase) {
            $rec->update(['persentase' => $data['persentase']]);
        }

        return $this->success($fu->load($this->with), 'Tindak lanjut dibuat', 201);
    }

    public function uploadBukti(Request $request, FollowUp $follow_up)
    {
        $request->validate([
            'file' => 'required|file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png,mp4|max:20480',
            'kategori' => 'in:surat,bukti_foto,laporan,ba,nota_dinas,file_pendukung',
        ]);
        $doc = DocumentService::upload($request->file('file'), $follow_up, $request->get('kategori', 'bukti_foto'));
        return $this->success($doc, 'Bukti diunggah', 201);
    }

    public function verify(Request $request, FollowUp $follow_up)
    {
        $data = $request->validate([
            'status' => 'required|in:disetujui,revisi,ditolak',
            'catatan' => 'nullable|string',
        ]);

        Approval::create([
            'follow_up_id' => $follow_up->id,
            'verifikator_id' => auth()->id(),
            'status' => $data['status'],
            'catatan' => $data['catatan'] ?? null,
        ]);

        $follow_up->update(['status' => $data['status']]);
        $rec = $follow_up->recommendation;

        if ($data['status'] === 'disetujui') {
            $rec->update(['status' => $rec->persentase >= 100 ? 'selesai' : 'diproses']);
        } elseif ($data['status'] === 'revisi') {
            $rec->update(['status' => 'revisi']);
            NotificationService::notifyOpd($rec->finding->opd_id,
                'Tindak Lanjut Perlu Revisi',
                'Tindak lanjut perlu direvisi: ' . Str::limit($data['catatan'], 100),
                null, 'warning');
        }

        return $this->success($follow_up->fresh()->load($this->with), 'Verifikasi berhasil');
    }
}
