@extends('layouts.app')
@section('title', 'Manajemen User')
@section('page-title', 'Manajemen User & Role')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0 fw-bold"><i class="bi bi-person-gear me-2"></i>Manajemen User</h5>
    <a href="{{ route('users.create') }}" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg me-1"></i>Tambah User</a>
</div>

<div class="card mb-3"><div class="card-body"><form method="GET" class="row g-2 align-items-end">
    <div class="col-md-4"><label class="form-label small">Cari</label><input type="text" name="q" value="{{ request('q') }}" class="form-control form-control-sm" placeholder="Nama / Email / NIP"></div>
    <div class="col-md-3"><label class="form-label small">Role</label><select name="role" class="form-select form-select-sm"><option value="">Semua</option>@foreach($roles as $r)<option value="{{ $r->name }}" @if(request('role')==$r->name)selected @endif>{{ $r->name }}</option>@endforeach</select></div>
    <div class="col-md-3"><label class="form-label small">OPD</label><select name="opd_id" class="form-select form-select-sm"><option value="">Semua</option>@foreach($opds as $o)<option value="{{ $o->id }}" @if(request('opd_id')==$o->id)selected @endif>{{ $o->nama }}</option>@endforeach</select></div>
    <div class="col-md-2 d-grid"><button class="btn btn-primary btn-sm"><i class="bi bi-search me-1"></i>Filter</button></div>
</form></div></div>

<div class="card"><div class="card-body">
    <div class="table-responsive"><table class="table table-hover datatable">
        <thead><tr><th>No</th><th>User</th><th>Email</th><th>NIP</th><th>OPD</th><th>Role</th><th>Status</th><th>Login Terakhir</th><th>Aksi</th></tr></thead>
        <tbody>@forelse($items as $i => $u)<tr>
            <td>{{ $items->firstItem() + $i }}</td>
            <td><div class="d-flex align-items-center gap-2"><img src="{{ $u->avatar_url }}" class="rounded-circle" width="32" height="32"><span class="fw-semibold">{{ $u->name }}</span></div></td>
            <td class="small">{{ $u->email }}</td><td class="small">{{ $u->nip ?? '-' }}</td><td class="small">{{ $u->opd?->nama ?? '-' }}</td>
            <td><span class="badge bg-info text-dark">{{ $u->role_name }}</span></td>
            <td>@if($u->is_active)<span class="badge bg-success">Aktif</span>@else<span class="badge bg-danger">Nonaktif</span>@endif</td>
            <td class="small">{{ $u->last_login_at?->diffForHumans() ?? 'Belum pernah' }}</td>
            <td nowrap>
                <a href="{{ route('users.edit', $u) }}" class="btn btn-sm btn-outline-warning"><i class="bi bi-pencil"></i></a>
                <a href="{{ route('users.login-activities', $u) }}" class="btn btn-sm btn-outline-info" title="Riwayat Login"><i class="bi bi-clock-history"></i></a>
                <form action="{{ route('users.reset-password', $u) }}" method="POST" class="d-inline" onsubmit="return confirm('Reset password user ini?')">@csrf<button class="btn btn-sm btn-outline-secondary" title="Reset Password"><i class="bi bi-key"></i></button></form>
                @if($u->id !== auth()->id())<form action="{{ route('users.destroy', $u) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus user ini?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button></form>@endif
            </td>
        </tr>@empty<tr><td colspan="9" class="text-center py-4 text-muted">Belum ada user</td></tr>@endforelse</tbody>
    </table></div>
    <div class="d-flex justify-content-center">{{ $items->withQueryString()->links() }}</div>
</div></div>
@endsection
