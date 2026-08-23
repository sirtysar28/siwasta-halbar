@extends('layouts.app')
@section('title', 'Manajemen Audit')
@section('page-title', 'Manajemen Audit')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0 fw-bold"><i class="bi bi-clipboard-check me-2"></i>Manajemen Audit</h5>
    <a href="{{ route('audit.create') }}" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg me-1"></i>Input Audit Baru</a>
</div>

<div class="card mb-3"><div class="card-body"><form method="GET" class="row g-2 align-items-end">
    <div class="col-md-4"><label class="form-label small">Cari</label><input type="text" name="q" value="{{ request('q') }}" class="form-control form-control-sm" placeholder="Nomor / Judul / Instansi"></div>
    <div class="col-md-2"><label class="form-label small">Jenis Audit</label>@php $jenis = \App\Models\JenisAudit::all(); @endphp<select name="jenis_audit_id" class="form-select form-select-sm"><option value="">Semua</option>@foreach($jenis as $j)<option value="{{ $j->id }}" @if(request('jenis_audit_id')==$j->id)selected @endif>{{ $j->nama }}</option>@endforeach</select></div>
    <div class="col-md-2"><label class="form-label small">Tahun</label>@php $thns = \App\Models\TahunAudit::orderByDesc('tahun')->get(); @endphp<select name="tahun_audit_id" class="form-select form-select-sm"><option value="">Semua</option>@foreach($thns as $t)<option value="{{ $t->id }}" @if(request('tahun_audit_id')==$t->id)selected @endif>{{ $t->tahun }}</option>@endforeach</select></div>
    <div class="col-md-2"><label class="form-label small">Status</label><select name="status" class="form-select form-select-sm"><option value="">Semua</option><option value="draft" @if(request('status')=='draft')selected @endif>Draft</option><option value="berjalan" @if(request('status')=='berjalan')selected @endif>Berjalan</option><option value="selesai" @if(request('status')=='selesai')selected @endif>Selesai</option><option value="ditutup" @if(request('status')=='ditutup')selected @endif>Ditutup</option></select></div>
    <div class="col-md-2 d-grid"><button class="btn btn-primary btn-sm"><i class="bi bi-search me-1"></i>Filter</button></div>
</form></div></div>

<div class="card"><div class="card-body">
    <div class="table-responsive"><table class="table table-hover datatable">
        <thead><tr><th>No</th><th>Nomor</th><th>Judul</th><th>Jenis</th><th>Tahun</th><th>Auditor</th><th>OPD</th><th>Tanggal</th><th>Status</th><th>Aksi</th></tr></thead>
        <tbody>@forelse($items as $i => $a)<tr>
            <td>{{ $items->firstItem() + $i }}</td>
            <td><span class="fw-semibold">{{ $a->nomor }}</span></td>
            <td><a href="{{ route('audit.show', $a) }}" class="text-decoration-none">{{ Str::limit($a->judul, 45) }}</a></td>
            <td class="small">{{ $a->jenisAudit->nama }}</td><td>{{ $a->tahunAudit->tahun }}</td><td class="small">{{ $a->auditor->nama }}</td>
            <td class="small">{{ $a->opds->pluck('nama')->implode(', ') ?: $a->instansi }}</td>
            <td class="small">{{ $a->tanggal_mulai?->format('d/m/Y') }}</td>
            <td>{!! $a->status_badge !!}</td>
            <td nowrap>
                <a href="{{ route('audit.show', $a) }}" class="btn btn-sm btn-outline-info"><i class="bi bi-eye"></i></a>
                <a href="{{ route('audit.edit', $a) }}" class="btn btn-sm btn-outline-warning"><i class="bi bi-pencil"></i></a>
                <form action="{{ route('audit.destroy', $a) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus audit & semua temuan?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button></form>
            </td>
        </tr>@empty<tr><td colspan="10" class="text-center py-4 text-muted">Belum ada data audit</td></tr>@endforelse</tbody>
    </table></div>
    <div class="d-flex justify-content-center">{{ $items->withQueryString()->links() }}</div>
</div></div>
@endsection
