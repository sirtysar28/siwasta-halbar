<?php

namespace App\Http\Controllers;

use App\Exports\ReportExport;
use App\Models\Audit;
use App\Models\Auditor;
use App\Models\Finding;
use App\Models\Opd;
use App\Models\Recommendation;
use App\Services\TahunAuditService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $tahun = $request->get('tahun', date('Y'));
        $type = $request->get('type', 'audit');

        $data = match ($type) {
            'audit' => Audit::withCount('findings')->with(['jenisAudit', 'tahunAudit', 'auditor'])
                ->when($tahun, fn($q) => $q->whereHas('tahunAudit', fn($q2) => $q2->where('tahun', $tahun)))->get(),
            'temuan' => Finding::with(['audit', 'opd', 'kategoriTemuan'])
                ->when($request->filled('opd_id'), fn($q) => $q->where('opd_id', $request->opd_id))->get(),
            'rekomendasi' => Recommendation::with(['finding.audit', 'finding.opd'])->get(),
            'opd' => Opd::withCount('findings')->with(['findings.recommendations'])->get(),
            'auditor' => Auditor::withCount('audits')->get(),
            'deadline' => Recommendation::with(['finding.opd'])->where('status', '!=', 'selesai')->orderBy('deadline')->get(),
            'tindak_lanjut' => \App\Models\FollowUp::with(['recommendation.finding.opd', 'creator', 'approvals'])->get(),
            default => collect(),
        };

        $tahuns = TahunAuditService::years();
        $opds = Opd::where('is_active', true)->orderBy('nama')->get();

        return view('laporan.index', compact('data', 'type', 'tahun', 'tahuns', 'opds'));
    }

    public function export(Request $request, $type, $format)
    {
        $tahun = $request->get('tahun', date('Y'));
        $data = $this->getData($type, $request);
        $title = 'Laporan ' . ucfirst(str_replace('_', ' ', $type)) . ' ' . $tahun;
        $fileName = 'laporan-' . $type . '-' . date('Y-m-d');

        $view = 'laporan.pdf.' . $type;

        if ($format === 'pdf') {
            $pdf = \PDF::loadView('laporan.pdf.template', compact('data', 'type', 'title', 'tahun'))
                ->setPaper('a4', 'landscape');
            return $pdf->download($fileName . '.pdf');
        }

        if (in_array($format, ['excel', 'csv'])) {
            return Excel::download(new ReportExport($data, $type), $fileName . '.xlsx');
        }

        return back()->with('error', 'Format tidak didukung.');
    }

    private function getData($type, Request $request)
    {
        $tahun = $request->get('tahun', date('Y'));
        return match ($type) {
            'audit' => Audit::with(['jenisAudit', 'tahunAudit', 'auditor', 'findings'])
                ->whereHas('tahunAudit', fn($q) => $q->where('tahun', $tahun))->get(),
            'temuan' => Finding::with(['audit', 'opd', 'kategoriTemuan', 'recommendations'])->get(),
            'rekomendasi' => Recommendation::with(['finding.audit', 'finding.opd'])->get(),
            'opd' => Opd::with(['findings.recommendations', 'users'])->get(),
            'auditor' => Auditor::with('audits')->get(),
            'deadline' => Recommendation::with(['finding.opd'])->where('status', '!=', 'selesai')->orderBy('deadline')->get(),
            'tindak_lanjut' => \App\Models\FollowUp::with(['recommendation.finding.opd', 'creator', 'approvals'])->get(),
            default => collect(),
        };
    }

    public function grafik(Request $request)
    {
        $tahun = $request->get('tahun', date('Y'));

        // Bulanan
        $bulanan = collect(range(1, 12))->map(function ($m) use ($tahun) {
            return Recommendation::whereYear('created_at', $tahun)->whereMonth('created_at', $m)->count();
        })->toArray();

        // Per kategori
        $perKategori = \App\Models\KategoriTemuan::withCount('findings')->get();

        // Per OPD
        $perOpd = Opd::withCount('findings')->orderByDesc('findings_count')->limit(15)->get();

        // Per Status
        $perStatus = [
            'selesai' => Recommendation::where('status', 'selesai')->count(),
            'diproses' => Recommendation::where('status', 'diproses')->count(),
            'baru' => Recommendation::where('status', 'baru')->count(),
            'revisi' => Recommendation::where('status', 'revisi')->count(),
        ];

        // Tahunan 5 tahun
        $tahunan = collect(range((int)$tahun - 4, (int)$tahun))->map(fn($t) => [
            'tahun' => $t,
            'audit' => Audit::whereHas('tahunAudit', fn($q) => $q->where('tahun', $t))->count(),
            'temuan' => Finding::whereHas('audit.tahunAudit', fn($q) => $q->where('tahun', $t))->count(),
        ]);

        $tahuns = TahunAuditService::years();

        return view('laporan.grafik', compact('bulanan', 'perKategori', 'perOpd', 'perStatus', 'tahunan', 'tahun', 'tahuns'));
    }
}
