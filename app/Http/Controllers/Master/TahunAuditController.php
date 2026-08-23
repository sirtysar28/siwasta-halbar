<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\TahunAudit;
use Illuminate\Http\Request;

class TahunAuditController extends Controller
{
    public function index()
    {
        return view('master.tahun-audit.index', ['items' => TahunAudit::latest('tahun')->paginate(10)]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'tahun' => 'required|integer|unique:tahun_audit,tahun',
            'keterangan' => 'nullable|string',
            'is_active' => 'boolean',
        ]);
        $data['is_active'] = $request->boolean('is_active', true);
        TahunAudit::create($data);
        return back()->with('success', 'Tahun Audit ditambahkan.');
    }

    public function update(Request $request, TahunAudit $tahunAudit)
    {
        $data = $request->validate([
            'tahun' => 'required|integer|unique:tahun_audit,tahun,' . $tahunAudit->id,
            'keterangan' => 'nullable|string',
            'is_active' => 'boolean',
        ]);
        $data['is_active'] = $request->boolean('is_active');
        $tahunAudit->update($data);
        return back()->with('success', 'Tahun Audit diperbarui.');
    }

    public function destroy(TahunAudit $tahunAudit)
    {
        $tahunAudit->delete();
        return back()->with('success', 'Tahun Audit dihapus.');
    }
}
