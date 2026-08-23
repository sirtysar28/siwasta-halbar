@extends('layouts.app')
@section('title', 'Master OPD')
@section('page-title', 'Master Data OPD')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0 fw-bold"><i class="bi bi-building me-2"></i>Master OPD</h5>
    <a href="{{ route('master.opd.create') }}" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg me-1"></i>Tambah OPD</a>
</div>

<div class="card mb-3">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-6"><label class="form-label small">Cari</label><input type="text" name="q" value="{{ request('q') }}" class="form-control form-control-sm" placeholder="Nama / Kode / Kepala OPD..."></div>
            <div class="col-md-3"><label class="form-label small">Status</label>
                <select name="status" class="form-select form-select-sm"><option value="">Semua</option><option value="1" @if(request('status')==='1')selected @endif>Aktif</option><option value="0" @if(request('status')==='0')selected @endif>Nonaktif</option></select></div>
            <div class="col-md-3"><button class="btn btn-primary btn-sm w-100"><i class="bi bi-search me-1"></i>Filter</button>
                <a href="{{ route('master.opd.index') }}" class="btn btn-outline-secondary btn-sm w-100 mt-1">Reset</a></div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover datatable">
                <thead><tr><th width="50">No</th><th>Kode</th><th>Nama OPD</th><th>Kepala</th><th>Kontak</th><th>Status</th><th>Aksi</th></tr></thead>
                <tbody>
                @forelse($items as $i => $opd)
                    <tr>
                        <td>{{ $items->firstItem() + $i }}</td>
                        <td><span class="badge bg-light text-dark">{{ $opd->kode }}</span></td>
                        <td><a href="{{ route('master.opd.show', $opd) }}" class="text-decoration-none fw-semibold">{{ $opd->nama }}</a></td>
                        <td>{{ $opd->kepala ?? '-' }}</td>
                        <td class="small">{{ $opd->telepon ?? '-' }}<br><span class="text-muted">{{ $opd->email ?? '-' }}</span></td>
                        <td>@if($opd->is_active) <span class="badge bg-success">Aktif</span> @else <span class="badge bg-danger">Nonaktif</span> @endif</td>
                        <td>
                            <a href="{{ route('master.opd.show', $opd) }}" class="btn btn-sm btn-outline-info"><i class="bi bi-eye"></i></a>
                            <a href="{{ route('master.opd.edit', $opd) }}" class="btn btn-sm btn-outline-warning"><i class="bi bi-pencil"></i></a>
                            <form action="{{ route('master.opd.destroy', $opd) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus OPD ini?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button></form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center py-4 text-muted">Belum ada data OPD</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-center">{{ $items->withQueryString()->links() }}</div>
    </div>
</div>
@endsection
