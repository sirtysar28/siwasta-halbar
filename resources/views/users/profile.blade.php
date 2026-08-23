@extends('layouts.app')
@section('title', 'Profil Saya')
@section('page-title', 'Profil Saya')

@section('content')
<div class="row g-3">
    <div class="col-lg-4">
        <div class="card text-center"><div class="card-body">
            <img src="{{ $user->avatar_url }}" class="rounded-circle mb-3" width="120" height="120">
            <h5 class="fw-bold mb-0">{{ $user->name }}</h5>
            <p class="text-muted small mb-1">{{ $user->email }}</p>
            <span class="badge bg-info text-dark">{{ $user->role_name }}</span>
            <hr>
            <div class="small text-start">
                <p class="mb-2"><i class="bi bi-person-badge me-2 text-muted"></i>{{ $user->nip ?? '-' }}</p>
                <p class="mb-2"><i class="bi bi-telephone me-2 text-muted"></i>{{ $user->phone ?? '-' }}</p>
                <p class="mb-2"><i class="bi bi-building me-2 text-muted"></i>{{ $user->opd?->nama ?? '-' }}</p>
                <p class="mb-0"><i class="bi bi-clock me-2 text-muted"></i>Login terakhir: {{ $user->last_login_at?->diffForHumans() ?? '-' }}</p>
            </div>
        </div></div>
    </div>
    <div class="col-lg-8">
        <div class="card mb-3"><div class="card-header bg-white"><h6 class="mb-0 fw-bold"><i class="bi bi-pencil-square me-2"></i>Edit Profil</h6></div><div class="card-body">
            <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">@csrf
                <div class="row g-3">
                    <div class="col-md-8"><label class="form-label">Nama</label><input type="text" name="name" value="{{ old('name', $user->name) }}" class="form-control"></div>
                    <div class="col-md-4"><label class="form-label">NIP</label><input type="text" name="nip" value="{{ old('nip', $user->nip) }}" class="form-control"></div>
                    <div class="col-md-8"><label class="form-label">Email</label><input type="email" value="{{ $user->email }}" class="form-control" disabled></div>
                    <div class="col-md-4"><label class="form-label">Telepon</label><input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="form-control"></div>
                    <div class="col-12"><label class="form-label">Foto Profil</label><input type="file" name="avatar" class="form-control" accept="image/*"></div>
                </div>
                <button class="btn btn-primary mt-3"><i class="bi bi-save me-1"></i>Simpan Perubahan</button>
            </form>
        </div></div>

        <div class="card"><div class="card-header bg-white"><h6 class="mb-0 fw-bold"><i class="bi bi-key me-2"></i>Ubah Password</h6></div><div class="card-body">
            <form method="POST" action="{{ route('profile.password') }}">@csrf
                <div class="row g-3">
                    <div class="col-12"><label class="form-label">Password Saat Ini</label><input type="password" name="current_password" class="form-control" required></div>
                    <div class="col-md-6"><label class="form-label">Password Baru</label><input type="password" name="password" class="form-control" required></div>
                    <div class="col-md-6"><label class="form-label">Konfirmasi Password Baru</label><input type="password" name="password_confirmation" class="form-control" required></div>
                </div>
                <button class="btn btn-warning mt-3"><i class="bi bi-key me-1"></i>Ubah Password</button>
            </form>
        </div></div>
    </div>
</div>
@endsection
