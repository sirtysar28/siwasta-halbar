@extends('layouts.app')
@section('title', 'Grafik Laporan')
@section('page-title', 'Grafik & Analitik')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0 fw-bold"><i class="bi bi-bar-chart me-2"></i>Grafik & Analitik</h5>
    <form class="d-flex gap-2"><select name="tahun" class="form-select form-select-sm" onchange="this.form.submit()">@foreach($tahuns as $t=>$l)<option value="{{ $t }}" @if((string)$tahun===(string)$t)selected @endif>{{ $t }}</option>@endforeach</select></form>
</div>

<div class="row g-3 mb-3">
    <div class="col-lg-6"><div class="card h-100"><div class="card-header bg-white"><h6 class="mb-0 fw-bold">Rekomendasi per Bulan ({{ $tahun }})</h6></div><div class="card-body"><canvas id="bulanan" height="120"></canvas></div></div></div>
    <div class="col-lg-6"><div class="card h-100"><div class="card-header bg-white"><h6 class="mb-0 fw-bold">Status Rekomendasi</h6></div><div class="card-body"><canvas id="status" height="120"></canvas></div></div></div>
</div>
<div class="row g-3 mb-3">
    <div class="col-lg-7"><div class="card h-100"><div class="card-header bg-white"><h6 class="mb-0 fw-bold">Temuan per Kategori</h6></div><div class="card-body"><canvas id="kategori" height="120"></canvas></div></div></div>
    <div class="col-lg-5"><div class="card h-100"><div class="card-header bg-white"><h6 class="mb-0 fw-bold">Tren 5 Tahun Terakhir</h6></div><div class="card-body"><canvas id="tahunan" height="120"></canvas></div></div></div>
</div>
<div class="card"><div class="card-header bg-white"><h6 class="mb-0 fw-bold">Temuan per OPD (Top 15)</h6></div><div class="card-body"><canvas id="perOpd" height="60"></canvas></div></div>

@push('scripts')
<script>
new Chart(document.getElementById('bulanan'),{type:'bar',data:{labels:['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'],datasets:[{label:'Rekomendasi',data:@json($bulanan),backgroundColor:'#1e40af'}]}});
new Chart(document.getElementById('status'),{type:'pie',data:{labels:['Selesai','Diproses','Baru','Revisi'],datasets:[{data:[{{ $perStatus['selesai'] }},{{ $perStatus['diproses'] }},{{ $perStatus['baru'] }},{{ $perStatus['revisi'] }}],backgroundColor:['#16a34a','#f59e0b','#94a3b8','#0ea5e9']}]},options:{plugins:{legend:{position:'bottom'}}}});
new Chart(document.getElementById('kategori'),{type:'doughnut',data:{labels:@json($perKategori->pluck('nama')),datasets:[{data:@json($perKategori->pluck('findings_count')),backgroundColor:['#1e40af','#0ea5e9','#16a34a','#f59e0b','#dc2626','#8b5cf6','#ec4899']}]}});
new Chart(document.getElementById('tahunan'),{type:'line',data:{labels:@json($tahunan->pluck('tahun')),datasets:[{label:'Audit',data:@json($tahunan->pluck('audit')),borderColor:'#1e40af'},{label:'Temuan',data:@json($tahunan->pluck('temuan')),borderColor:'#dc2626'}]}});
new Chart(document.getElementById('perOpd'),{type:'bar',data:{labels:@json($perOpd->pluck('nama')),datasets:[{label:'Temuan',data:@json($perOpd->pluck('findings_count')),backgroundColor:'#0ea5e9'}]},options:{indexAxis:'y',plugins:{legend:{display:false}}}});
</script>
@endpush
@endsection
