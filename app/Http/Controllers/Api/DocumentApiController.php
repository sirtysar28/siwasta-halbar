<?php

namespace App\Http\Controllers\Api;

use App\Models\Document;
use Illuminate\Http\Request;

class DocumentApiController extends BaseApiController
{
    public function index(Request $request)
    {
        $query = Document::with('uploader')->latest();
        if ($request->filled('kategori')) $query->where('kategori', $request->kategori);
        return $this->success($query->paginate(20));
    }

    public function download(Document $document)
    {
        $path = storage_path('app/public/' . $document->path);
        if (!file_exists($path)) return $this->error('File tidak ditemukan', 404);
        return response()->download($path, $document->nama_asli);
    }
}
