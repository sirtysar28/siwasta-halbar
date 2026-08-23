@extends('layouts.app')
@section('title', 'Jenis Audit')
@section('page-title', 'Master Jenis Audit')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0 fw-bold"><i class="bi bi-tag me-2"></i>Jenis Audit</h5>
    <a href="{{ route('master.jenis-audit.create') }}" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg me-1"></i>Tambah</a>
</div>
<div class="card"><div class="card-body">
    <div class="table-responsive"><table class="table table-hover datatable">
        <thead><tr><th>No</th><th>Kode</th><th>Nama</th><th>Keterangan</th><th>Status</th><th>Aksi</th></tr></thead>
        <tbody>@forelse($items as $i => $j)<tr>
            <td>{{ $items->firstItem() + $i }}</td><td><span class="badge bg-primary">{{ $j->kode }}</span></td><td>{{ $j->nama }}</td><td class="small text-muted">{{ $j->keterangan ?? '-' }}</td>
            <td>@if($j->is_active)<span class="badge bg-success">Aktif</span>@else<span class="badge bg-danger">Nonaktif</span>@endif</td>
            <td><a href="{{ route('master.jenis-audit.edit', $j) }}" class="btn btn-sm btn-outline-warning"><i class="bi bi-pencil"></i></a>
                <form action="{{ route('master.jenis-audit.destroy', $j) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus data?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button></form></td>
        </tr>@empty<tr><td colspan="6" class="text-center py-4 text-muted">Belum ada data</td></tr>@endforelse</tbody>
    </table></div>
    <div class="d-flex justify-content-center">{{ $items->withQueryString()->links() }}</div>
</div></div>
@endsection
