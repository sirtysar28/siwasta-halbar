@extends('layouts.app')
@section('title', 'Early Warning System')
@section('page-title', 'Early Warning System')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0 fw-bold"><i class="bi bi-alarm me-2"></i>Early Warning System</h5>
    <form method="POST" action="{{ route('early-warning.overdue') }}">@csrf<button class="btn btn-danger btn-sm"><i class="bi bi-send me-1"></i>Kirim Notifikasi Lewat Deadline</button></form>
</div>

<div class="row g-3 mb-3">
    <div class="col-md-6"><div class="card border-warning"><div class="card-body d-flex justify-content-between align-items-center"><div><div class="text-muted small">Reminder Hari Ini</div><h3 class="fw-bold mb-0 text-warning">{{ $todayCount }}</h3></div><i class="bi bi-clock-history text-warning" style="font-size:2.5rem"></i></div></div></div>
    <div class="col-md-6"><div class="card border-danger"><div class="card-body d-flex justify-content-between align-items-center"><div><div class="text-muted small">Lewat Deadline</div><h3 class="fw-bold mb-0 text-danger">{{ $overdueCount }}</h3></div><i class="bi bi-exclamation-octagon text-danger" style="font-size:2.5rem"></i></div></div></div>
</div>

<div class="card"><div class="card-header bg-white"><h6 class="mb-0 fw-bold">Daftar Reminder Terjadwal</h6></div><div class="card-body">
    <div class="table-responsive"><table class="table table-hover datatable">
        <thead><tr><th>No</th><th>OPD</th><th>Rekomendasi</th><th>Deadline</th><th>Hari Sebelum</th><th>Tgl Kirim</th><th>Status</th><th>Aksi</th></tr></thead>
        <tbody>@forelse($items as $i => $r)<tr>
            <td>{{ $items->firstItem() + $i }}</td>
            <td class="small">{{ $r->recommendation?->finding?->opd?->nama }}</td>
            <td class="small"><a href="{{ route('recommendation.show', $r->recommendation) }}">#{{ $r->recommendation?->nomor }} {{ Str::limit($r->recommendation?->isi, 40) }}</a></td>
            <td class="small">{{ $r->recommendation?->deadline?->format('d/m/Y') }}</td>
            <td>@if($r->hari_sebelum==0)<span class="badge bg-danger">Hari H</span>@else<span class="badge bg-info text-dark">{{ $r->hari_sebelum }} hari</span>@endif</td>
            <td class="small">{{ $r->tanggal_kirim->format('d/m/Y') }}</td>
            <td>@if($r->is_sent)<span class="badge bg-success"><i class="bi bi-check"></i> Terkirim</span>@else<span class="badge bg-secondary">Pending</span>@endif</td>
            <td>@if(!$r->is_sent)<form method="POST" action="{{ route('early-warning.send', $r) }}">@csrf<button class="btn btn-sm btn-primary"><i class="bi bi-send"></i> Kirim</button></form>@endif</td>
        </tr>@empty<tr><td colspan="8" class="text-center py-4 text-muted">Tidak ada reminder</td></tr>@endforelse</tbody>
    </table></div>
    <div class="d-flex justify-content-center">{{ $items->withQueryString()->links() }}</div>
</div></div>

<div class="alert alert-info mt-3"><i class="bi bi-info-circle me-2"></i><strong>Konfigurasi Media:</strong> Saat ini reminder menggunakan Email. Dapat dikonfigurasi ke WhatsApp Gateway, Telegram Bot, SMS Gateway, atau Push Notification melalui file <code>.env</code>.</div>
@endsection
