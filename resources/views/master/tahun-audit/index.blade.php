@extends('layouts.app')
@section('title', 'Tahun Audit')
@section('page-title', 'Master Tahun Audit')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0 fw-bold"><i class="bi bi-calendar3 me-2"></i>Tahun Audit</h5>
</div>
<div class="card mb-3"><div class="card-header bg-white"><h6 class="mb-0 fw-bold">Tambah Tahun Audit</h6></div><div class="card-body">
    <form method="POST" action="{{ route('master.tahun-audit.store') }}">@csrf
        <div class="row g-2 align-items-end">
            <div class="col-md-3"><label class="form-label small">Tahun</label><input type="number" name="tahun" value="{{ date('Y') }}" class="form-control" required></div>
            <div class="col-md-6"><label class="form-label small">Keterangan</label><input type="text" name="keterangan" class="form-control" placeholder="cth: Audit tahun anggaran"></div>
            <div class="col-md-3"><button class="btn btn-primary w-100"><i class="bi bi-plus-lg me-1"></i>Tambah</button></div>
        </div>
    </form>
</div></div>
<div class="card"><div class="card-body">
    <div class="table-responsive"><table class="table table-hover">
        <thead><tr><th>No</th><th>Tahun</th><th>Keterangan</th><th>Status</th><th>Aksi</th></tr></thead>
        <tbody>@forelse($items as $i => $t)<tr>
            <td>{{ $i + 1 }}</td><td><strong>{{ $t->tahun }}</strong></td><td>{{ $t->keterangan ?? '-' }}</td>
            <td>@if($t->is_active)<span class="badge bg-success">Aktif</span>@else<span class="badge bg-secondary">Nonaktif</span>@endif</td>
            <td>
                <button class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#edit-{{ $t->id }}"><i class="bi bi-pencil"></i></button>
                <form action="{{ route('master.tahun-audit.destroy', $t) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button></form>
            </td>
        </tr>@empty<tr><td colspan="5" class="text-center py-4 text-muted">Belum ada data</td></tr>@endforelse</tbody>
    </table></div>
    <div class="d-flex justify-content-center">{{ $items->links() }}</div>
</div></div>

@foreach($items as $t)
<div class="modal fade" id="edit-{{ $t->id }}"><div class="modal-dialog"><div class="modal-content"><div class="modal-header"><h6 class="modal-title">Edit Tahun {{ $t->tahun }}</h6><button class="btn-close" data-bs-dismiss="modal"></button></div>
    <form method="POST" action="{{ route('master.tahun-audit.update', $t) }}">@csrf @method('PUT')
        <div class="modal-body"><div class="mb-3"><label class="form-label">Tahun</label><input type="number" name="tahun" value="{{ old('tahun', $t->tahun) }}" class="form-control" required></div>
        <div class="mb-3"><label class="form-label">Keterangan</label><input type="text" name="keterangan" value="{{ old('keterangan', $t->keterangan) }}" class="form-control"></div>
        <div class="form-check"><input type="checkbox" name="is_active" value="1" class="form-check-input" id="t{{ $t->id }}" @if(old('is_active', $t->is_active))checked @endif><label class="form-check-label" for="t{{ $t->id }}">Aktif</label></div></div>
        <div class="modal-footer"><button class="btn btn-primary">Simpan</button></div>
    </form></div></div></div>
@endforeach
@endsection
