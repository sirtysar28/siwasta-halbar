<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\KategoriTemuan;
use Illuminate\Http\Request;

class KategoriTemuanController extends Controller
{
    public function index()
    {
        return view('master.kategori-temuan.index', ['items' => KategoriTemuan::latest()->paginate(10)]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama' => 'required|string|max:255',
            'kode' => 'required|unique:kategori_temuan,kode',
            'keterangan' => 'nullable|string',
        ]);
        KategoriTemuan::create($data);
        return back()->with('success', 'Kategori Temuan ditambahkan.');
    }

    public function update(Request $request, KategoriTemuan $kategoriTemuan)
    {
        $data = $request->validate([
            'nama' => 'required|string|max:255',
            'kode' => 'required|unique:kategori_temuan,kode,' . $kategoriTemuan->id,
            'keterangan' => 'nullable|string',
        ]);
        $kategoriTemuan->update($data);
        return back()->with('success', 'Kategori Temuan diperbarui.');
    }

    public function destroy(KategoriTemuan $kategoriTemuan)
    {
        $kategoriTemuan->delete();
        return back()->with('success', 'Kategori Temuan dihapus.');
    }
}
