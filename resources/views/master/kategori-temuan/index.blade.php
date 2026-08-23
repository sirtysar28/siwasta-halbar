@extends('layouts.app')
@section('title', 'Kategori Temuan')
@section('page-title', 'Master Kategori Temuan')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0 fw-bold"><i class="bi bi-collection me-2"></i>Kategori Temuan</h5>
</div>
<div class="card mb-3"><div class="card-header bg-white"><h6 class="mb-0 fw-bold">Tambah Kategori</h6></div><div class="card-body">
    <form method="POST" action="{{ route('master.kategori-temuan.store') }}">@csrf
        <div class="row g-2 align-items-end">
            <div class="col-md-2"><label class="form-label small">Kode</label><input type="text" name="kode" class="form-control" required></div>
            <div class="col-md-4"><label class="form-label small">Nama</label><input type="text" name="nama" class="form-control" placeholder="cth: Keuangan" required></div>
            <div class="col-md-5"><label class="form-label small">Keterangan</label><input type="text" name="keterangan" class="form-control"></div>
            <div class="col-md-1"><button class="btn btn-primary w-100"><i class="bi bi-plus-lg"></i></button></div>
        </div>
    </form>
</div></div>
<div class="card"><div class="card-body">
    <div class="table-responsive"><table class="table table-hover">
        <thead><tr><th>No</th><th>Kode</th><th>Nama</th><th>Keterangan</th><th>Jumlah Temuan</th><th>Aksi</th></tr></thead>
        <tbody>@forelse($items as $i => $k)<tr>
            <td>{{ $i + 1 }}</td><td><span class="badge bg-primary">{{ $k->kode }}</span></td><td>{{ $k->nama }}</td><td class="small">{{ $k->keterangan ?? '-' }}</td>
            <td><span class="badge bg-secondary">{{ $k->findings()->count() }}</span></td>
            <td>
                <button class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#edit-{{ $k->id }}"><i class="bi bi-pencil"></i></button>
                <form action="{{ route('master.kategori-temuan.destroy', $k) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button></form>
            </td>
        </tr>@empty<tr><td colspan="6" class="text-center py-4 text-muted">Belum ada data</td></tr>@endforelse</tbody>
    </table></div>
    <div class="d-flex justify-content-center">{{ $items->links() }}</div>
</div></div>

@foreach($items as $k)
<div class="modal fade" id="edit-{{ $k->id }}"><div class="modal-dialog"><div class="modal-content"><div class="modal-header"><h6 class="modal-title">Edit {{ $k->nama }}</h6><button class="btn-close" data-bs-dismiss="modal"></button></div>
    <form method="POST" action="{{ route('master.kategori-temuan.update', $k) }}">@csrf @method('PUT')
        <div class="modal-body">
            <div class="mb-3"><label class="form-label">Kode</label><input type="text" name="kode" value="{{ old('kode', $k->kode) }}" class="form-control" required></div>
            <div class="mb-3"><label class="form-label">Nama</label><input type="text" name="nama" value="{{ old('nama', $k->nama) }}" class="form-control" required></div>
            <div class="mb-3"><label class="form-label">Keterangan</label><input type="text" name="keterangan" value="{{ old('keterangan', $k->keterangan) }}" class="form-control"></div>
        </div>
        <div class="modal-footer"><button class="btn btn-primary">Simpan</button></div>
    </form></div></div></div>
@endforeach
@endsection
