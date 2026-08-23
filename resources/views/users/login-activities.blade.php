@extends('layouts.app')
@section('title', 'Riwayat Login')
@section('page-title', 'Riwayat Login - ' . $user->name)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0 fw-bold"><i class="bi bi-clock-history me-2"></i>Riwayat Login: {{ $user->name }}</h5>
    <a href="{{ route('users.index') }}" class="btn btn-outline-secondary btn-sm">Kembali</a>
</div>

<div class="card"><div class="card-body">
    <div class="table-responsive"><table class="table table-hover">
        <thead><tr><th>No</th><th>Waktu Login</th><th>Waktu Logout</th><th>Durasi</th><th>IP Address</th><th>Device</th></tr></thead>
        <tbody>@forelse($items as $i => $la)<tr>
            <td>{{ $items->firstItem() + $i }}</td>
            <td>{{ $la->login_at->format('d/m/Y H:i:s') }}</td>
            <td>{{ $la->logout_at?->format('d/m/Y H:i:s') ?? '<span class="text-muted">Aktif</span>' }}</td>
            <td class="small">{{ $la->logout_at ? $la->login_at->diffForHumans($la->logout_at, true) : $la->login_at->diffForHumans(null, true) }}</td>
            <td><code>{{ $la->ip_address }}</code></td>
            <td class="small">{{ Str::limit($la->device, 60) }}</td>
        </tr>@empty<tr><td colspan="6" class="text-center py-4 text-muted">Belum ada riwayat login</td></tr>@endforelse</tbody>
    </table></div>
    <div class="d-flex justify-content-center">{{ $items->links() }}</div>
</div></div>
@endsection
