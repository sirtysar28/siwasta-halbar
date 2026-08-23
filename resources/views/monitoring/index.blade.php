@extends('layouts.app')
@section('title', 'Monitoring')
@section('page-title', 'Monitoring Tindak Lanjut')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <h5 class="mb-0 fw-bold"><i class="bi bi-graph-up me-2"></i>Monitoring Tindak Lanjut</h5>
    <div class="btn-group btn-group-sm">
        <a href="{{ route('monitoring.export', 'excel') }}" class="btn btn-outline-success"><i class="bi bi-file-earmark-excel me-1"></i>Excel</a>
        <a href="{{ route('monitoring.export', 'pdf') }}" class="btn btn-outline-danger"><i class="bi bi-file-earmark-pdf me-1"></i>PDF</a>
        <a href="{{ route('monitoring.export', 'csv') }}" class="btn btn-outline-secondary"><i class="bi bi-filetype-csv me-1"></i>CSV</a>
        <a href="javascript:window.print()" class="btn btn-outline-primary"><i class="bi bi-printer me-1"></i>Print</a>
    </div>
</div>

<div class="card mb-3"><div class="card-body"><form method="GET" class="row g-2 align-items-end">
    <div class="col-md-2"><label class="form-label small">Tahun</label><select name="tahun" class="form-select form-select-sm"><option value="">Semua</option>@foreach($tahuns as $t)<option value="{{ $t->tahun }}" @if(request('tahun')==$t->tahun)selected @endif>{{ $t->tahun }}</option>@endforeach</select></div>
    <div class="col-md-3"><label class="form-label small">OPD</label><select name="opd_id" class="form-select form-select-sm"><option value="">Semua</option>@foreach($opds as $o)<option value="{{ $o->id }}" @if(request('opd_id')==$o->id)selected @endif>{{ $o->nama }}</option>@endforeach</select></div>
    <div class="col-md-2"><label class="form-label small">Status</label><select name="status" class="form-select form-select-sm"><option value="">Semua</option>@foreach(['baru'=>'Baru','diproses'=>'Diproses','selesai'=>'Selesai','revisi'=>'Revisi','ditolak'=>'Ditolak'] as $k=>$v)<option value="{{ $k }}" @if(request('status')==$k)selected @endif>{{ $v }}</option>@endforeach</select></div>
    <div class="col-md-2"><label class="form-label small">Risiko</label><select name="tingkat_risiko" class="form-select form-select-sm"><option value="">Semua</option>@foreach(['rendah'=>'Rendah','sedang'=>'Sedang','tinggi'=>'Tinggi','sangat_tinggi'=>'Sangat Tinggi'] as $k=>$v)<option value="{{ $k }}" @if(request('tingkat_risiko')==$k)selected @endif>{{ $v }}</option>@endforeach</select></div>
    <div class="col-md-2"><label class="form-label small">Deadline</label><select name="deadline_status" class="form-select form-select-sm"><option value="">Semua</option><option value="lewat" @if(request('deadline_status')=='lewat')selected @endif>Lewat</option><option value="minggu_ini" @if(request('deadline_status')=='minggu_ini')selected @endif>7 Hari</option><option value="bulan_ini" @if(request('deadline_status')=='bulan_ini')selected @endif>30 Hari</option></select></div>
    <div class="col-md-1 d-grid"><button class="btn btn-primary btn-sm"><i class="bi bi-search"></i></button></div>
</form></div></div>

<div class="card"><div class="card-body">
    <div class="table-responsive"><table class="table table-hover datatable">
        <thead><tr><th>No</th><th>OPD</th><th>Audit</th><th>Temuan</th><th>Rekomendasi</th><th>Deadline</th><th>Hari</th><th>Progress</th><th>Status</th></tr></thead>
        <tbody>@forelse($items as $i => $r)<tr>
            <td>{{ $items->firstItem() + $i }}</td>
            <td class="small">{{ $r->finding?->opd?->nama }}</td>
            <td class="small">{{ $r->finding?->audit?->nomor }}</td>
            <td class="small"><a href="{{ route('finding.show', $r->finding) }}">{{ Str::limit($r->finding?->judul, 30) }}</a></td>
            <td class="small"><a href="{{ route('recommendation.show', $r) }}">#{{ $r->nomor }} {{ Str::limit($r->isi, 40) }}</a></td>
            <td class="small">{{ $r->deadline?->format('d/m/Y') }}</td>
            <td>@php $sisa = now()->startOfDay()->diffInDays($r->deadline, false); @endphp<span class="badge bg-{{ $sisa<0?'danger':($sisa<=7?'warning':'success') }}">{{ $sisa<0?'Lewat '.abs($sisa).'h':$sisa.'h' }}</span></td>
            <td style="min-width:110px"><div class="progress" style="height:16px"><div class="progress-bar bg-{{ $r->persentase>=100?'success':($r->persentase>=50?'warning':'danger') }}" style="width:{{ $r->persentase }}%">{{ $r->persentase }}%</div></div></td>
            <td>{!! $r->status_badge !!}</td>
        </tr>@empty<tr><td colspan="9" class="text-center py-4 text-muted">Tidak ada data</td></tr>@endforelse</tbody>
    </table></div>
    <div class="d-flex justify-content-center">{{ $items->withQueryString()->links() }}</div>
</div></div>
@endsection
