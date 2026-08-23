@extends('layouts.app')
@section('title', 'Tindak Lanjut')
@section('page-title', 'Tindak Lanjut')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0 fw-bold"><i class="bi bi-arrow-repeat me-2"></i>Tindak Lanjut</h5>
    <div class="btn-group btn-group-sm">
        <a href="{{ route('follow-up.index', ['status' => '']) }}" class="btn btn-outline-secondary">Semua</a>
        <a href="{{ route('follow-up.index', ['status' => 'pending']) }}" class="btn btn-outline-warning">Pending</a>
        <a href="{{ route('follow-up.index', ['status' => 'disetujui']) }}" class="btn btn-outline-success">Disetujui</a>
        <a href="{{ route('follow-up.index', ['status' => 'revisi']) }}" class="btn btn-outline-info">Revisi</a>
    </div>
</div>

<div class="card"><div class="card-body">
    <div class="table-responsive"><table class="table table-hover datatable">
        <thead><tr><th>No</th><th>Tanggal</th><th>OPD</th><th>Rekomendasi</th><th>Uraian</th><th>%</th><th>Status</th><th>Aksi</th></tr></thead>
        <tbody>@forelse($items as $i => $fu)<tr>
            <td>{{ $items->firstItem() + $i }}</td>
            <td class="small">{{ $fu->tanggal->format('d/m/Y') }}</td>
            <td class="small">{{ $fu->recommendation?->finding?->opd?->nama ?? '-' }}</td>
            <td class="small">#{{ $fu->recommendation?->nomor }}</td>
            <td>{{ Str::limit($fu->uraian, 50) }}</td>
            <td><span class="badge bg-primary">{{ $fu->persentase }}%</span></td>
            <td>{!! $fu->status_badge !!}</td>
            <td nowrap>
                <a href="{{ route('follow-up.show', $fu) }}" class="btn btn-sm btn-outline-info"><i class="bi bi-eye"></i></a>
                @if($fu->status === 'pending' && auth()->user()->hasRole(['Verifikator','Inspektorat','Administrator','Super Administrator']))
                <a href="{{ route('follow-up.show', $fu) }}#verify" class="btn btn-sm btn-success"><i class="bi bi-check2"></i></a>
                @endif
                @if($fu->creator_id === auth()->id() && $fu->status === 'pending')
                <form action="{{ route('follow-up.destroy', $fu) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button></form>
                @endif
            </td>
        </tr>@empty<tr><td colspan="8" class="text-center py-4 text-muted">Belum ada data</td></tr>@endforelse</tbody>
    </table></div>
    <div class="d-flex justify-content-center">{{ $items->withQueryString()->links() }}</div>
</div></div>
@endsection
