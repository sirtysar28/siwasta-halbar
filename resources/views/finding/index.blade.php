@extends('layouts.app')
@section('title', 'Temuan')
@section('page-title', 'Manajemen Temuan')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0 fw-bold"><i class="bi bi-search me-2"></i>Manajemen Temuan</h5>
    <a href="{{ route('finding.create') }}" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg me-1"></i>Tambah Temuan</a>
</div>

<div class="card mb-3"><div class="card-body"><form method="GET" class="row g-2 align-items-end">
    <div class="col-md-3"><label class="form-label small">Cari</label><input type="text" name="q" value="{{ request('q') }}" class="form-control form-control-sm" placeholder="Nomor / Judul"></div>
    <div class="col-md-3"><label class="form-label small">OPD</label>@php $opds = \App\Models\Opd::orderBy('nama')->get(); @endphp<select name="opd_id" class="form-select form-select-sm"><option value="">Semua OPD</option>@foreach($opds as $o)<option value="{{ $o->id }}" @if(request('opd_id')==$o->id)selected @endif>{{ $o->nama }}</option>@endforeach</select></div>
    <div class="col-md-2"><label class="form-label small">Status</label><select name="status" class="form-select form-select-sm"><option value="">Semua</option>@foreach(['baru'=>'Baru','diproses'=>'Diproses','selesai'=>'Selesai','revisi'=>'Revisi','ditolak'=>'Ditolak'] as $k=>$v)<option value="{{ $k }}" @if(request('status')==$k)selected @endif>{{ $v }}</option>@endforeach</select></div>
    <div class="col-md-2"><label class="form-label small">Risiko</label><select name="tingkat_risiko" class="form-select form-select-sm"><option value="">Semua</option>@foreach(['rendah'=>'Rendah','sedang'=>'Sedang','tinggi'=>'Tinggi','sangat_tinggi'=>'Sangat Tinggi'] as $k=>$v)<option value="{{ $k }}" @if(request('tingkat_risiko')==$k)selected @endif>{{ $v }}</option>@endforeach</select></div>
    <div class="col-md-2 d-grid"><button class="btn btn-primary btn-sm"><i class="bi bi-search me-1"></i>Filter</button></div>
</form></div></div>

<div class="card"><div class="card-body">
    <div class="table-responsive"><table class="table table-hover datatable">
        <thead><tr><th>No</th><th>Nomor</th><th>Judul</th><th>OPD</th><th>Nilai</th><th>Kategori</th><th>Risiko</th><th>Progress</th><th>Status</th><th>Aksi</th></tr></thead>
        <tbody>@forelse($items as $i => $f)<tr>
            <td>{{ $items->firstItem() + $i }}</td>
            <td>{{ $f->nomor }}</td>
            <td><a href="{{ route('finding.show', $f) }}" class="text-decoration-none">{{ Str::limit($f->judul, 40) }}</a></td>
            <td class="small">{{ $f->opd->nama }}</td>
            <td>Rp {{ number_format($f->nilai/1000000, 1) }}jt</td>
            <td class="small">{{ $f->kategoriTemuan->nama }}</td>
            <td>{!! $f->risiko_badge !!}</td>
            <td style="min-width:110px"><div class="progress" style="height:16px"><div class="progress-bar bg-{{ $f->progress_rata>=100?'success':($f->progress_rata>=50?'warning':'danger') }}" style="width:{{ $f->progress_rata }}%">{{ $f->progress_rata }}%</div></div></td>
            <td>{!! $f->status_badge !!}</td>
            <td nowrap>
                <a href="{{ route('finding.show', $f) }}" class="btn btn-sm btn-outline-info"><i class="bi bi-eye"></i></a>
                <a href="{{ route('finding.edit', $f) }}" class="btn btn-sm btn-outline-warning"><i class="bi bi-pencil"></i></a>
                <form action="{{ route('finding.destroy', $f) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus temuan & rekomendasi?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button></form>
            </td>
        </tr>@empty<tr><td colspan="10" class="text-center py-4 text-muted">Belum ada temuan</td></tr>@endforelse</tbody>
    </table></div>
    <div class="d-flex justify-content-center">{{ $items->withQueryString()->links() }}</div>
</div></div>
@endsection
