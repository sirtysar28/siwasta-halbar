@extends('layouts.app')
@section('title', 'Detail Log')
@section('page-title', 'Detail Log Aktivitas')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0 fw-bold">Detail Log Aktivitas</h5>
    <a href="{{ route('audit-trail.index') }}" class="btn btn-outline-secondary btn-sm">Kembali</a>
</div>
<div class="card"><div class="card-body">
    <dl class="row">
        <dt class="col-sm-3 text-muted">Waktu</dt><dd class="col-sm-9">{{ $item->created_at->format('d F Y H:i:s') }}</dd>
        <dt class="col-sm-3 text-muted">User</dt><dd class="col-sm-9">{{ $item->causer?->name ?? 'Sistem' }} ({{ $item->causer?->email ?? '-' }})</dd>
        <dt class="col-sm-3 text-muted">Modul</dt><dd class="col-sm-9"><span class="badge bg-primary">{{ $item->log_name }}</span></dd>
        <dt class="col-sm-3 text-muted">Event</dt><dd class="col-sm-9"><span class="badge bg-secondary">{{ $item->event ?? '-' }}</span></dd>
        <dt class="col-sm-3 text-muted">Deskripsi</dt><dd class="col-sm-9">{{ $item->description }}</dd>
    </dl>
    @if($item->properties->count())
    <hr><h6 class="fw-bold mb-2">Detail Perubahan</h6>
    <pre class="bg-dark text-light p-3 rounded small"><code>{{ json_encode($item->properties, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</code></pre>
    @endif
</div></div>
@endsection
