@extends('layouts.app')
@section('title', 'Broadcast Notifikasi')
@section('page-title', 'Broadcast Notifikasi')

@section('content')
<div class="row justify-content-center"><div class="col-lg-7">
    <div class="card"><div class="card-header bg-white"><h6 class="mb-0 fw-bold"><i class="bi bi-megaphone me-2"></i>Broadcast Notifikasi ke Semua User</h6></div>
    <div class="card-body">
        <form method="POST" action="{{ route('notifications.broadcast') }}">@csrf
            <div class="mb-3"><label class="form-label">Tipe</label><select name="tipe" class="form-select"><option value="info">Info</option><option value="success">Sukses</option><option value="warning">Peringatan</option><option value="danger">Bahaya</option><option value="reminder">Reminder</option></select></div>
            <div class="mb-3"><label class="form-label">Judul <span class="text-danger">*</span></label><input type="text" name="judul" class="form-control" required></div>
            <div class="mb-3"><label class="form-label">Pesan <span class="text-danger">*</span></label><textarea name="pesan" rows="4" class="form-control" required></textarea></div>
            <div class="d-flex gap-2"><button class="btn btn-primary"><i class="bi bi-send me-1"></i>Broadcast</button><a href="{{ route('notifications.index') }}" class="btn btn-outline-secondary">Batal</a></div>
        </form>
    </div></div>
</div></div>
@endsection
