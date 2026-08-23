<?php

namespace App\Http\Controllers;

use App\Models\Audit;
use App\Models\Document;
use App\Models\Finding;
use App\Models\Opd;
use App\Models\Recommendation;
use App\Models\User;
use App\Services\TahunAuditService;
use App\Models\TahunAudit;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $tahun = $request->get('tahun', date('Y'));

        // KPI Cards
        $totalTemuan = Finding::count();
        $totalRekomendasi = Recommendation::count();
        $sudahSelesai = Recommendation::where('status', 'selesai')->count();
        $dalamProses = Recommendation::whereIn('status', ['diproses', 'revisi', 'baru'])->count();
        $belumDitindaklanjuti = Recommendation::where('status', 'baru')->count();
        $lewatDeadline = Recommendation::where('deadline', '<', Carbon::today())
            ->where('status', '!=', 'selesai')
            ->count();

        $progress = $totalRekomendasi > 0
            ? round((Recommendation::avg('persentase') ?? 0), 1)
            : 0;

        // Widgets
        $totalDokumen = Document::count();
        $totalUser = User::count();
        $totalAudit = Audit::count();
        $reminderHariIni = Recommendation::whereDate('deadline', Carbon::today())->count();
        $notifikasiBaru = auth()->user()?->unreadNotificationsCount() ?? 0;

        // Grafik Pie Status
        $statusChart = [
            'selesai' => $sudahSelesai,
            'diproses' => $dalamProses,
            'baru' => $belumDitindaklanjuti,
            'lewat' => $lewatDeadline,
        ];

        // Grafik Bar per OPD (top 10)
        $perOpd = Opd::withCount(['findings'])
            ->orderByDesc('findings_count')
            ->limit(10)
            ->get();
        $perOpdLabels = $perOpd->pluck('nama')->toArray();
        $perOpdData = $perOpd->pluck('findings_count')->toArray();

        // Grafik Bulanan (tindak lanjut per bulan tahun berjalan)
        $bulanan = collect(range(1, 12))->map(function ($m) use ($tahun) {
            $count = Recommendation::whereYear('created_at', $tahun)
                ->whereMonth('created_at', $m)->count();
            return $count;
        })->toArray();

        // Grafik Tahunan (rekomendasi 5 tahun terakhir)
        $tahunList = collect(range((int)$tahun - 4, (int)$tahun));
        $tahunan = $tahunList->map(fn($t) => Recommendation::whereYear('created_at', $t)->count())->toArray();

        // Top 10 OPD Tercepat (avg progress tertinggi)
        $opdTercepat = Opd::select('opd.*')
            ->join('findings', 'findings.opd_id', '=', 'opd.id')
            ->join('recommendations', 'recommendations.finding_id', '=', 'findings.id')
            ->groupBy('opd.id', 'opd.kode', 'opd.nama', 'opd.kepala', 'opd.alamat', 'opd.email', 'opd.telepon', 'opd.is_active', 'opd.created_at', 'opd.updated_at')
            ->selectRaw('COALESCE(AVG(recommendations.persentase), 0) as avg_progress')
            ->orderByDesc('avg_progress')
            ->limit(10)
            ->get();

        // Top 10 OPD Terlambat (deadline lewat terbanyak)
        $opdTerlambat = Opd::select('opd.*')
            ->join('findings', 'findings.opd_id', '=', 'opd.id')
            ->join('recommendations', 'recommendations.finding_id', '=', 'findings.id')
            ->where('recommendations.deadline', '<', Carbon::today())
            ->where('recommendations.status', '!=', 'selesai')
            ->groupBy('opd.id', 'opd.kode', 'opd.nama', 'opd.kepala', 'opd.alamat', 'opd.email', 'opd.telepon', 'opd.is_active', 'opd.created_at', 'opd.updated_at')
            ->selectRaw('COUNT(recommendations.id) as total_late')
            ->orderByDesc('total_late')
            ->limit(10)
            ->get();

        // Aktivitas Terbaru
        $aktivitasTerbaru = \Spatie\Activitylog\Models\Activity::with('causer')
            ->latest()->limit(8)->get();

        // Reminder mendatang 7 hari
        $reminderList = Recommendation::with('finding.opd')
            ->whereBetween('deadline', [Carbon::today(), Carbon::today()->addDays(7)])
            ->where('status', '!=', 'selesai')
            ->orderBy('deadline')
            ->limit(5)
            ->get();

        $listTahun = TahunAuditService::list();

        return view('dashboard', compact(
            'totalTemuan', 'totalRekomendasi', 'sudahSelesai', 'dalamProses',
            'belumDitindaklanjuti', 'lewatDeadline', 'progress',
            'totalDokumen', 'totalUser', 'totalAudit', 'reminderHariIni',
            'notifikasiBaru', 'statusChart', 'perOpdLabels', 'perOpdData',
            'bulanan', 'tahunan', 'tahunList', 'tahun', 'opdTercepat',
            'opdTerlambat', 'aktivitasTerbaru', 'reminderList', 'listTahun'
        ));
    }
}
