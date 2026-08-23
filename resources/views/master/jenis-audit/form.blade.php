@extends('layouts.app')
@section('title', $item->exists ? 'Edit Jenis Audit' : 'Tambah Jenis Audit')
@section('page-title', $item->exists ? 'Edit Jenis Audit' : 'Tambah Jenis Audit')

@section('content')
<div class="row justify-content-center"><div class="col-lg-6">
    <div class="card"><div class="card-header bg-white"><h6 class="mb-0 fw-bold"><i class="bi bi-tag me-2"></i>{{ $item->exists ? 'Edit' : 'Tambah' }} Jenis Audit</h6></div>
    <div class="card-body">
        <form method="POST" action="{{ $item->exists ? route('master.jenis-audit.update', $item) : route('master.jenis-audit.store') }}">@csrf @if($item->exists)@method('PUT')@endif
            <div class="row g-3">
                <div class="col-md-4"><label class="form-label">Kode <span class="text-danger">*</span></label><input type="text" name="kode" value="{{ old('kode', $item->kode) }}" class="form-control" required></div>
                <div class="col-md-8"><label class="form-label">Nama <span class="text-danger">*</span></label><input type="text" name="nama" value="{{ old('nama', $item->nama) }}" class="form-control" required></div>
                <div class="col-12"><label class="form-label">Keterangan</label><textarea name="keterangan" rows="2" class="form-control">{{ old('keterangan', $item->keterangan) }}</textarea></div>
                <div class="col-12"><div class="form-check"><input type="checkbox" name="is_active" value="1" id="a" class="form-check-input" @if(old('is_active', $item->is_active ?? true))checked @endif><label class="form-check-label" for="a">Aktif</label></div></div>
            </div>
            <div class="d-flex gap-2 mt-4"><button class="btn btn-primary"><i class="bi bi-save me-1"></i>Simpan</button><a href="{{ route('master.jenis-audit.index') }}" class="btn btn-outline-secondary">Batal</a></div>
        </form>
    </div></div>
</div></div>
@endsection
