@extends('layouts.app')
@section('title', 'Detail Rekomendasi')
@section('page-title', 'Detail Rekomendasi')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <div><h5 class="mb-0 fw-bold">Rekomendasi #{{ $item->nomor }}</h5><p class="text-muted small mb-0">{!! $item->status_badge !!} Temuan: <a href="{{ route('finding.show', $item->finding) }}">#{{ $item->finding->nomor }} {{ Str::limit($item->finding->judul, 40) }}</a></p></div>
    <div class="d-flex gap-1">
        @if($item->status != 'selesai')<button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addTL"><i class="bi bi-plus-lg me-1"></i>Tambah Tindak Lanjut</button>@endif
        <a href="{{ route('finding.show', $item->finding) }}" class="btn btn-outline-secondary btn-sm">Kembali</a>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-4">
        <div class="card mb-3"><div class="card-header bg-white"><h6 class="mb-0 fw-bold">Informasi Rekomendasi</h6></div><div class="card-body">
            <p class="mb-3">{{ $item->isi }}</p>
            <hr>
            <dl class="row small mb-0">
                <dt class="col-5 text-muted">OPD</dt><dd class="col-7">{{ $item->finding->opd->nama }}</dd>
                <dt class="col-5 text-muted">PIC</dt><dd class="col-7">{{ $item->pic ?? '-' }}</dd>
                <dt class="col-5 text-muted">Deadline</dt><dd class="col-7"><span class="badge bg-{{ $item->warna_status }}">{{ $item->deadline->format('d M Y') }}</span><br><small class="text-{{ $item->warna_status }}">{{ $item->hari_tersisa >= 0 ? $item->hari_tersisa . ' hari lagi' : 'LEWAT ' . abs($item->hari_tersisa) . ' hari' }}</small></dd>
                <dt class="col-5 text-muted">Progress</dt><dd class="col-7"><div class="progress" style="height:20px"><div class="progress-bar bg-success" style="width:{{ $item->persentase }}%">{{ $item->persentase }}%</div></div></dd>
            </dl>
        </div></div>

        @if($item->reminders->isNotEmpty())
        <div class="card"><div class="card-header bg-white"><h6 class="mb-0 fw-bold">Schedule Reminder</h6></div><div class="card-body p-2 small">
            @foreach($item->reminders->sortBy('tanggal_kirim') as $rm)
            <div class="d-flex justify-content-between py-1 border-bottom">
                <span>@if($rm->hari_sebelum==0) Hari H @else {{ $rm->hari_sebelum }} hari @endif sebelum</span>
                <span class="text-muted">{{ $rm->tanggal_kirim->format('d M Y') }} @if($rm->is_sent)<i class="bi bi-check-circle text-success"></i>@endif</span>
            </div>
            @endforeach
        </div></div>
        @endif
    </div>
    <div class="col-lg-8">
        <div class="card"><div class="card-header bg-white"><h6 class="mb-0 fw-bold">Riwayat Tindak Lanjut ({{ $item->followUps->count() }})</h6></div><div class="card-body">
            @forelse($item->followUps as $fu)
            <div class="card mb-3 border-start border-4 border-{{ $fu->status=='disetujui'?'success':($fu->status=='revisi'?'warning':($fu->status=='ditolak'?'danger':'secondary')) }}">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div><span class="badge bg-light text-dark">{{ $fu->tanggal->format('d M Y') }}</span> {!! $fu->status_badge !!} <span class="badge bg-primary">{{ $fu->persentase }}%</span></div>
                        <small class="text-muted">oleh {{ $fu->creator->name }}</small>
                    </div>
                    <p class="mt-2 mb-2">{{ $fu->uraian }}</p>
                    @if($fu->catatan)<div class="alert alert-light small mb-2 py-1"><i class="bi bi-sticky me-1"></i>{{ $fu->catatan }}</div>@endif
                    @if($fu->documents->isNotEmpty())<div class="mb-2">@foreach($fu->documents as $d)<a href="{{ route('documents.download', $d) }}" class="badge bg-light text-dark text-decoration-none me-1"><i class="bi {{ $d->icon }}"></i> {{ $d->nama_asli }}</a>@endforeach</div>@endif
                    @if($fu->approvals->isNotEmpty())<div class="small text-muted"><i class="bi bi-check2-circle me-1"></i>Diverifikasi oleh {{ $fu->approvals->first()->verifikator->name }}: {{ $fu->approvals->first()->catatan }}</div>@endif
                    <div class="text-end"><a href="{{ route('follow-up.show', $fu) }}" class="btn btn-sm btn-outline-secondary">Detail</a></div>
                </div>
            </div>
            @empty<div class="text-center py-4 text-muted"><i class="bi bi-inbox fs-1 d-block"></i><p class="mt-2">Belum ada tindak lanjut</p></div>@endforelse
        </div></div>
    </div>
</div>

{{-- Modal Tambah Tindak Lanjut --}}
<div class="modal fade" id="addTL"><div class="modal-dialog modal-lg"><div class="modal-content"><div class="modal-header"><h6 class="modal-title">Tambah Tindak Lanjut</h6><button class="btn-close" data-bs-dismiss="modal"></button></div>
<form method="POST" action="{{ route('follow-up.store') }}" enctype="multipart/form-data">@csrf
    <div class="modal-body"><input type="hidden" name="recommendation_id" value="{{ $item->id }}">
        <div class="row g-3">
            <div class="col-md-4"><label class="form-label">Tanggal <span class="text-danger">*</span></label><input type="date" name="tanggal" value="{{ date('Y-m-d') }}" class="form-control" required></div>
            <div class="col-md-8"><label class="form-label">Persentase Progress <span class="text-danger">*</span></label><input type="range" name="persentase" min="0" max="100" step="25" value="{{ $item->persentase }}" class="form-range" oninput="this.nextElementSibling.textContent=this.value+'%'" id="rangeTL"><div class="text-center fw-bold">{{ $item->persentase }}%</div></div>
            <div class="col-12"><label class="form-label">Uraian Tindak Lanjut <span class="text-danger">*</span></label><textarea name="uraian" rows="4" class="form-control" required placeholder="Jelaskan tindak lanjut yang telah dilakukan..."></textarea></div>
            <div class="col-12"><label class="form-label">Catatan</label><textarea name="catatan" rows="2" class="form-control"></textarea></div>
            <div class="col-12"><label class="form-label">Upload Bukti (PDF, Foto, Video, Dokumen)</label><input type="file" name="documents[]" class="form-control" multiple accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.mp4,.mov"></div>
        </div>
    </div><div class="modal-footer"><button class="btn btn-primary"><i class="bi bi-send me-1"></i>Kirim untuk Verifikasi</button></div>
</form></div></div></div>

@push('scripts')<script>document.getElementById('rangeTL').addEventListener('input', e => e.nextElementSibling.textContent = e.target.value + '%');</script>@endpush
@endsection
