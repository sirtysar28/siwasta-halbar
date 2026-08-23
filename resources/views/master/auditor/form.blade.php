@extends('layouts.app')
@section('title', $item->exists ? 'Edit Auditor' : 'Tambah Auditor')
@section('page-title', $item->exists ? 'Edit Auditor' : 'Tambah Auditor')

@section('content')
<div class="row justify-content-center"><div class="col-lg-8">
    <div class="card"><div class="card-header bg-white"><h6 class="mb-0 fw-bold"><i class="bi bi-person-badge me-2"></i>{{ $item->exists ? 'Edit' : 'Tambah' }} Auditor</h6></div>
    <div class="card-body">
        <form method="POST" action="{{ $item->exists ? route('master.auditor.update', $item) : route('master.auditor.store') }}">@csrf @if($item->exists)@method('PUT')@endif
            <div class="row g-3">
                <div class="col-md-8"><label class="form-label">Nama <span class="text-danger">*</span></label><input type="text" name="nama" value="{{ old('nama', $item->nama) }}" class="form-control" required></div>
                <div class="col-md-4"><label class="form-label">NIP</label><input type="text" name="nip" value="{{ old('nip', $item->nip) }}" class="form-control"></div>
                <div class="col-md-6"><label class="form-label">Jabatan</label><input type="text" name="jabatan" value="{{ old('jabatan', $item->jabatan) }}" class="form-control"></div>
                <div class="col-md-6"><label class="form-label">Instansi</label><input type="text" name="instansi" value="{{ old('instansi', $item->instansi) }}" class="form-control"></div>
                <div class="col-md-6"><label class="form-label">Email</label><input type="email" name="email" value="{{ old('email', $item->email) }}" class="form-control"></div>
                <div class="col-md-6"><label class="form-label">Telepon</label><input type="text" name="telepon" value="{{ old('telepon', $item->telepon) }}" class="form-control"></div>
                <div class="col-12"><div class="form-check"><input type="checkbox" name="is_active" value="1" id="active" class="form-check-input" @if(old('is_active', $item->is_active ?? true)) checked @endif><label class="form-check-label" for="active">Auditor Aktif</label></div></div>
            </div>
            <div class="d-flex gap-2 mt-4"><button class="btn btn-primary"><i class="bi bi-save me-1"></i>Simpan</button><a href="{{ route('master.auditor.index') }}" class="btn btn-outline-secondary">Batal</a></div>
        </form>
    </div></div>
</div></div>
@endsection
