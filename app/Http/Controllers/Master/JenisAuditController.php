<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\JenisAudit;
use Illuminate\Http\Request;

class JenisAuditController extends Controller
{
    public function index(Request $request)
    {
        $query = JenisAudit::query();
        if ($s = $request->get('q')) {
            $query->where(fn($q) => $q->where('nama', 'like', "%{$s}%")->orWhere('kode', 'like', "%{$s}%"));
        }
        return view('master.jenis-audit.index', ['items' => $query->latest()->paginate(10)->appends($request->query())]);
    }

    public function create()
    {
        return view('master.jenis-audit.form', ['item' => new JenisAudit()]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama' => 'required|string|max:255',
            'kode' => 'required|unique:jenis_audit,kode',
            'keterangan' => 'nullable|string',
            'is_active' => 'boolean',
        ]);
        $data['is_active'] = $request->boolean('is_active', true);
        JenisAudit::create($data);
        return redirect()->route('master.jenis-audit.index')->with('success', 'Jenis Audit ditambahkan.');
    }

    public function edit(JenisAudit $jenisAudit)
    {
        return view('master.jenis-audit.form', ['item' => $jenisAudit]);
    }

    public function update(Request $request, JenisAudit $jenisAudit)
    {
        $data = $request->validate([
            'nama' => 'required|string|max:255',
            'kode' => 'required|unique:jenis_audit,kode,' . $jenisAudit->id,
            'keterangan' => 'nullable|string',
            'is_active' => 'boolean',
        ]);
        $data['is_active'] = $request->boolean('is_active', true);
        $jenisAudit->update($data);
        return redirect()->route('master.jenis-audit.index')->with('success', 'Jenis Audit diperbarui.');
    }

    public function destroy(JenisAudit $jenisAudit)
    {
        $jenisAudit->delete();
        return redirect()->route('master.jenis-audit.index')->with('success', 'Jenis Audit dihapus.');
    }
}
