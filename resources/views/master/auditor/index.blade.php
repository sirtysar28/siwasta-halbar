@extends('layouts.app')
@section('title', 'Master Auditor')
@section('page-title', 'Master Data Auditor')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0 fw-bold"><i class="bi bi-people me-2"></i>Master Auditor</h5>
    <a href="{{ route('master.auditor.create') }}" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg me-1"></i>Tambah Auditor</a>
</div>

<div class="card mb-3"><div class="card-body"><form method="GET" class="d-flex gap-2"><input type="text" name="q" value="{{ request('q') }}" class="form-control form-control-sm" placeholder="Cari nama / instansi / email..."><button class="btn btn-primary btn-sm"><i class="bi bi-search"></i></button><a href="{{ route('master.auditor.index') }}" class="btn btn-outline-secondary btn-sm">Reset</a></form></div></div>

<div class="card"><div class="card-body">
    <div class="table-responsive"><table class="table table-hover datatable">
        <thead><tr><th>No</th><th>Nama</th><th>NIP</th><th>Jabatan</th><th>Instansi</th><th>Kontak</th><th>Status</th><th>Aksi</th></tr></thead>
        <tbody>@forelse($items as $i => $a)<tr>
            <td>{{ $items->firstItem() + $i }}</td>
            <td><a href="{{ route('master.auditor.show', $a) }}" class="fw-semibold text-decoration-none">{{ $a->nama }}</a></td>
            <td>{{ $a->nip ?? '-' }}</td><td>{{ $a->jabatan ?? '-' }}</td><td>{{ $a->instansi ?? '-' }}</td>
            <td class="small">{{ $a->email ?? '-' }}<br><span class="text-muted">{{ $a->telepon ?? '-' }}</span></td>
            <td>@if($a->is_active) <span class="badge bg-success">Aktif</span> @else <span class="badge bg-danger">Nonaktif</span> @endif</td>
            <td>
                <a href="{{ route('master.auditor.show', $a) }}" class="btn btn-sm btn-outline-info"><i class="bi bi-eye"></i></a>
                <a href="{{ route('master.auditor.edit', $a) }}" class="btn btn-sm btn-outline-warning"><i class="bi bi-pencil"></i></a>
                <form action="{{ route('master.auditor.destroy', $a) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus data?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button></form>
            </td>
        </tr>@empty<tr><td colspan="8" class="text-center py-4 text-muted">Belum ada data</td></tr>@endforelse</tbody>
    </table></div>
    <div class="d-flex justify-content-center">{{ $items->withQueryString()->links() }}</div>
</div></div>
@endsection
