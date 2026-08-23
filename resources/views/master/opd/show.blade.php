@extends('layouts.app')
@section('title', 'Detail OPD')
@section('page-title', 'Detail OPD')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0 fw-bold">{{ $item->nama }}</h5>
    <div>
        <a href="{{ route('master.opd.edit', $item) }}" class="btn btn-warning btn-sm"><i class="bi bi-pencil me-1"></i>Edit</a>
        <a href="{{ route('master.opd.index') }}" class="btn btn-outline-secondary btn-sm">Kembali</a>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-body text-center">
                <div class="avatar mx-auto mb-3" style="width:80px;height:80px;background:#1e40af;border-radius:1rem;display:flex;align-items:center;justify-content:center"><i class="bi bi-building text-white" style="font-size:2.5rem"></i></div>
                <h5 class="fw-bold mb-1">{{ $item->nama }}</h5>
                <p class="text-muted mb-2">{{ $item->kode }}</p>
                @if($item->is_active) <span class="badge bg-success">Aktif</span> @else <span class="badge bg-danger">Nonaktif</span> @endif
                <hr>
                <div class="text-start small">
                    <p class="mb-2"><i class="bi bi-person me-2 text-muted"></i>{{ $item->kepala ?? '-' }}</p>
                    <p class="mb-2"><i class="bi bi-telephone me-2 text-muted"></i>{{ $item->telepon ?? '-' }}</p>
                    <p class="mb-2"><i class="bi bi-envelope me-2 text-muted"></i>{{ $item->email ?? '-' }}</p>
                    <p class="mb-0"><i class="bi bi-geo-alt me-2 text-muted"></i>{{ $item->alamat ?? '-' }}</p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card mb-3"><div class="card-header bg-white"><h6 class="mb-0 fw-bold">Statistik Temuan</h6></div><div class="card-body">
            <div class="row text-center">
                <div class="col-4"><h3 class="fw-bold text-primary">{{ $item->findings->count() }}</h3><small class="text-muted">Total Temuan</small></div>
                <div class="col-4"><h3 class="fw-bold text-success">{{ $item->findings->flatMap->recommendations->where('status','selesai')->count() }}</h3><small class="text-muted">Selesai</small></div>
                <div class="col-4"><h3 class="fw-bold text-warning">{{ $item->users->count() }}</h3><small class="text-muted">User OPD</small></div>
            </div>
        </div></div>
        <div class="card"><div class="card-header bg-white"><h6 class="mb-0 fw-bold">Daftar Temuan</h6></div><div class="card-body p-0">
            <div class="table-responsive"><table class="table table-sm mb-0">
                <thead><tr><th>Nomor</th><th>Judul</th><th>Nilai</th><th>Status</th></tr></thead>
                <tbody>@forelse($item->findings as $f)<tr><td>{{ $f->nomor }}</td><td class="small"><a href="{{ route('finding.show', $f) }}">{{ $f->judul }}</a></td><td>Rp {{ number_format($f->nilai) }}</td><td>{!! $f->status_badge !!}</td></tr>@empty<tr><td colspan="4" class="text-center text-muted py-3">Belum ada temuan</td></tr>@endforelse</tbody>
            </table></div>
        </div></div>
    </div>
</div>
@endsection
