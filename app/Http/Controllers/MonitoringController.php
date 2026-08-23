<?php

namespace App\Http\Controllers;

use App\Exports\MonitoringExport;
use App\Models\Recommendation;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class MonitoringController extends Controller
{
    public function index(Request $request)
    {
        $query = Recommendation::with(['finding.audit.jenisAudit', 'finding.opd', 'finding.kategoriTemuan', 'followUps']);

        // Filters
        if ($request->filled('tahun')) {
            $query->whereHas('finding.audit', fn($q) => $q->whereHas('tahunAudit', fn($q2) => $q2->where('tahun', $request->tahun)));
        }
        if ($request->filled('opd_id')) $query->whereHas('finding', fn($q) => $q->where('opd_id', $request->opd_id));
        if ($request->filled('status')) $query->where('status', $request->status);
        if ($request->filled('tingkat_risiko')) $query->whereHas('finding', fn($q) => $q->where('tingkat_risiko', $request->tingkat_risiko));
        if ($request->filled('deadline_status')) {
            $ds = $request->deadline_status;
            if ($ds === 'lewat') {
                $query->where('deadline', '<', now())->where('status', '!=', 'selesai');
            } elseif ($ds === 'minggu_ini') {
                $query->whereBetween('deadline', [now(), now()->addDays(7)])->where('status', '!=', 'selesai');
            } elseif ($ds === 'bulan_ini') {
                $query->whereBetween('deadline', [now(), now()->addDays(30)])->where('status', '!=', 'selesai');
            }
        }

        $items = $query->latest()->paginate(10)->appends($request->query());

        $opds = \App\Models\Opd::where('is_active', true)->orderBy('nama')->get();
        $tahuns = \App\Models\TahunAudit::orderByDesc('tahun')->get();

        return view('monitoring.index', compact('items', 'opds', 'tahuns'));
    }

    public function export(Request $request, $format)
    {
        $fileName = 'monitoring-tindak-lanjut-' . date('Y-m-d');
        $items = $this->getQuery($request)->get();

        return match ($format) {
            'excel' => Excel::download(new MonitoringExport($items), $fileName . '.xlsx'),
            'csv' => Excel::download(new MonitoringExport($items), $fileName . '.csv', \Maatwebsite\Excel\Excel::CSV),
            'pdf' => \PDF::loadView('monitoring.pdf', ['items' => $items])->setPaper('a4', 'landscape')->download($fileName . '.pdf'),
            default => back()->with('error', 'Format tidak didukung'),
        };
    }

    private function getQuery(Request $request)
    {
        $query = Recommendation::with(['finding.audit.jenisAudit', 'finding.opd', 'finding.kategoriTemuan', 'followUps']);
        if ($request->filled('tahun')) {
            $query->whereHas('finding.audit.tahunAudit', fn($q) => $q->where('tahun', $request->tahun));
        }
        if ($request->filled('opd_id')) $query->whereHas('finding', fn($q) => $q->where('opd_id', $request->opd_id));
        if ($request->filled('status')) $query->where('status', $request->status);
        return $query;
    }
}
