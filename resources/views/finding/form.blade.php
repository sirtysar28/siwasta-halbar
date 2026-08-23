@extends('layouts.app')
@section('title', $item->exists ? 'Edit Temuan' : 'Tambah Temuan')
@section('page-title', $item->exists ? 'Edit Temuan' : 'Tambah Temuan Baru')

@section('content')
<div class="row justify-content-center"><div class="col-lg-10">
    <div class="card"><div class="card-header bg-white"><h6 class="mb-0 fw-bold"><i class="bi bi-search me-2"></i>{{ $item->exists ? 'Edit' : 'Tambah' }} Temuan</h6></div>
    <div class="card-body">
        <form method="POST" action="{{ $item->exists ? route('finding.update', $item) : route('finding.store') }}">@csrf @if($item->exists)@method('PUT')@endif
            <div class="row g-3">
                <div class="col-md-3"><label class="form-label">Audit <span class="text-danger">*</span></label><select name="audit_id" class="form-select select2" required><option value="">Pilih Audit</option>@foreach($audits as $a)<option value="{{ $a->id }}" @if(old('audit_id', $item->audit_id ?? $audit_id)==$a->id)selected @endif>{{ $a->nomor }} - {{ Str::limit($a->judul, 30) }}</option>@endforeach</select></div>
                <div class="col-md-3"><label class="form-label">Nomor Temuan <span class="text-danger">*</span></label><input type="text" name="nomor" value="{{ old('nomor', $item->nomor) }}" class="form-control" placeholder="cth: 01" required></div>
                <div class="col-md-6"><label class="form-label">Judul Temuan <span class="text-danger">*</span></label><input type="text" name="judul" value="{{ old('judul', $item->judul) }}" class="form-control" required></div>
                <div class="col-md-5"><label class="form-label">OPD <span class="text-danger">*</span></label><select name="opd_id" class="form-select select2" required><option value="">Pilih OPD</option>@foreach($opds as $o)<option value="{{ $o->id }}" @if(old('opd_id', $item->opd_id)==$o->id)selected @endif>{{ $o->nama }}</option>@endforeach</select></div>
                <div class="col-md-3"><label class="form-label">Kategori <span class="text-danger">*</span></label><select name="kategori_temuan_id" class="form-select" required><option value="">Pilih</option>@foreach($kategoris as $k)<option value="{{ $k->id }}" @if(old('kategori_temuan_id', $item->kategori_temuan_id)==$k->id)selected @endif>{{ $k->nama }}</option>@endforeach</select></div>
                <div class="col-md-2"><label class="form-label">Nilai Temuan (Rp)</label><input type="text" name="nilai" value="{{ old('nilai', $item->nilai ? number_format($item->nilai, 0, '.', ',') : '0') }}" class="form-control text-end"></div>
                <div class="col-md-2"><label class="form-label">Risiko <span class="text-danger">*</span></label><select name="tingkat_risiko" class="form-select">@foreach(['rendah'=>'Rendah','sedang'=>'Sedang','tinggi'=>'Tinggi','sangat_tinggi'=>'Sangat Tinggi'] as $k=>$v)<option value="{{ $k }}" @if(old('tingkat_risiko', $item->tingkat_risiko)==$k)selected @endif>{{ $v }}</option>@endforeach</select></div>
                <div class="col-md-2"><label class="form-label">Status</label><select name="status" class="form-select">@foreach(['baru'=>'Baru','diproses'=>'Diproses','selesai'=>'Selesai','revisi'=>'Revisi','ditolak'=>'Ditolak'] as $k=>$v)<option value="{{ $k }}" @if(old('status', $item->status ?? 'baru')==$k)selected @endif>{{ $v }}</option>@endforeach</select></div>
                <div class="col-md-12"><label class="form-label">Deskripsi Temuan</label><textarea name="deskripsi" rows="4" class="form-control">{{ old('deskripsi', $item->deskripsi) }}</textarea></div>
            </div>
            <div class="d-flex gap-2 mt-4"><button class="btn btn-primary"><i class="bi bi-save me-1"></i>Simpan</button><a href="{{ route('finding.index') }}" class="btn btn-outline-secondary">Batal</a></div>
        </form>
    </div></div>
</div></div>
@endsection
