@extends('layouts.app')
@section('title', 'Detail Temuan')
@section('page-title', 'Detail Temuan')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <div><h5 class="mb-0 fw-bold">#{{ $item->nomor }} - {{ $item->judul }}</h5><p class="text-muted small mb-0">{!! $item->status_badge !!} {!! $item->risiko_badge !!} Audit: <a href="{{ route('audit.show', $item->audit) }}">{{ $item->audit->nomor }}</a></p></div>
    <div class="d-flex gap-1">
        <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addRec"><i class="bi bi-plus-lg me-1"></i>Tambah Rekomendasi</button>
        <a href="{{ route('finding.edit', $item) }}" class="btn btn-warning btn-sm"><i class="bi bi-pencil"></i></a>
        <a href="{{ route('finding.index') }}" class="btn btn-outline-secondary btn-sm">Kembali</a>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-4">
        <div class="card mb-3"><div class="card-header bg-white"><h6 class="mb-0 fw-bold">Detail Temuan</h6></div><div class="card-body small">
            <dl class="row mb-0">
                <dt class="col-5 text-muted">OPD</dt><dd class="col-7">{{ $item->opd->nama }}</dd>
                <dt class="col-5 text-muted">Kategori</dt><dd class="col-7">{{ $item->kategoriTemuan->nama }}</dd>
                <dt class="col-5 text-muted">Nilai</dt><dd class="col-7 fw-bold text-danger">Rp {{ number_format($item->nilai) }}</dd>
                <dt class="col-5 text-muted">Risiko</dt><dd class="col-7">{!! $item->risiko_badge !!}</dd>
                <dt class="col-5 text-muted">Progress</dt><dd class="col-7"><div class="progress" style="height:18px"><div class="progress-bar bg-success" style="width:{{ $item->progress_rata }}%">{{ $item->progress_rata }}%</div></div></dd>
            </dl>
            @if($item->deskripsi)<hr><div class="small">{{ $item->deskripsi }}</div>@endif
        </div></div>

        <div class="card"><div class="card-header bg-white d-flex justify-content-between"><h6 class="mb-0 fw-bold">Dokumen Pendukung</h6><button class="btn btn-sm btn-link" data-bs-toggle="modal" data-bs-target="#addDoc"><i class="bi bi-upload"></i></button></div><div class="card-body p-2">
            @forelse($item->documents as $d)<div class="d-flex align-items-center py-1"><i class="bi {{ $d->icon }} text-primary me-2"></i><div class="flex-grow-1 small">{{ $d->nama_asli }}</div><a href="{{ route('documents.download', $d) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-download"></i></a></div>@empty<p class="text-muted small text-center mb-0">Belum ada dokumen</p>@endforelse
        </div></div>
    </div>
    <div class="col-lg-8">
        <div class="card"><div class="card-header bg-white"><h6 class="mb-0 fw-bold">Daftar Rekomendasi ({{ $item->recommendations->count() }})</h6></div><div class="card-body p-0">
            @forelse($item->recommendations as $r)
            <div class="border-bottom p-3">
                <div class="d-flex justify-content-between">
                    <div class="flex-grow-1">
                        <a href="{{ route('recommendation.show', $r) }}" class="text-decoration-none"><strong>Rekomendasi #{{ $r->nomor }}</strong></a> {!! $r->status_badge !!}
                        <p class="small mb-1 mt-1">{{ $r->isi }}</p>
                        <div class="small text-muted">
                            <i class="bi bi-calendar3 me-1"></i>Deadline: <span class="text-{{ $r->warna_status }}">{{ $r->deadline->format('d M Y') }}</span> ({{ $r->hari_tersisa >= 0 ? $r->hari_tersisa . ' hari lagi' : 'lewat ' . abs($r->hari_tersisa) . ' hari' }})
                            @if($r->pic) <span class="ms-2"><i class="bi bi-person me-1"></i>{{ $r->pic }}</span>@endif
                            <span class="ms-2"><i class="bi bi-arrow-repeat me-1"></i>{{ $r->followUps->count() }} tindak lanjut</span>
                        </div>
                    </div>
                    <div style="width:130px"><div class="progress" style="height:18px"><div class="progress-bar bg-{{ $r->persentase>=100?'success':($r->persentase>=50?'warning':'danger') }}" style="width:{{ $r->persentase }}%">{{ $r->persentase }}%</div></div></div>
                </div>
            </div>
            @empty<div class="text-center py-5 text-muted"><i class="bi bi-inbox fs-1 d-block"></i><p class="mt-2">Belum ada rekomendasi</p><button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addRec">Tambah Rekomendasi</button></div>@endforelse
        </div></div>
    </div>
</div>

{{-- Modal Tambah Rekomendasi --}}
<div class="modal fade" id="addRec"><div class="modal-dialog modal-lg"><div class="modal-content"><div class="modal-header"><h6 class="modal-title">Tambah Rekomendasi untuk Temuan #{{ $item->nomor }}</h6><button class="btn-close" data-bs-dismiss="modal"></button></div>
<form method="POST" action="{{ route('recommendation.store') }}">@csrf
    <div class="modal-body"><input type="hidden" name="finding_id" value="{{ $item->id }}">
        <div class="row g-3">
            <div class="col-md-3"><label class="form-label">Nomor <span class="text-danger">*</span></label><input type="text" name="nomor" value="{{ $item->recommendations->count() + 1 }}" class="form-control" required></div>
            <div class="col-md-5"><label class="form-label">PIC</label><input type="text" name="pic" class="form-control" placeholder="Penanggung Jawab"></div>
            <div class="col-md-4"><label class="form-label">Deadline <span class="text-danger">*</span></label><input type="date" name="deadline" class="form-control" required></div>
            <div class="col-12"><label class="form-label">Isi Rekomendasi <span class="text-danger">*</span></label><textarea name="isi" rows="4" class="form-control" required></textarea><small class="text-muted">Reminder otomatis akan dibuat untuk 30, 14, 7, 3, 1 hari sebelum deadline</small></div>
        </div>
    </div><div class="modal-footer"><button class="btn btn-primary"><i class="bi bi-save me-1"></i>Simpan & Generate Reminder</button></div>
</form></div></div></div>

{{-- Modal Upload Dokumen --}}
<div class="modal fade" id="addDoc"><div class="modal-dialog"><div class="modal-content"><div class="modal-header"><h6 class="modal-title">Upload Dokumen</h6><button class="btn-close" data-bs-dismiss="modal"></button></div>
<form method="POST" action="{{ route('finding.upload-document', $item) }}" enctype="multipart/form-data">@csrf
    <div class="modal-body">
        <div class="mb-3"><label class="form-label">Kategori</label><select name="kategori" class="form-select"><option value="surat">Surat</option><option value="bukti_foto">Bukti Foto</option><option value="laporan">Laporan</option><option value="ba">Berita Acara</option><option value="nota_dinas">Nota Dinas</option><option value="file_pendukung">File Pendukung</option></select></div>
        <div class="mb-3"><label class="form-label">File</label><input type="file" name="file" class="form-control" accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.mp4" required><small class="text-muted">Max 20MB</small></div>
    </div><div class="modal-footer"><button class="btn btn-primary">Upload</button></div>
</form></div></div></div>
@endsection
