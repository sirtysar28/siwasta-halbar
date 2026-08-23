@extends('layouts.app')
@section('title', $item->exists ? 'Edit Audit' : 'Input Audit Baru')
@section('page-title', $item->exists ? 'Edit Audit' : 'Input Audit Baru')

@section('content')
<div class="row justify-content-center"><div class="col-lg-10">
    <div class="card"><div class="card-header bg-white"><h6 class="mb-0 fw-bold"><i class="bi bi-clipboard-check me-2"></i>{{ $item->exists ? 'Edit Audit' : 'Input Audit Baru' }}</h6></div>
    <div class="card-body">
        <form method="POST" action="{{ $item->exists ? route('audit.update', $item) : route('audit.store') }}" enctype="multipart/form-data">@csrf @if($item->exists)@method('PUT')@endif
            <div class="row g-3">
                <div class="col-md-4"><label class="form-label">Nomor Audit <span class="text-danger">*</span></label><input type="text" name="nomor" value="{{ old('nomor', $item->nomor) }}" class="form-control" placeholder="cth: AUD/001/2025" required></div>
                <div class="col-md-8"><label class="form-label">Judul Audit <span class="text-danger">*</span></label><input type="text" name="judul" value="{{ old('judul', $item->judul) }}" class="form-control" required></div>
                <div class="col-md-4"><label class="form-label">Tanggal Mulai <span class="text-danger">*</span></label><input type="date" name="tanggal_mulai" value="{{ old('tanggal_mulai', $item->tanggal_mulai?->format('Y-m-d')) }}" class="form-control" required></div>
                <div class="col-md-4"><label class="form-label">Tanggal Selesai</label><input type="date" name="tanggal_selesai" value="{{ old('tanggal_selesai', $item->tanggal_selesai?->format('Y-m-d')) }}" class="form-control"></div>
                <div class="col-md-4"><label class="form-label">Status</label><select name="status" class="form-select">@foreach(['draft'=>'Draft','berjalan'=>'Berjalan','selesai'=>'Selesai','ditutup'=>'Ditutup'] as $k=>$v)<option value="{{ $k }}" @if(old('status', $item->status)==$k)selected @endif>{{ $v }}</option>@endforeach</select></div>
                <div class="col-md-4"><label class="form-label">Jenis Audit <span class="text-danger">*</span></label><select name="jenis_audit_id" class="form-select select2" required><option value="">Pilih...</option>@foreach($jenis as $j)<option value="{{ $j->id }}" @if(old('jenis_audit_id', $item->jenis_audit_id)==$j->id)selected @endif>{{ $j->nama }}</option>@endforeach</select></div>
                <div class="col-md-4"><label class="form-label">Tahun <span class="text-danger">*</span></label><select name="tahun_audit_id" class="form-select" required><option value="">Pilih...</option>@foreach($tahuns as $t)<option value="{{ $t->id }}" @if(old('tahun_audit_id', $item->tahun_audit_id)==$t->id)selected @endif>{{ $t->tahun }}</option>@endforeach</select></div>
                <div class="col-md-4"><label class="form-label">Auditor <span class="text-danger">*</span></label><select name="auditor_id" class="form-select select2" required><option value="">Pilih...</option>@foreach($auditors as $au)<option value="{{ $au->id }}" @if(old('auditor_id', $item->auditor_id)==$au->id)selected @endif>{{ $au->nama }} ({{ $au->instansi }})</option>@endforeach</select></div>
                <div class="col-md-12"><label class="form-label">Instansi yang Diaudit <span class="text-danger">*</span></label><input type="text" name="instansi" value="{{ old('instansi', $item->instansi) }}" class="form-control" placeholder="cth: Pemkab Halmahera Barat" required></div>
                <div class="col-md-12"><label class="form-label">OPD Terkait</label><select name="opds[]" class="form-select select2" multiple><option value="">Pilih OPD...</option>@foreach($opds as $o)<option value="{{ $o->id }}" @if(in_array($o->id, old('opds', $item->opds?->pluck('id')->toArray() ?? [])))selected @endif>{{ $o->nama }}</option>@endforeach</select><small class="text-muted">Pilih satu atau beberapa OPD yang diaudit</small></div>
                <div class="col-md-12"><label class="form-label">Keterangan</label><textarea name="keterangan" rows="3" class="form-control">{{ old('keterangan', $item->keterangan) }}</textarea></div>
                <div class="col-md-12"><label class="form-label">Upload Dokumen (Surat Audit, BA, Pendukung)</label><input type="file" name="documents[]" class="form-control" multiple accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png"><small class="text-muted">Max 10MB per file. Bisa pilih banyak file.</small></div>
                @if($item->exists && $item->documents->isNotEmpty())
                <div class="col-md-12"><label class="form-label">Dokumen Terunggah</label><div>@foreach($item->documents as $d)<a href="{{ route('documents.download', $d) }}" class="badge bg-light text-dark text-decoration-none me-1 mb-1"><i class="bi {{ $d->icon }}"></i> {{ $d->nama_asli }}</a>@endforeach</div></div>
                @endif
            </div>
            <div class="d-flex gap-2 mt-4"><button class="btn btn-primary"><i class="bi bi-save me-1"></i>{{ $item->exists ? 'Update' : 'Simpan' }} Audit</button><a href="{{ route('audit.index') }}" class="btn btn-outline-secondary">Batal</a></div>
        </form>
    </div></div>
</div></div>
@endsection
