<?php

namespace App\Http\Controllers;

use App\Models\Audit;
use App\Models\Finding;
use App\Models\KategoriTemuan;
use App\Models\Opd;
use App\Services\DocumentService;
use Illuminate\Http\Request;

class FindingController extends Controller
{
    public function index(Request $request)
    {
        $query = Finding::with(['audit', 'opd', 'kategoriTemuan', 'recommendations']);

        if ($s = $request->get('q')) {
            $query->where(fn($q) => $q->where('nomor', 'like', "%{$s}%")->orWhere('judul', 'like', "%{$s}%"));
        }
        if ($request->filled('audit_id')) $query->where('audit_id', $request->audit_id);
        if ($request->filled('opd_id')) $query->where('opd_id', $request->opd_id);
        if ($request->filled('status')) $query->where('status', $request->status);
        if ($request->filled('tingkat_risiko')) $query->where('tingkat_risiko', $request->tingkat_risiko);
        if ($request->filled('kategori_temuan_id')) $query->where('kategori_temuan_id', $request->kategori_temuan_id);

        $items = $query->latest()->paginate(10)->appends($request->query());

        return view('finding.index', compact('items'));
    }

    public function create(Request $request)
    {
        return view('finding.form', [
            'item' => new Finding(),
            'audits' => Audit::orderByDesc('id')->get(),
            'opds' => Opd::where('is_active', true)->orderBy('nama')->get(),
            'kategoris' => KategoriTemuan::all(),
            'audit_id' => $request->get('audit_id'),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'audit_id' => 'required|exists:audits,id',
            'nomor' => 'required|string|max:100',
            'judul' => 'required|string|max:255',
            'opd_id' => 'required|exists:opd,id',
            'nilai' => 'required|numeric|min:0',
            'kategori_temuan_id' => 'required|exists:kategori_temuan,id',
            'tingkat_risiko' => 'required|in:rendah,sedang,tinggi,sangat_tinggi',
            'deskripsi' => 'nullable|string',
            'status' => 'required|in:baru,diproses,selesai,ditolak,revisi',
        ]);
        $data['created_by'] = auth()->id();
        $data['nilai'] = str_replace(',', '', $data['nilai']);

        $finding = Finding::create($data);

        activity()->performedOn($finding)->log('Menambah temuan baru');

        return redirect()->route('finding.show', $finding)->with('success', 'Temuan berhasil ditambahkan.');
    }

    public function show(Finding $finding)
    {
        $finding->load(['audit', 'opd', 'kategoriTemuan', 'recommendations.followUps', 'creator']);
        return view('finding.show', ['item' => $finding]);
    }

    public function edit(Finding $finding)
    {
        return view('finding.form', [
            'item' => $finding,
            'audits' => Audit::orderByDesc('id')->get(),
            'opds' => Opd::where('is_active', true)->orderBy('nama')->get(),
            'kategoris' => KategoriTemuan::all(),
            'audit_id' => null,
        ]);
    }

    public function update(Request $request, Finding $finding)
    {
        $data = $request->validate([
            'audit_id' => 'required|exists:audits,id',
            'nomor' => 'required|string|max:100',
            'judul' => 'required|string|max:255',
            'opd_id' => 'required|exists:opd,id',
            'nilai' => 'required|numeric|min:0',
            'kategori_temuan_id' => 'required|exists:kategori_temuan,id',
            'tingkat_risiko' => 'required|in:rendah,sedang,tinggi,sangat_tinggi',
            'deskripsi' => 'nullable|string',
            'status' => 'required|in:baru,diproses,selesai,ditolak,revisi',
        ]);
        $data['nilai'] = str_replace(',', '', $data['nilai']);
        $finding->update($data);

        return redirect()->route('finding.show', $finding)->with('success', 'Temuan diperbarui.');
    }

    public function destroy(Finding $finding)
    {
        $finding->delete();
        return redirect()->route('finding.index')->with('success', 'Temuan dihapus.');
    }

    public function uploadDocument(Request $request, Finding $finding)
    {
        $request->validate([
            'kategori' => 'required|in:surat,bukti_foto,laporan,ba,nota_dinas,file_pendukung',
            'file' => 'required|file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png,mp4,mov,avi|max:20480',
        ]);
        DocumentService::upload($request->file('file'), $finding, $request->kategori);
        return back()->with('success', 'Dokumen berhasil diunggah.');
    }
}
