<?php

namespace App\Services;

use App\Models\Document;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class DocumentService
{
    public static function upload(UploadedFile $file, Model $model, string $kategori = 'file_pendukung', string $folder = 'documents'): Document
    {
        $namaFile = Str::random(20) . '_' . time() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs($folder . '/' . class_basename($model) . '/' . $model->id, $namaFile, 'public');

        // Hitung versi (versioning)
        $versi = Document::where('documentable_type', get_class($model))
            ->where('documentable_id', $model->id)
            ->where('nama_asli', $file->getClientOriginalName())
            ->max('versi') + 1;

        return Document::create([
            'documentable_id' => $model->id,
            'documentable_type' => get_class($model),
            'kategori' => $kategori,
            'nama_asli' => $file->getClientOriginalName(),
            'nama_file' => $namaFile,
            'path' => $path,
            'extensi' => strtolower($file->getClientOriginalExtension()),
            'ukuran' => $file->getSize(),
            'versi' => $versi,
            'qr_code_token' => Str::uuid(),
            'uploaded_by' => auth()->id(),
        ]);
    }

    public static function download(Document $document)
    {
        $path = storage_path('app/public/' . $document->path);
        if (!file_exists($path)) abort(404, 'File tidak ditemukan');

        activity()->log('Download dokumen: ' . $document->nama_asli);

        return response()->download($path, $document->nama_asli);
    }
}
