@extends('layouts.app')
@section('title', 'Detail Tindak Lanjut')
@section('page-title', 'Detail Tindak Lanjut')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div><h5 class="mb-0 fw-bold">Tindak Lanjut {{ $item->tanggal->format('d M Y') }}</h5><p class="text-muted small mb-0">{!! $item->status_badge !!} oleh {{ $item->creator->name }}</p></div>
    <a href="{{ route('recommendation.show', $item->recommendation) }}" class="btn btn-outline-secondary btn-sm">Kembali ke Rekomendasi</a>
</div>

<div class="row g-3">
    <div class="col-lg-8">
        <div class="card mb-3"><div class="card-header bg-white"><h6 class="mb-0 fw-bold">Uraian Tindak Lanjut</h6></div><div class="card-body">
            <p>{{ $item->uraian }}</p>
            <div class="row small">
                <div class="col-md-6"><i class="bi bi-calendar3 me-1 text-muted"></i>Tanggal: <strong>{{ $item->tanggal->format('d M Y') }}</strong></div>
                <div class="col-md-6"><i class="bi bi-percent me-1 text-muted"></i>Persentase: <strong>{{ $item->persentase }}%</strong></div>
            </div>
            @if($item->catatan)<hr><div class="alert alert-light mb-0"><strong>Catatan:</strong> {{ $item->catatan }}</div>@endif
        </div></div>

        @if($item->documents->isNotEmpty())
        <div class="card mb-3"><div class="card-header bg-white"><h6 class="mb-0 fw-bold">Bukti Dokumen ({{ $item->documents->count() }})</h6></div><div class="card-body">
            <div class="row g-2">@foreach($item->documents as $d)
                <div class="col-md-4">
                    <div class="card h-100"><div class="card-body text-center">
                        <i class="bi {{ $d->icon }} fs-1 text-primary"></i>
                        <div class="small fw-semibold mt-2">{{ $d->nama_asli }}</div>
                        <small class="text-muted">{{ $d->ukuran_human }}</small><br>
                        @if(in_array($d->extensi, ['jpg','jpeg','png','gif','webp']))<a href="{{ $d->url }}" target="_blank" class="btn btn-sm btn-light mt-1">Preview</a>@endif
                        <a href="{{ route('documents.download', $d) }}" class="btn btn-sm btn-outline-primary mt-1"><i class="bi bi-download"></i></a>
                    </div></div>
                </div>
            @endforeach</div>
        </div></div>
        @endif

        {{-- Verifikasi Form --}}
        @if($item->status === 'pending' && auth()->user()->hasRole(['Verifikator','Inspektorat','Administrator','Super Administrator']))
        <div class="card border-success" id="verify"><div class="card-header bg-success text-white"><h6 class="mb-0 fw-bold"><i class="bi bi-check2-circle me-2"></i>Verifikasi Tindak Lanjut</h6></div><div class="card-body">
            <form method="POST" action="{{ route('follow-up.verify', $item) }}">@csrf
                <div class="mb-3"><label class="form-label">Status Verifikasi</label><div class="btn-group w-100" role="group">
                    <input type="radio" name="status" value="disetujui" id="v1" class="btn-check" checked><label class="btn btn-outline-success" for="v1"><i class="bi bi-check-circle"></i> Disetujui</label>
                    <input type="radio" name="status" value="revisi" id="v2" class="btn-check"><label class="btn btn-outline-warning" for="v2"><i class="bi bi-arrow-clockwise"></i> Revisi</label>
                    <input type="radio" name="status" value="ditolak" id="v3" class="btn-check"><label class="btn btn-outline-danger" for="v3"><i class="bi bi-x-circle"></i> Ditolak</label>
                </div></div>
                <div class="mb-3"><label class="form-label">Catatan Verifikasi</label><textarea name="catatan" rows="2" class="form-control" placeholder="Berikan catatan (wajib jika revisi/ditolak)"></textarea></div>
                <button class="btn btn-success"><i class="bi bi-check2 me-1"></i>Submit Verifikasi</button>
            </form>
        </div></div>
        @endif
    </div>
    <div class="col-lg-4">
        <div class="card"><div class="card-header bg-white"><h6 class="mb-0 fw-bold">Info Rekomendasi</h6></div><div class="card-body small">
            <p class="mb-2">{{ $item->recommendation->isi }}</p>
            <hr><dl class="row mb-0">
                <dt class="col-5 text-muted">OPD</dt><dd class="col-7">{{ $item->recommendation->finding->opd->nama }}</dd>
                <dt class="col-5 text-muted">Deadline</dt><dd class="col-7">{{ $item->recommendation->deadline->format('d M Y') }}</dd>
                <dt class="col-5 text-muted">Progress</dt><dd class="col-7"><div class="progress" style="height:16px"><div class="progress-bar bg-success" style="width:{{ $item->recommendation->persentase }}%">{{ $item->recommendation->persentase }}%</div></div></dd>
            </dl>
        </div></div>

        @if($item->approvals->isNotEmpty())
        <div class="card mt-3"><div class="card-header bg-white"><h6 class="mb-0 fw-bold">Riwayat Verifikasi</h6></div><div class="card-body p-0">
            @foreach($item->approvals as $ap)<div class="p-2 border-bottom small">
                <span class="badge bg-{{ $ap->status=='disetujui'?'success':($ap->status=='revisi'?'warning':'danger') }}">{{ ucfirst($ap->status) }}</span>
                <div class="text-muted">{{ $ap->verifikator->name }} - {{ $ap->created_at->format('d M Y H:i') }}</div>
                @if($ap->catatan)<div>{{ $ap->catatan }}</div>@endif
            </div>@endforeach
        </div></div>
        @endif
    </div>
</div>
@endsection
