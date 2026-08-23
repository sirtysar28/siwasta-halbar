@extends('layouts.app')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard Pimpinan')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <div>
        <h4 class="mb-0 fw-bold">Selamat Datang, {{ auth()->user()->name }} 👋</h4>
        <p class="text-muted mb-0 small">Ringkasan pengawasan dan tindak lanjut per {{ now()->format('d F Y') }}</p>
    </div>
    <form class="d-flex gap-2">
        <select name="tahun" class="form-select form-select-sm" onchange="this.form.submit()">
            @foreach($listTahun as $id => $thn)
                <option value="{{ $thn }}" @if((string)$tahun === (string)$thn) selected @endif>{{ $thn }}</option>
            @endforeach
        </select>
    </form>
</div>

{{-- KPI Cards --}}
<div class="row g-3 mb-3">
    <div class="col-md-3 col-sm-6">
        <div class="card stat-card border-start border-4 border-primary">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small">Total Temuan</div>
                    <h3 class="fw-bold mb-0">{{ number_format($totalTemuan) }}</h3>
                </div>
                <div class="text-primary"><i class="bi bi-search" style="font-size:2rem"></i></div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="card stat-card border-start border-4 border-info">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small">Total Rekomendasi</div>
                    <h3 class="fw-bold mb-0">{{ number_format($totalRekomendasi) }}</h3>
                </div>
                <div class="text-info"><i class="bi bi-list-check" style="font-size:2rem"></i></div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="card stat-card border-start border-4 border-success">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small">Sudah Selesai</div>
                    <h3 class="fw-bold mb-0 text-success">{{ number_format($sudahSelesai) }}</h3>
                </div>
                <div class="text-success"><i class="bi bi-check-circle" style="font-size:2rem"></i></div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="card stat-card border-start border-4 border-danger">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small">Lewat Deadline</div>
                    <h3 class="fw-bold mb-0 text-danger">{{ number_format($lewatDeadline) }}</h3>
                </div>
                <div class="text-danger"><i class="bi bi-exclamation-octagon" style="font-size:2rem"></i></div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-3">
    <div class="col-md-3 col-sm-6">
        <div class="card stat-card"><div class="card-body">
            <div class="d-flex justify-content-between"><span class="text-muted small">Dalam Proses</span><i class="bi bi-hourglass-split text-warning"></i></div>
            <h5 class="fw-bold mt-1 mb-0">{{ number_format($dalamProses) }}</h5>
        </div></div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="card stat-card"><div class="card-body">
            <div class="d-flex justify-content-between"><span class="text-muted small">Belum Ditindaklanjuti</span><i class="bi bi-clock text-secondary"></i></div>
            <h5 class="fw-bold mt-1 mb-0">{{ number_format($belumDitindaklanjuti) }}</h5>
        </div></div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="card stat-card"><div class="card-body">
            <div class="d-flex justify-content-between"><span class="text-muted small">Total Audit</span><i class="bi bi-clipboard-check text-primary"></i></div>
            <h5 class="fw-bold mt-1 mb-0">{{ number_format($totalAudit) }}</h5>
        </div></div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="card stat-card"><div class="card-body">
            <div class="d-flex justify-content-between"><span class="text-muted small">Progress Keseluruhan</span><i class="bi bi-graph-up-arrow text-success"></i></div>
            <div class="d-flex align-items-center gap-2 mt-1">
                <div class="progress flex-grow-1"><div class="progress-bar bg-success" style="width: {{ $progress }}%"></div></div>
                <strong>{{ $progress }}%</strong>
            </div>
        </div></div>
    </div>
</div>

{{-- Charts Row 1 --}}
<div class="row g-3 mb-3">
    <div class="col-lg-5">
        <div class="card h-100"><div class="card-header bg-white"><h6 class="mb-0 fw-bold"><i class="bi bi-pie-chart me-2 text-primary"></i>Status Rekomendasi</h6></div>
            <div class="card-body"><canvas id="statusChart" height="220"></canvas></div></div>
    </div>
    <div class="col-lg-7">
        <div class="card h-100"><div class="card-header bg-white"><h6 class="mb-0 fw-bold"><i class="bi bi-bar-chart me-2 text-primary"></i>Top 10 OPD (Jumlah Temuan)</h6></div>
            <div class="card-body"><canvas id="opdChart" height="220"></canvas></div></div>
    </div>
</div>

{{-- Charts Row 2 --}}
<div class="row g-3 mb-3">
    <div class="col-lg-6">
        <div class="card h-100"><div class="card-header bg-white d-flex justify-content-between"><h6 class="mb-0 fw-bold"><i class="bi bi-graph-up me-2 text-primary"></i>Grafik Bulanan {{ $tahun }}</h6></div>
            <div class="card-body"><canvas id="bulananChart" height="200"></canvas></div></div>
    </div>
    <div class="col-lg-6">
        <div class="card h-100"><div class="card-header bg-white"><h6 class="mb-0 fw-bold"><i class="bi bi-calendar2-stats me-2 text-primary"></i>Grafik Tahunan</h6></div>
            <div class="card-body"><canvas id="tahunanChart" height="200"></canvas></div></div>
    </div>
</div>

{{-- Top OPD + Reminder --}}
<div class="row g-3 mb-3">
    <div class="col-lg-6">
        <div class="card h-100"><div class="card-header bg-white"><h6 class="mb-0 fw-bold"><i class="bi bi-trophy me-2 text-success"></i>Top 10 OPD Tercepat</h6></div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead><tr><th>#</th><th>OPD</th><th>Progress</th></tr></thead>
                        <tbody>
                        @forelse($opdTercepat as $i => $opd)
                            <tr><td>{{ $loop->iteration }}</td><td class="small">{{ $opd->nama }}</td>
                            <td style="min-width:140px"><div class="progress" style="height:18px"><div class="progress-bar bg-success" style="width:{{ round($opd->avg_progress) }}%">{{ round($opd->avg_progress) }}%</div></div></td></tr>
                        @empty
                            <tr><td colspan="3" class="text-center text-muted py-3">Belum ada data</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div></div>
    </div>
    <div class="col-lg-6">
        <div class="card h-100"><div class="card-header bg-white"><h6 class="mb-0 fw-bold"><i class="bi bi-exclamation-triangle me-2 text-danger"></i>Top 10 OPD Terlambat</h6></div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead><tr><th>#</th><th>OPD</th><th>Total Lewat</th></tr></thead>
                        <tbody>
                        @forelse($opdTerlambat as $opd)
                            <tr><td>{{ $loop->iteration }}</td><td class="small">{{ $opd->nama }}</td>
                            <td><span class="badge bg-danger">{{ $opd->total_late }}</span></td></tr>
                        @empty
                            <tr><td colspan="3" class="text-center text-muted py-3">Tidak ada yang terlambat 🎉</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div></div>
    </div>
</div>

{{-- Reminder mendatang + Activity --}}
<div class="row g-3">
    <div class="col-lg-6">
        <div class="card h-100"><div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h6 class="mb-0 fw-bold"><i class="bi bi-alarm me-2 text-warning"></i>Reminder Mendatang (7 Hari)</h6>
            <span class="badge bg-warning text-dark">{{ $reminderHariIni }} hari ini</span>
        </div>
        <div class="card-body p-0">
            <div class="list-group list-group-flush">
                @forelse($reminderList as $r)
                    <a href="{{ route('recommendation.show', $r) }}" class="list-group-item list-group-item-action">
                        <div class="d-flex w-100 justify-content-between">
                            <h6 class="mb-1 small fw-bold">#{{ $r->nomor }} - {{ $r->finding->opd->nama }}</h6>
                            <small class="text-{{ $r->warna_status }}">{{ $r->deadline->diffForHumans() }}</small>
                        </div>
                        <p class="mb-0 text-muted" style="font-size:.78rem">{{ Str::limit($r->isi, 80) }}</p>
                    </a>
                @empty
                    <div class="text-center text-muted py-4"><i class="bi bi-check-circle fs-2"></i><p class="mt-2 mb-0">Tidak ada reminder</p></div>
                @endforelse
            </div>
        </div></div>
    </div>
    <div class="col-lg-6">
        <div class="card h-100"><div class="card-header bg-white"><h6 class="mb-0 fw-bold"><i class="bi bi-activity me-2 text-primary"></i>Aktivitas Terbaru</h6></div>
        <div class="card-body p-0">
            <div class="list-group list-group-flush">
                @forelse($aktivitasTerbaru as $act)
                    <div class="list-group-item">
                        <div class="d-flex">
                            <img src="{{ $act->causer?->avatar_url ?? 'https://ui-avatars.com/api/?name=System' }}" class="rounded-circle me-2" width="32" height="32">
                            <div class="flex-grow-1">
                                <div style="font-size:.85rem"><strong>{{ $act->causer?->name ?? 'Sistem' }}</strong> {{ strtolower($act->description) }}</div>
                                <small class="text-muted">{{ $act->created_at->diffForHumans() }}</small>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-muted py-4">Belum ada aktivitas</div>
                @endforelse
            </div>
        </div></div>
    </div>
</div>

@endsection

@push('scripts')
<script>
const STATUS_COLORS = { selesai:'#16a34a', diproses:'#f59e0b', baru:'#94a3b8', lewat:'#dc2626' };
new Chart(document.getElementById('statusChart'), {
    type: 'doughnut',
    data: { labels:['Selesai','Dalam Proses','Belum','Lewat Deadline'], datasets:[{ data:[{{ $statusChart['selesai'] }},{{ $statusChart['diproses'] }},{{ $statusChart['baru'] }},{{ $statusChart['lewat'] }}], backgroundColor:Object.values(STATUS_COLORS) }]},
    options:{ plugins:{ legend:{ position:'bottom' } }, cutout:'60%' }
});
new Chart(document.getElementById('opdChart'), {
    type:'bar',
    data:{ labels:@json($perOpdLabels), datasets:[{ label:'Jumlah Temuan', data:@json($perOpdData), backgroundColor:'#1e40af' }]},
    options:{ plugins:{ legend:{display:false} }, scales:{ y:{ beginAtZero:true } } }
});
new Chart(document.getElementById('bulananChart'), {
    type:'line',
    data:{ labels:['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'], datasets:[{ label:'Rekomendasi', data:@json($bulanan), borderColor:'#0ea5e9', backgroundColor:'rgba(14,165,233,.1)', fill:true, tension:.4 }]},
    options:{ plugins:{ legend:{display:false} }, scales:{ y:{ beginAtZero:true } } }
});
new Chart(document.getElementById('tahunanChart'), {
    type:'bar',
    data:{ labels:@json($tahunList), datasets:[{ label:'Rekomendasi', data:@json($tahunan), backgroundColor:'#16a34a' }]},
    options:{ plugins:{ legend:{display:false} }, scales:{ y:{ beginAtZero:true } } }
});
</script>
@endpush
