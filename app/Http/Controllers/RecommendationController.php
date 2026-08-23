<?php

namespace App\Http\Controllers;

use App\Models\Finding;
use App\Models\Recommendation;
use App\Models\Reminder;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RecommendationController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'finding_id' => 'required|exists:findings,id',
            'nomor' => 'required|string|max:100',
            'isi' => 'required|string',
            'deadline' => 'required|date|after_or_equal:today',
            'pic' => 'nullable|string|max:255',
        ]);
        $data['created_by'] = auth()->id();
        $data['status'] = 'baru';

        $rec = Recommendation::create($data);

        // Update finding status ke diproses
        $rec->finding->update(['status' => 'diproses']);

        // Generate reminders (Early Warning System)
        $this->generateReminders($rec);

        // Notify OPD users
        NotificationService::notifyOpd($rec->finding->opd_id,
            'Rekomendasi Baru #' . $rec->nomor,
            'Rekomendasi: ' . Str::limit($rec->isi, 100),
            route('recommendation.show', $rec)
        );

        return back()->with('success', 'Rekomendasi berhasil ditambahkan dengan reminder otomatis.');
    }

    public function show(Recommendation $recommendation)
    {
        $recommendation->load(['finding.audit', 'finding.opd', 'followUps.documents', 'followUps.approvals.verifikator', 'reminders', 'creator']);
        return view('recommendation.show', ['item' => $recommendation]);
    }

    public function update(Request $request, Recommendation $recommendation)
    {
        $data = $request->validate([
            'nomor' => 'required|string|max:100',
            'isi' => 'required|string',
            'deadline' => 'required|date',
            'pic' => 'nullable|string|max:255',
            'status' => 'required|in:baru,diproses,selesai,revisi,ditolak',
            'persentase' => 'required|integer|min:0|max:100',
        ]);

        $recommendation->update($data);

        if ($data['persentase'] >= 100 || $data['status'] === 'selesai') {
            $recommendation->update(['status' => 'selesai', 'persentase' => 100]);
        }

        return back()->with('success', 'Rekomendasi diperbarui.');
    }

    public function destroy(Recommendation $recommendation)
    {
        $recommendation->delete();
        return back()->with('success', 'Rekomendasi dihapus.');
    }

    private function generateReminders(Recommendation $rec): void
    {
        $days = array_map('intval', explode(',', config('app.reminder_days', '30,14,7,3,1')));
        foreach ($days as $d) {
            $tanggalKirim = $rec->deadline->copy()->subDays($d);
            if ($tanggalKirim->greaterThanOrEqualTo(now())) {
                Reminder::create([
                    'recommendation_id' => $rec->id,
                    'hari_sebelum' => $d,
                    'tanggal_kirim' => $tanggalKirim,
                    'media' => 'email',
                ]);
            }
        }
        // Hari H dan lewat deadline
        Reminder::create([
            'recommendation_id' => $rec->id,
            'hari_sebelum' => 0,
            'tanggal_kirim' => $rec->deadline,
            'media' => 'email',
        ]);
    }
}
