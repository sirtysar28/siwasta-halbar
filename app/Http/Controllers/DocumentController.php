<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Services\DocumentService;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    public function index(Request $request)
    {
        $query = Document::with('uploader');

        if ($s = $request->get('q')) {
            $query->where(fn($q) => $q->where('nama_asli', 'like', "%{$s}%"));
        }
        if ($request->filled('kategori')) $query->where('kategori', $request->kategori);
        if ($request->filled('extensi')) $query->where('extensi', $request->extensi);

        $items = $query->latest()->paginate(10)->appends($request->query());

        return view('documents.index', compact('items'));
    }

    public function download(Document $document)
    {
        return DocumentService::download($document);
    }

    public function verifyQr($token)
    {
        $document = Document::where('qr_code_token', $token)->firstOrFail();
        $document->load('documentable', 'uploader');
        return view('documents.verify', compact('document'));
    }

    public function destroy(Document $document)
    {
        $path = storage_path('app/public/' . $document->path);
        if (file_exists($path)) @unlink($path);
        $document->delete();
        return back()->with('success', 'Dokumen berhasil dihapus.');
    }
}
