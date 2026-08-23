@extends('layouts.app')
@section('title', 'Notifikasi')
@section('page-title', 'Kotak Masuk Notifikasi')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0 fw-bold"><i class="bi bi-bell me-2"></i>Notifikasi</h5>
    <div class="d-flex gap-1">
        @if(auth()->user()->hasRole(['Super Administrator','Administrator','Inspektorat']))<a href="{{ route('notifications.broadcast.form') }}" class="btn btn-primary btn-sm"><i class="bi bi-megaphone me-1"></i>Broadcast</a>@endif
        <form method="POST" action="{{ route('notifications.read-all') }}">@csrf<button class="btn btn-outline-secondary btn-sm"><i class="bi bi-check2-all me-1"></i>Tandai Semua Dibaca</button></form>
    </div>
</div>

<ul class="nav nav-pills mb-3">
    <li class="nav-item"><a class="nav-link py-1 px-3 {{ $tab=='inbox'?'active':'' }}" href="{{ route('notifications.index') }}">Semua</a></li>
    <li class="nav-item"><a class="nav-link py-1 px-3 {{ $tab=='unread'?'active':'' }}" href="{{ route('notifications.index',['tab'=>'unread']) }}">Belum Dibaca</a></li>
    <li class="nav-item"><a class="nav-link py-1 px-3 {{ $tab=='reminder'?'active':'' }}" href="{{ route('notifications.index',['tab'=>'reminder']) }}">Reminder</a></li>
    <li class="nav-item"><a class="nav-link py-1 px-3 {{ $tab=='broadcast'?'active':'' }}" href="{{ route('notifications.index',['tab'=>'broadcast']) }}">Broadcast</a></li>
</ul>

<div class="card"><div class="card-body p-0">
    @forelse($items as $n)
    <div class="list-group-item {{ $n->dibaca?'':'bg-light-subtle' }} d-flex align-items-start py-3">
        <div class="me-3"><i class="bi {{ $n->tipe_icon }} fs-4"></i></div>
        <div class="flex-grow-1">
            <div class="d-flex justify-content-between"><strong class="small">{{ $n->judul }}</strong>
            <div><small class="text-muted">{{ $n->created_at->diffForHumans() }}</small></div></div>
            <p class="mb-1 small text-muted">{{ $n->pesan }}</p>
            @if($n->url)<a href="{{ route('notifications.read', $n) }}" class="small">Lihat detail →</a>@endif
        </div>
        @if(!$n->dibaca)<span class="badge bg-primary ms-2">Baru</span>@endif
    </div>
    @empty<div class="text-center py-5 text-muted"><i class="bi bi-inbox fs-1 d-block"></i><p class="mt-2">Tidak ada notifikasi</p></div>@endforelse
</div></div>
<div class="d-flex justify-content-center mt-3">{{ $items->withQueryString()->links() }}</div>
@endsection
