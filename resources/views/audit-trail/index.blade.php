@extends('layouts.app')
@section('title', 'Audit Trail')
@section('page-title', 'Audit Trail - Log Aktivitas')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0 fw-bold"><i class="bi bi-clock-history me-2"></i>Audit Trail</h5>
</div>

<div class="card mb-3"><div class="card-body"><form method="GET" class="row g-2 align-items-end">
    <div class="col-md-4"><label class="form-label small">Cari Aktivitas</label><input type="text" name="q" value="{{ request('q') }}" class="form-control form-control-sm" placeholder="Kata kunci..."></div>
    <div class="col-md-3"><label class="form-label small">Modul</label><select name="log_name" class="form-select form-select-sm"><option value="">Semua</option>@foreach($logNames as $ln)<option value="{{ $ln }}" @if(request('log_name')==$ln)selected @endif>{{ $ln }}</option>@endforeach</select></div>
    <div class="col-md-3"><label class="form-label small">Tanggal</label><input type="date" name="tanggal" value="{{ request('tanggal') }}" class="form-control form-control-sm"></div>
    <div class="col-md-2 d-grid"><button class="btn btn-primary btn-sm"><i class="bi bi-search me-1"></i>Filter</button></div>
</form></div></div>

<div class="card"><div class="card-body">
    <div class="table-responsive"><table class="table table-hover">
        <thead><tr><th>No</th><th>Waktu</th><th>User</th><th>Modul</th><th>Aktivitas</th><th>Aksi</th></tr></thead>
        <tbody>@forelse($items as $i => $act)<tr>
            <td>{{ $items->firstItem() + $i }}</td>
            <td class="small">{{ $act->created_at->format('d/m/Y H:i:s') }}</td>
            <td><div class="d-flex align-items-center gap-1"><img src="{{ $act->causer?->avatar_url ?? 'https://ui-avatars.com/api/?name=S&size=32' }}" class="rounded-circle" width="24" height="24"><span class="small">{{ $act->causer?->name ?? 'Sistem' }}</span></div></td>
            <td><span class="badge bg-primary">{{ $act->log_name }}</span></td>
            <td class="small">{{ $act->description }}</td>
            <td><a href="{{ route('audit-trail.show', $act) }}" class="btn btn-sm btn-outline-info"><i class="bi bi-eye"></i></a></td>
        </tr>@empty<tr><td colspan="6" class="text-center py-4 text-muted">Tidak ada log aktivitas</td></tr>@endforelse</tbody>
    </table></div>
    <div class="d-flex justify-content-center">{{ $items->withQueryString()->links() }}</div>
</div></div>
@endsection
