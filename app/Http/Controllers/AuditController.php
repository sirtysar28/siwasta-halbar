<?php

namespace App\Http\Controllers;

use App\Models\Audit;
use App\Models\Auditor;
use App\Models\Document;
use App\Models\JenisAudit;
use App\Models\Opd;
use App\Models\TahunAudit;
use App\Services\TahunAuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AuditController extends Controller
{
    public function index(Request $request)
    {
        $query = Audit::with(['jenisAudit', 'tahunAudit', 'auditor', 'opds', 'creator']);

        if ($s = $request->get('q')) {
            $query->where(fn($q) => $q->where('nomor', 'like', "%{$s}%")
                ->orWhere('judul', 'like', "%{$s}%")->orWhere('instansi', 'like', "%{$s}%"));
        }
        if ($request->filled('jenis_audit_id')) $query->where('jenis_audit_id', $request->jenis_audit_id);
        if ($request->filled('tahun_audit_id')) $query->where('tahun_audit_id', $request->tahun_audit_id);
        if ($request->filled('status')) $query->where('status', $request->status);

        $items = $query->latest()->paginate(10)->appends($request->query());

        return view('audit.index', compact('items'));
    }

    public function create()
    {
        $jenis = JenisAudit::where('is_active', true)->get();
        $tahuns = TahunAudit::orderByDesc('tahun')->get();
        $auditors = Auditor::where('is_active', true)->get();
        $opds = Opd::where('is_active', true)->orderBy('nama')->get();

        return view('audit.form', [
            'item' => new Audit(),
            'jenis' => $jenis,
            'tahuns' => $tahuns,
            'auditors' => $auditors,
            'opds' => $opds,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nomor' => 'required|unique:audits,nomor',
            'judul' => 'required|string|max:255',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
            'jenis_audit_id' => 'required|exists:jenis_audit,id',
            'tahun_audit_id' => 'required|exists:tahun_audit,id',
            'auditor_id' => 'required|exists:auditors,id',
            'instansi' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
            'status' => 'required|in:draft,berjalan,selesai,ditutup',
            'opds' => 'array',
            'opds.*' => 'exists:opd,id',
            'documents.*' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png|max:10240',
        ]);

        $data['created_by'] = auth()->id();
        $opds = $request->input('opds', []);
        $audit = Audit::create($data);

        if (!empty($opds)) $audit->opds()->sync($opds);

        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $file) {
                $this->uploadDocument($file, $audit);
            }
        }

        return redirect()->route('audit.show', $audit)->with('success', 'Audit berhasil dibuat.');
    }

    public function show(Audit $audit)
    {
        $audit->load(['jenisAudit', 'tahunAudit', 'auditor', 'opds', 'findings.recommendations', 'documents.uploader', 'creator']);
        return view('audit.show', ['item' => $audit]);
    }

    public function edit(Audit $audit)
    {
        $audit->load('opds');
        $jenis = JenisAudit::where('is_active', true)->get();
        $tahuns = TahunAudit::orderByDesc('tahun')->get();
        $auditors = Auditor::where('is_active', true)->get();
        $opds = Opd::where('is_active', true)->orderBy('nama')->get();

        return view('audit.form', compact('audit', 'jenis', 'tahuns', 'auditors', 'opds') + ['item' => $audit]);
    }

    public function update(Request $request, Audit $audit)
    {
        $data = $request->validate([
            'nomor' => 'required|unique:audits,nomor,' . $audit->id,
            'judul' => 'required|string|max:255',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
            'jenis_audit_id' => 'required|exists:jenis_audit,id',
            'tahun_audit_id' => 'required|exists:tahun_audit,id',
            'auditor_id' => 'required|exists:auditors,id',
            'instansi' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
            'status' => 'required|in:draft,berjalan,selesai,ditutup',
            'opds' => 'array',
            'opds.*' => 'exists:opd,id',
            'documents.*' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png|max:10240',
        ]);

        $opds = $request->input('opds', []);
        $audit->update($data);
        $audit->opds()->sync($opds);

        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $file) {
                $this->uploadDocument($file, $audit);
            }
        }

        return redirect()->route('audit.show', $audit)->with('success', 'Audit berhasil diperbarui.');
    }

    public function destroy(Audit $audit)
    {
        $audit->delete();
        return redirect()->route('audit.index')->with('success', 'Audit berhasil dihapus.');
    }

    private function uploadDocument($file, $audit): void
    {
        $namaFile = Str::random(20) . '_' . time() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('documents/audit/' . $audit->id, $namaFile, 'public');

        Document::create([
            'documentable_id' => $audit->id,
            'documentable_type' => Audit::class,
            'kategori' => 'surat',
            'nama_asli' => $file->getClientOriginalName(),
            'nama_file' => $namaFile,
            'path' => $path,
            'extensi' => $file->getClientOriginalExtension(),
            'ukuran' => $file->getSize(),
            'qr_code_token' => Str::uuid(),
            'uploaded_by' => auth()->id(),
        ]);
    }
}
