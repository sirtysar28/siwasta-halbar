<?php

namespace App\Http\Controllers;

use App\Models\Recommendation;
use App\Models\Reminder;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class EarlyWarningController extends Controller
{
    public function index(Request $request)
    {
        $query = Reminder::with(['recommendation.finding.opd']);

        if ($request->filled('status')) {
            if ($request->status === 'pending') $query->where('is_sent', false);
            if ($request->status === 'sent') $query->where('is_sent', true);
        }
        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal_kirim', $request->tanggal);
        }

        $items = $query->orderBy('tanggal_kirim')->paginate(10);

        // Summary
        $todayCount = Reminder::whereDate('tanggal_kirim', today())->where('is_sent', false)->count();
        $overdueCount = Recommendation::where('deadline', '<', today())->where('status', '!=', 'selesai')->count();

        return view('early-warning.index', compact('items', 'todayCount', 'overdueCount'));
    }

    public function sendNow(Reminder $reminder)
    {
        $rec = $reminder->recommendation;

        $judul = $reminder->hari_sebelum === 0
            ? '⚠️ Deadline Hari Ini!'
            : "⏰ Deadline dalam {$reminder->hari_sebelum} hari";

        $pesan = "Rekomendasi #{$rec->nomor}: " . \Illuminate\Support\Str::limit($rec->isi, 150) .
                 "\nDeadline: {$rec->deadline->format('d M Y')}" .
                 "\nOPD: {$rec->finding->opd->nama}";

        NotificationService::notifyOpd($rec->finding->opd_id, $judul, $pesan, route('recommendation.show', $rec), 'reminder');
        NotificationService::notifyRoles(['Verifikator', 'Inspektorat'], $judul, $pesan, route('recommendation.show', $rec), 'reminder');

        $reminder->update(['is_sent' => true, 'sent_at' => now()]);

        return back()->with('success', 'Reminder berhasil dikirim.');
    }

    public function sendOverdue()
    {
        $overdue = Recommendation::with('finding.opd')
            ->where('deadline', '<', today())
            ->where('status', '!=', 'selesai')
            ->get();

        $count = 0;
        foreach ($overdue as $rec) {
            NotificationService::notifyOpd($rec->finding->opd_id,
                '🚨 LEWAT DEADLINE!',
                "Rekomendasi #{$rec->nomor} telah lewat deadline. Segera lakukan tindak lanjut!",
                route('recommendation.show', $rec),
                'danger'
            );
            $count++;
        }

        return back()->with('success', "{$count} reminder lewat deadline terkirim.");
    }
}
