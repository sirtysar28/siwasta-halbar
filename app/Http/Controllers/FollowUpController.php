<?php

namespace App\Http\Controllers;

use App\Models\Approval;
use App\Models\FollowUp;
use App\Models\Recommendation;
use App\Services\DocumentService;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class FollowUpController extends Controller
{
    public function index(Request $request)
    {
        $query = FollowUp::with(['recommendation.finding.opd', 'creator', 'approvals']);

        if ($request->filled('status')) $query->where('status', $request->status);
        if ($request->filled('opd_id')) {
            $query->whereHas('recommendation.finding', fn($q) => $q->where('opd_id', $request->opd_id));
        }

        // Operator OPD hanya melihat milik OPD-nya
        if (auth()->user()->hasRole('Operator OPD') && auth()->user()->opd_id) {
            $query->whereHas('recommendation.finding', fn($q) => $q->where('opd_id', auth()->user()->opd_id));
        }

        $items = $query->latest()->paginate(10)->appends($request->query());

        return view('follow-up.index', compact('items'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'recommendation_id' => 'required|exists:recommendations,id',
            'tanggal' => 'required|date',
            'uraian' => 'required|string',
            'persentase' => 'required|integer|min:0|max:100',
            'catatan' => 'nullable|string',
            'documents.*' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png,mp4,mov,avi|max:20480',
        ]);
        $data['created_by'] = auth()->id();
        $data['status'] = 'pending';

        $fu = FollowUp::create($data);

        // Upload dokumen bukti
        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $file) {
                DocumentService::upload($file, $fu, 'bukti_foto');
            }
        }

        // Update persentase rekomendasi (ambil persentase terbesar)
        $rec = $fu->recommendation;
        if ($data['persentase'] > $rec->persentase) {
            $rec->update(['persentase' => $data['persentase']]);
        }
        if ($data['persentase'] < 100 && $rec->status === 'baru') {
            $rec->update(['status' => 'diproses']);
        }
        if ($data['persentase'] >= 100) {
            $rec->update(['persentase' => 100]);
        }

        // Update status finding
        $this->syncFindingStatus($rec->finding_id);

        // Notifikasi ke Verifikator
        NotificationService::notifyRoles(['Verifikator', 'Inspektorat', 'Administrator', 'Super Administrator'],
            'Tindak Lanjut Baru Perlu Verifikasi',
            'Tindak lanjut dari ' . $rec->finding->opd->nama . ' menunggu verifikasi.',
            route('follow-up.show', $fu),
            'reminder'
        );

        activity()->performedOn($fu)->log('Menambah tindak lanjut');

        return back()->with('success', 'Tindak lanjut berhasil dikirim untuk diverifikasi.');
    }

    public function show(FollowUp $followUp)
    {
        $followUp->load(['recommendation.finding.audit', 'recommendation.finding.opd', 'documents', 'approvals.verifikator', 'creator']);
        return view('follow-up.show', ['item' => $followUp]);
    }

    public function verify(Request $request, FollowUp $followUp)
    {
        $data = $request->validate([
            'status' => 'required|in:disetujui,revisi,ditolak',
            'catatan' => 'nullable|string',
        ]);

        Approval::create([
            'follow_up_id' => $followUp->id,
            'verifikator_id' => auth()->id(),
            'status' => $data['status'],
            'catatan' => $data['catatan'] ?? null,
        ]);

        $followUp->update(['status' => $data['status']]);
        $rec = $followUp->recommendation;

        if ($data['status'] === 'disetujui') {
            if ($rec->persentase >= 100) {
                $rec->update(['status' => 'selesai']);
            } else {
                $rec->update(['status' => 'diproses']);
            }
        } elseif ($data['status'] === 'revisi') {
            $rec->update(['status' => 'revisi']);
            // Notifikasi ke Operator OPD
            NotificationService::notifyOpd($rec->finding->opd_id,
                'Tindak Lanjut Perlu Revisi',
                'Tindak lanjut Anda perlu direvisi: ' . Str::limit($data['catatan'], 100),
                route('follow-up.show', $followUp),
                'warning'
            );
        } elseif ($data['status'] === 'ditolak') {
            NotificationService::notifyOpd($rec->finding->opd_id,
                'Tindak Lanjut Ditolak',
                'Tindak lanjut ditolak: ' . Str::limit($data['catatan'], 100),
                route('follow-up.show', $followUp),
                'danger'
            );
        }

        $this->syncFindingStatus($rec->finding_id);

        activity()->performedOn($followUp)->log('Verifikasi tindak lanjut: ' . $data['status']);

        return back()->with('success', 'Tindak lanjut telah diverifikasi.');
    }

    public function destroy(FollowUp $followUp)
    {
        $followUp->delete();
        return back()->with('success', 'Tindak lanjut dihapus.');
    }

    private function syncFindingStatus(int $findingId): void
    {
        $finding = \App\Models\Finding::find($findingId);
        if (!$finding) return;
        $total = $finding->recommendations()->count();
        $selesai = $finding->recommendations()->where('status', 'selesai')->count();
        $revisi = $finding->recommendations()->where('status', 'revisi')->count();

        if ($total > 0 && $selesai === $total) {
            $finding->update(['status' => 'selesai']);
        } elseif ($revisi > 0) {
            $finding->update(['status' => 'revisi']);
        } else {
            $finding->update(['status' => 'diproses']);
        }
    }
}
