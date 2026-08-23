<?php

namespace App\Http\Controllers\Api;

use App\Models\Audit;
use App\Models\Finding;
use App\Models\Recommendation;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardApiController extends BaseApiController
{
    public function statistik(Request $request)
    {
        return $this->success([
            'total_temuan' => Finding::count(),
            'total_rekomendasi' => Recommendation::count(),
            'selesai' => Recommendation::where('status', 'selesai')->count(),
            'dalam_proses' => Recommendation::whereIn('status', ['baru', 'diproses'])->count(),
            'belum_ditindaklanjuti' => Recommendation::where('status', 'baru')->count(),
            'lewat_deadline' => Recommendation::where('deadline', '<', today())->where('status', '!=', 'selesai')->count(),
            'progress' => round(Recommendation::avg('persentase') ?? 0, 1),
            'total_audit' => Audit::count(),
        ]);
    }

    public function grafik(Request $request)
    {
        $tahun = $request->get('tahun', date('Y'));

        $bulanan = collect(range(1, 12))->map(fn($m) => Recommendation::whereYear('created_at', $tahun)->whereMonth('created_at', $m)->count())->values();

        $perStatus = [
            'selesai' => Recommendation::where('status', 'selesai')->count(),
            'diproses' => Recommendation::where('status', 'diproses')->count(),
            'baru' => Recommendation::where('status', 'baru')->count(),
            'revisi' => Recommendation::where('status', 'revisi')->count(),
        ];

        $tahunan = collect(range((int)$tahun - 4, (int)$tahun))->map(fn($t) => [
            'tahun' => $t,
            'audit' => Audit::whereHas('tahunAudit', fn($q) => $q->where('tahun', $t))->count(),
            'temuan' => Finding::whereHas('audit.tahunAudit', fn($q) => $q->where('tahun', $t))->count(),
        ])->values();

        return $this->success(compact('bulanan', 'perStatus', 'tahunan', 'tahun'));
    }

    public function progress(Request $request)
    {
        $perOpd = \App\Models\Opd::select('opd.*')
            ->join('findings', 'findings.opd_id', '=', 'opd.id')
            ->join('recommendations', 'recommendations.finding_id', '=', 'findings.id')
            ->groupBy('opd.id')
            ->selectRaw('COALESCE(AVG(recommendations.persentase), 0) as avg_progress, COUNT(recommendations.id) as total')
            ->orderByDesc('avg_progress')
            ->limit(20)
            ->get();

        return $this->success($perOpd);
    }
}
