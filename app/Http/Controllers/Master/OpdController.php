<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Opd;
use Illuminate\Http\Request;

class OpdController extends Controller
{
    public function index(Request $request)
    {
        $query = Opd::query();

        if ($s = $request->get('q')) {
            $query->where(function ($q) use ($s) {
                $q->where('nama', 'like', "%{$s}%")
                  ->orWhere('kode', 'like', "%{$s}%")
                  ->orWhere('kepala', 'like', "%{$s}%");
            });
        }
        if ($request->filled('status')) {
            $query->where('is_active', $request->boolean('status'));
        }

        $items = $query->latest()->paginate(10)->appends($request->query());

        return view('master.opd.index', compact('items'));
    }

    public function create()
    {
        return view('master.opd.form', ['item' => new Opd()]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'kode' => 'required|unique:opd,kode',
            'nama' => 'required|string|max:255',
            'kepala' => 'nullable|string|max:255',
            'alamat' => 'nullable|string',
            'email' => 'nullable|email',
            'telepon' => 'nullable|string|max:30',
            'is_active' => 'boolean',
        ]);
        $data['is_active'] = $request->boolean('is_active', true);

        Opd::create($data);
        return redirect()->route('master.opd.index')->with('success', 'OPD berhasil ditambahkan.');
    }

    public function show(Opd $opd)
    {
        $opd->load('findings.recommendations', 'users');
        return view('master.opd.show', ['item' => $opd]);
    }

    public function edit(Opd $opd)
    {
        return view('master.opd.form', ['item' => $opd]);
    }

    public function update(Request $request, Opd $opd)
    {
        $data = $request->validate([
            'kode' => 'required|unique:opd,kode,' . $opd->id,
            'nama' => 'required|string|max:255',
            'kepala' => 'nullable|string|max:255',
            'alamat' => 'nullable|string',
            'email' => 'nullable|email',
            'telepon' => 'nullable|string|max:30',
            'is_active' => 'boolean',
        ]);
        $data['is_active'] = $request->boolean('is_active', true);

        $opd->update($data);
        return redirect()->route('master.opd.index')->with('success', 'OPD berhasil diperbarui.');
    }

    public function destroy(Opd $opd)
    {
        $opd->delete();
        return redirect()->route('master.opd.index')->with('success', 'OPD berhasil dihapus.');
    }
}
