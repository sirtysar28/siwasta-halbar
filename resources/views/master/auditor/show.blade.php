@extends('layouts.app')
@section('title', 'Detail Auditor')
@section('page-title', 'Detail Auditor')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0 fw-bold">{{ $item->nama }}</h5>
    <div><a href="{{ route('master.auditor.edit', $item) }}" class="btn btn-warning btn-sm">Edit</a><a href="{{ route('master.auditor.index') }}" class="btn btn-outline-secondary btn-sm">Kembali</a></div>
</div>
<div class="row g-3">
    <div class="col-lg-4"><div class="card"><div class="card-body text-center">
        <img src="https://ui-avatars.com/api/?name={{ urlencode($item->nama) }}&size=128&background=1e40af&color=fff" class="rounded-circle mb-3" width="100">
        <h5 class="fw-bold mb-0">{{ $item->nama }}</h5><p class="text-muted small">{{ $item->jabatan }}</p>
        <hr><div class="text-start small">
            <p class="mb-2"><i class="bi bi-person-badge me-2"></i>{{ $item->instansi ?? '-' }}</p>
            <p class="mb-2"><i class="bi bi-telephone me-2"></i>{{ $item->telepon ?? '-' }}</p>
            <p class="mb-0"><i class="bi bi-envelope me-2"></i>{{ $item->email ?? '-' }}</p>
        </div>
    </div></div></div>
    <div class="col-lg-8"><div class="card"><div class="card-header bg-white"><h6 class="mb-0 fw-bold">Riwayat Audit ({{ $item->audits->count() }})</h6></div><div class="card-body p-0">
        <div class="table-responsive"><table class="table table-sm mb-0"><thead><tr><th>Nomor</th><th>Judul</th><th>Tanggal</th><th>Status</th></tr></thead>
        <tbody>@forelse($item->audits as $a)<tr><td>{{ $a->nomor }}</td><td class="small"><a href="{{ route('audit.show', $a) }}">{{ $a->judul }}</a></td><td>{{ $a->tanggal_mulai?->format('d M Y') }}</td><td>{!! $a->status_badge !!}</td></tr>@empty<tr><td colspan="4" class="text-center text-muted py-3">Belum ada audit</td></tr>@endforelse</tbody></table></div>
    </div></div></div>
</div>
@endsection
