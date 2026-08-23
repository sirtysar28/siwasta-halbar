<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Auditor;
use Illuminate\Http\Request;

class AuditorController extends Controller
{
    public function index(Request $request)
    {
        $query = Auditor::query();
        if ($s = $request->get('q')) {
            $query->where(fn($q) => $q->where('nama', 'like', "%{$s}%")
                ->orWhere('instansi', 'like', "%{$s}%")->orWhere('email', 'like', "%{$s}%"));
        }
        return view('master.auditor.index', ['items' => $query->latest()->paginate(10)->appends($request->query())]);
    }

    public function create()
    {
        return view('master.auditor.form', ['item' => new Auditor()]);
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);
        Auditor::create($data);
        return redirect()->route('master.auditor.index')->with('success', 'Auditor berhasil ditambahkan.');
    }

    public function show(Auditor $auditor)
    {
        $auditor->load('audits');
        return view('master.auditor.show', ['item' => $auditor]);
    }

    public function edit(Auditor $auditor)
    {
        return view('master.auditor.form', ['item' => $auditor]);
    }

    public function update(Request $request, Auditor $auditor)
    {
        $data = $this->validateData($request);
        $auditor->update($data);
        return redirect()->route('master.auditor.index')->with('success', 'Auditor berhasil diperbarui.');
    }

    public function destroy(Auditor $auditor)
    {
        $auditor->delete();
        return redirect()->route('master.auditor.index')->with('success', 'Auditor berhasil dihapus.');
    }

    private function validateData(Request $request): array
    {
        $data = $request->validate([
            'nama' => 'required|string|max:255',
            'nip' => 'nullable|string|max:50',
            'jabatan' => 'nullable|string|max:255',
            'instansi' => 'nullable|string|max:255',
            'email' => 'nullable|email',
            'telepon' => 'nullable|string|max:30',
            'is_active' => 'boolean',
        ]);
        $data['is_active'] = $request->boolean('is_active', true);
        return $data;
    }
}
