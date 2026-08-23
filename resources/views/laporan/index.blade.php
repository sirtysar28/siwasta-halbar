@extends('layouts.app')
@section('title', 'Laporan')
@section('page-title', 'Laporan & Rekap')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <h5 class="mb-0 fw-bold"><i class="bi bi-file-earmark-bar-graph me-2"></i>Laporan & Rekap</h5>
    <a href="{{ route('laporan.grafik') }}" class="btn btn-outline-primary btn-sm"><i class="bi bi-bar-chart me-1"></i>Grafik Laporan</a>
</div>

<div class="card mb-3"><div class="card-body"><form method="GET" class="row g-2 align-items-end">
    <div class="col-md-3"><label class="form-label small">Jenis Laporan</label><select name="type" class="form-select form-select-sm" onchange="this.form.submit()">
        <option value="audit" @if($type=='audit')selected @endif>Rekap Audit</option>
        <option value="temuan" @if($type=='temuan')selected @endif>Rekap Temuan</option>
        <option value="rekomendasi" @if($type=='rekomendasi')selected @endif>Rekap Rekomendasi</option>
        <option value="opd" @if($type=='opd')selected @endif>Rekap OPD</option>
        <option value="auditor" @if($type=='auditor')selected @endif>Rekap Auditor</option>
        <option value="deadline" @if($type=='deadline')selected @endif>Rekap Deadline</option>
        <option value="tindak_lanjut" @if($type=='tindak_lanjut')selected @endif>Rekap Tindak Lanjut</option>
    </select></div>
    <div class="col-md-3"><label class="form-label small">Tahun</label><select name="tahun" class="form-select form-select-sm"><option value="">Semua</option>@foreach($tahuns as $t=>$label)<option value="{{ $t }}" @if((string)$tahun===(string)$t)selected @endif>{{ $t }}</option>@endforeach</select></div>
    <div class="col-md-3"><label class="form-label small">OPD @if($type=='temuan'||$type=='tindak_lanjut')@endif</label><select name="opd_id" class="form-select form-select-sm"><option value="">Semua OPD</option>@foreach($opds as $o)<option value="{{ $o->id }}" @if(request('opd_id')==$o->id)selected @endif>{{ $o->nama }}</option>@endforeach</select></div>
    <div class="col-md-3 d-flex gap-1"><button class="btn btn-primary btn-sm flex-grow-1"><i class="bi bi-search me-1"></i>Tampilkan</button></div>
</form></div></div>

<div class="d-flex gap-2 mb-3">
    <a href="{{ route('laporan.export', ['type'=>$type,'format'=>'pdf']) }}" class="btn btn-danger btn-sm"><i class="bi bi-file-earmark-pdf me-1"></i>Export PDF</a>
    <a href="{{ route('laporan.export', ['type'=>$type,'format'=>'excel']) }}" class="btn btn-success btn-sm"><i class="bi bi-file-earmark-excel me-1"></i>Export Excel</a>
    <a href="javascript:window.print()" class="btn btn-outline-primary btn-sm"><i class="bi bi-printer me-1"></i>Print</a>
</div>

<div class="card"><div class="card-body">
    <h6 class="fw-bold mb-3">Laporan {{ ucfirst(str_replace('_',' ',$type)) }} @if($tahun)Tahun {{ $tahun }}@endif</h6>
    <div class="table-responsive"><table class="table table-sm table-bordered">
        @if($type=='audit')
        <thead><tr><th>Nomor</th><th>Judul</th><th>Jenis</th><th>Tahun</th><th>Auditor</th><th>Instansi</th><th>Tanggal</th><th>Status</th><th>Temuan</th></tr></thead>
        <tbody>@forelse($data as $a)<tr><td>{{ $a->nomor }}</td><td>{{ $a->judul }}</td><td>{{ $a->jenisAudit?->nama }}</td><td>{{ $a->tahunAudit?->tahun }}</td><td>{{ $a->auditor?->nama }}</td><td>{{ $a->instansi }}</td><td>{{ $a->tanggal_mulai?->format('d/m/Y') }}</td><td>{{ ucfirst($a->status) }}</td><td>{{ $a->findings->count() }}</td></tr>@empty<tr><td colspan="9" class="text-center text-muted py-3">Tidak ada data</td></tr>@endforelse</tbody>
        @elseif($type=='temuan')
        <thead><tr><th>Nomor</th><th>Judul</th><th>OPD</th><th>Kategori</th><th>Nilai</th><th>Risiko</th><th>Status</th><th>Progress</th></tr></thead>
        <tbody>@forelse($data as $f)<tr><td>{{ $f->nomor }}</td><td>{{ $f->judul }}</td><td>{{ $f->opd?->nama }}</td><td>{{ $f->kategoriTemuan?->nama }}</td><td>Rp {{ number_format($f->nilai) }}</td><td>{{ ucfirst($f->tingkat_risiko) }}</td><td>{{ ucfirst($f->status) }}</td><td>{{ $f->progress_rata }}%</td></tr>@empty<tr><td colspan="8" class="text-center text-muted py-3">Tidak ada data</td></tr>@endforelse</tbody>
        @elseif($type=='rekomendasi')
        <thead><tr><th>Nomor</th><th>OPD</th><th>Rekomendasi</th><th>Deadline</th><th>PIC</th><th>Progress</th><th>Status</th></tr></thead>
        <tbody>@forelse($data as $r)<tr><td>{{ $r->nomor }}</td><td>{{ $r->finding?->opd?->nama }}</td><td>{{ Str::limit($r->isi,60) }}</td><td>{{ $r->deadline?->format('d/m/Y') }}</td><td>{{ $r->pic }}</td><td>{{ $r->persentase }}%</td><td>{{ ucfirst($r->status) }}</td></tr>@empty<tr><td colspan="7" class="text-center text-muted py-3">Tidak ada data</td></tr>@endforelse</tbody>
        @elseif($type=='opd')
        <thead><tr><th>Kode</th><th>OPD</th><th>Kepala</th><th>Total Temuan</th><th>Selesai</th><th>Progress</th></tr></thead>
        <tbody>@forelse($data as $o)<tr><td>{{ $o->kode }}</td><td>{{ $o->nama }}</td><td>{{ $o->kepala }}</td><td>{{ $o->findings->count() }}</td><td>{{ $o->findings->flatMap->recommendations->where('status','selesai')->count() }}</td><td>{{ $o->findings->flatMap->recommendations->count()>0 ? round($o->findings->flatMap->recommendations->avg('persentase')) : 0 }}%</td></tr>@empty<tr><td colspan="6" class="text-center text-muted py-3">Tidak ada data</td></tr>@endforelse</tbody>
        @elseif($type=='auditor')
        <thead><tr><th>Nama</th><th>Jabatan</th><th>Instansi</th><th>Total Audit</th></tr></thead>
        <tbody>@forelse($data as $au)<tr><td>{{ $au->nama }}</td><td>{{ $au->jabatan }}</td><td>{{ $au->instansi }}</td><td>{{ $au->audits->count() }}</td></tr>@empty<tr><td colspan="4" class="text-center text-muted py-3">Tidak ada data</td></tr>@endforelse</tbody>
        @elseif($type=='deadline')
        <thead><tr><th>Nomor</th><th>OPD</th><th>Deadline</th><th>Hari</th><th>Progress</th><th>Status</th></tr></thead>
        <tbody>@forelse($data as $r)<tr><td>{{ $r->nomor }}</td><td>{{ $r->finding?->opd?->nama }}</td><td>{{ $r->deadline?->format('d/m/Y') }}</td><td>{{ now()->startOfDay()->diffInDays($r->deadline,false) }}</td><td>{{ $r->persentase }}%</td><td>{{ ucfirst($r->status) }}</td></tr>@empty<tr><td colspan="6" class="text-center text-success py-3">Semua tepat waktu 🎉</td></tr>@endforelse</tbody>
        @elseif($type=='tindak_lanjut')
        <thead><tr><th>Tanggal</th><th>OPD</th><th>Uraian</th><th>%</th><th>Status</th><th>Verifikator</th></tr></thead>
        <tbody>@forelse($data as $fu)<tr><td>{{ $fu->tanggal?->format('d/m/Y') }}</td><td>{{ $fu->recommendation?->finding?->opd?->nama }}</td><td>{{ Str::limit($fu->uraian,50) }}</td><td>{{ $fu->persentase }}%</td><td>{{ ucfirst($fu->status) }}</td><td>{{ $fu->approvals->first()?->verifikator?->name ?? '-' }}</td></tr>@empty<tr><td colspan="6" class="text-center text-muted py-3">Tidak ada data</td></tr>@endforelse</tbody>
        @endif
    </table></div>
    <p class="text-muted small mt-2">Total: {{ $data->count() }} record</p>
</div></div>
@endsection
