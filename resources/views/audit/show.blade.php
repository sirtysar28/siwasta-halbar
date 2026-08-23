@extends('layouts.app')
@section('title', 'Detail Audit')
@section('page-title', 'Detail Audit')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <div><h5 class="mb-0 fw-bold">{{ $item->nomor }} - {{ $item->judul }}</h5><p class="text-muted small mb-0">{!! $item->status_badge !!} Dibuat oleh {{ $item->creator?->name }}</p></div>
    <div class="d-flex gap-1">
        <a href="{{ route('finding.create', ['audit_id' => $item->id]) }}" class="btn btn-success btn-sm"><i class="bi bi-plus-lg me-1"></i>Tambah Temuan</a>
        <a href="{{ route('audit.edit', $item) }}" class="btn btn-warning btn-sm"><i class="bi bi-pencil"></i></a>
        <a href="{{ route('audit.index') }}" class="btn btn-outline-secondary btn-sm">Kembali</a>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-4">
        <div class="card mb-3"><div class="card-header bg-white"><h6 class="mb-0 fw-bold">Informasi Audit</h6></div><div class="card-body small">
            <dl class="row mb-0">
                <dt class="col-5 text-muted">Nomor</dt><dd class="col-7">{{ $item->nomor }}</dd>
                <dt class="col-5 text-muted">Jenis Audit</dt><dd class="col-7"><span class="badge bg-primary">{{ $item->jenisAudit->nama }}</span></dd>
                <dt class="col-5 text-muted">Tahun</dt><dd class="col-7">{{ $item->tahunAudit->tahun }}</dd>
                <dt class="col-5 text-muted">Auditor</dt><dd class="col-7">{{ $item->auditor->nama }}</dd>
                <dt class="col-5 text-muted">Instansi</dt><dd class="col-7">{{ $item->instansi }}</dd>
                <dt class="col-5 text-muted">Mulai</dt><dd class="col-7">{{ $item->tanggal_mulai?->format('d M Y') }}</dd>
                <dt class="col-5 text-muted">Selesai</dt><dd class="col-7">{{ $item->tanggal_selesai?->format('d M Y') ?: '-' }}</dd>
                <dt class="col-5 text-muted">OPD</dt><dd class="col-7">{{ $item->opds->pluck('nama')->implode(', ') ?: '-' }}</dd>
            </dl>
            @if($item->keterangan)<hr class="my-2"><p class="small mb-0">{{ $item->keterangan }}</p>@endif
        </div></div>
        <div class="card"><div class="card-header bg-white"><h6 class="mb-0 fw-bold">Dokumen ({{ $item->documents->count() }})</h6></div><div class="card-body p-2">
            @forelse($item->documents as $d)
            <div class="d-flex align-items-center py-1">
                <i class="bi {{ $d->icon }} text-primary me-2"></i>
                <div class="flex-grow-1 small">{{ $d->nama_asli }}</div>
                <a href="{{ route('documents.download', $d) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-download"></i></a>
            </div>
            @empty<p class="text-muted small text-center mb-0">Belum ada dokumen</p>@endforelse
        </div></div>
    </div>
    <div class="col-lg-8">
        <div class="row g-2 mb-3">
            <div class="col-md-3"><div class="card text-center"><div class="card-body"><h4 class="fw-bold text-primary mb-0">{{ $item->findings->count() }}</h4><small class="text-muted">Total Temuan</small></div></div></div>
            <div class="col-md-3"><div class="card text-center"><div class="card-body"><h4 class="fw-bold text-success mb-0">{{ $item->findings->flatMap->recommendations->where('status','selesai')->count() }}</h4><small class="text-muted">Rekomendasi Selesai</small></div></div></div>
            <div class="col-md-3"><div class="card text-center"><div class="card-body"><h4 class="fw-bold text-warning mb-0">{{ $item->findings->flatMap->recommendations->whereNotIn('status',['selesai'])->count() }}</h4><small class="text-muted">Dalam Proses</small></div></div></div>
            <div class="col-md-3"><div class="card text-center"><div class="card-body"><h4 class="fw-bold text-danger mb-0">Rp {{ number_format($item->findings->sum('nilai')/1000000, 1) }}jt</h4><small class="text-muted">Total Nilai Temuan</small></div></div></div>
        </div>
        <div class="card"><div class="card-header bg-white d-flex justify-content-between"><h6 class="mb-0 fw-bold">Daftar Temuan</h6></div><div class="card-body p-0">
            @forelse($item->findings as $f)
            <div class="border-bottom p-3">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="flex-grow-1">
                        <a href="{{ route('finding.show', $f) }}" class="text-decoration-none"><strong class="text-dark">#{{ $f->nomor }} - {{ $f->judul }}</strong></a>
                        <div class="mt-1 small">
                            <span class="me-3"><i class="bi bi-building me-1"></i>{{ $f->opd->nama }}</span>
                            <span class="me-3"><i class="bi bi-cash-stack me-1"></i>Rp {{ number_format($f->nilai) }}</span>
                            <span class="me-3">{!! $f->risiko_badge !!}</span>
                            <span class="me-3">{!! $f->status_badge !!}</span>
                        </div>
                    </div>
                    <div class="text-end"><small class="text-muted">{{ $f->recommendations->count() }} rekomendasi</small><div class="mt-1" style="width:120px"><div class="progress" style="height:18px"><div class="progress-bar bg-{{ $f->progress_rata>=100?'success':($f->progress_rata>=50?'warning':'danger') }}" style="width:{{ $f->progress_rata }}%">{{ $f->progress_rata }}%</div></div></div></div>
                </div>
            </div>
            @empty<div class="text-center py-5 text-muted"><i class="bi bi-inbox fs-1 d-block"></i><p class="mt-2">Belum ada temuan. <a href="{{ route('finding.create', ['audit_id' => $item->id]) }}">Tambah temuan</a></p></div>@endforelse
        </div></div>
    </div>
</div>
@endsection
