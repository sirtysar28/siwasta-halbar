@extends('layouts.app')
@section('title', $item->exists ? 'Edit OPD' : 'Tambah OPD')
@section('page-title', $item->exists ? 'Edit OPD' : 'Tambah OPD Baru')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header bg-white"><h6 class="mb-0 fw-bold"><i class="bi bi-building me-2"></i>{{ $item->exists ? 'Edit' : 'Tambah' }} OPD</h6></div>
            <div class="card-body">
                <form method="POST" action="{{ $item->exists ? route('master.opd.update', $item) : route('master.opd.store') }}">
                    @csrf @if($item->exists) @method('PUT') @endif
                    <div class="row g-3">
                        <div class="col-md-6"><label class="form-label">Kode OPD <span class="text-danger">*</span></label><input type="text" name="kode" value="{{ old('kode', $item->kode) }}" class="form-control" required></div>
                        <div class="col-md-6"><label class="form-label">Nama OPD <span class="text-danger">*</span></label><input type="text" name="nama" value="{{ old('nama', $item->nama) }}" class="form-control" required></div>
                        <div class="col-md-6"><label class="form-label">Kepala OPD</label><input type="text" name="kepala" value="{{ old('kepala', $item->kepala) }}" class="form-control"></div>
                        <div class="col-md-6"><label class="form-label">Telepon</label><input type="text" name="telepon" value="{{ old('telepon', $item->telepon) }}" class="form-control"></div>
                        <div class="col-md-12"><label class="form-label">Email</label><input type="email" name="email" value="{{ old('email', $item->email) }}" class="form-control"></div>
                        <div class="col-md-12"><label class="form-label">Alamat</label><textarea name="alamat" rows="2" class="form-control">{{ old('alamat', $item->alamat) }}</textarea></div>
                        <div class="col-md-12"><div class="form-check"><input type="checkbox" name="is_active" value="1" class="form-check-input" id="active" @if(old('is_active', $item->is_active ?? true)) checked @endif><label class="form-check-label" for="active">OPD Aktif</label></div></div>
                    </div>
                    <div class="d-flex gap-2 mt-4">
                        <button class="btn btn-primary"><i class="bi bi-save me-1"></i>Simpan</button>
                        <a href="{{ route('master.opd.index') }}" class="btn btn-outline-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
