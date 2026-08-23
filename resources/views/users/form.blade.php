@extends('layouts.app')
@section('title', $item->exists ? 'Edit User' : 'Tambah User')
@section('page-title', $item->exists ? 'Edit User' : 'Tambah User Baru')

@section('content')
<div class="row justify-content-center"><div class="col-lg-9">
    <div class="card"><div class="card-header bg-white"><h6 class="mb-0 fw-bold"><i class="bi bi-person-gear me-2"></i>{{ $item->exists ? 'Edit' : 'Tambah' }} User</h6></div>
    <div class="card-body">
        <form method="POST" action="{{ $item->exists ? route('users.update', $item) : route('users.store') }}">@csrf @if($item->exists)@method('PUT')@endif
            <div class="row g-3">
                <div class="col-md-6"><label class="form-label">Nama Lengkap <span class="text-danger">*</span></label><input type="text" name="name" value="{{ old('name', $item->name) }}" class="form-control" required></div>
                <div class="col-md-6"><label class="form-label">NIP</label><input type="text" name="nip" value="{{ old('nip', $item->nip) }}" class="form-control"></div>
                <div class="col-md-6"><label class="form-label">Email <span class="text-danger">*</span></label><input type="email" name="email" value="{{ old('email', $item->email) }}" class="form-control" required></div>
                <div class="col-md-6"><label class="form-label">Telepon</label><input type="text" name="phone" value="{{ old('phone', $item->phone) }}" class="form-control"></div>
                <div class="col-md-6"><label class="form-label">OPD</label><select name="opd_id" class="form-select select2"><option value="">- Pilih OPD -</option>@foreach($opds as $o)<option value="{{ $o->id }}" @if(old('opd_id', $item->opd_id)==$o->id)selected @endif>{{ $o->nama }}</option>@endforeach</select></div>
                <div class="col-md-6"><label class="form-label">Role <span class="text-danger">*</span></label><select name="role" class="form-select" required>@foreach($roles as $r)<option value="{{ $r->name }}" @if(old('role', $item->roles->first()?->name)==$r->name)selected @endif>{{ $r->name }}</option>@endforeach</select></div>
                <div class="col-md-6"><label class="form-label">Password {{ $item->exists ? '(kosongkan jika tidak diubah)' : '<span class="text-danger">*</span>' }}</label><input type="password" name="password" class="form-control" {{ $item->exists?'':'required' }}></div>
                <div class="col-md-6"><label class="form-label">Konfirmasi Password</label><input type="password" name="password_confirmation" class="form-control"></div>
                <div class="col-12"><div class="form-check"><input type="checkbox" name="is_active" value="1" id="ua" class="form-check-input" @if(old('is_active', $item->is_active ?? true))checked @endif><label class="form-check-label" for="ua">User Aktif</label></div></div>
            </div>
            <div class="d-flex gap-2 mt-4"><button class="btn btn-primary"><i class="bi bi-save me-1"></i>Simpan</button><a href="{{ route('users.index') }}" class="btn btn-outline-secondary">Batal</a></div>
        </form>
    </div></div>
</div></div>
@endsection
