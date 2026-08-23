@extends('layouts.app')
@section('title', 'Manajemen Dokumen')
@section('page-title', 'Repository Dokumen')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0 fw-bold"><i class="bi bi-folder me-2"></i>Repository Dokumen</h5>
</div>

<div class="card mb-3"><div class="card-body"><form method="GET" class="row g-2 align-items-end">
    <div class="col-md-5"><label class="form-label small">Cari</label><input type="text" name="q" value="{{ request('q') }}" class="form-control form-control-sm" placeholder="Nama file..."></div>
    <div class="col-md-3"><label class="form-label small">Kategori</label><select name="kategori" class="form-select form-select-sm"><option value="">Semua</option>@foreach(['surat'=>'Surat','bukti_foto'=>'Bukti Foto','laporan'=>'Laporan','ba'=>'Berita Acara','nota_dinas'=>'Nota Dinas','file_pendukung'=>'File Pendukung'] as $k=>$v)<option value="{{ $k }}" @if(request('kategori')==$k)selected @endif>{{ $v }}</option>@endforeach</select></div>
    <div class="col-md-2"><label class="form-label small">Ekstensi</label><input type="text" name="extensi" value="{{ request('extensi') }}" class="form-control form-control-sm" placeholder="pdf"></div>
    <div class="col-md-2 d-grid"><button class="btn btn-primary btn-sm"><i class="bi bi-search me-1"></i>Filter</button></div>
</form></div></div>

<div class="card"><div class="card-body">
    <div class="table-responsive"><table class="table table-hover" id="table-dokumen">
        <thead>
            <tr>
                <th>No</th>
                <th>File</th>
                <th>Kategori</th>
                <th>Ukuran</th>
                <th>Versi</th>
                <th>Uploader</th>
                <th>Tanggal</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tfoot>
            <tr>
                <th>No</th>
                <th>File</th>
                <th>Kategori</th>
                <th>Ukuran</th>
                <th>Versi</th>
                <th>Uploader</th>
                <th>Tanggal</th>
                <th>Aksi</th>
            </tr>
        </tfoot>
        <tbody>
            @forelse($items as $i => $d)
            <tr>
                <td>{{ $items->firstItem() + $i }}</td>
                <td><i class="bi {{ $d->icon }} text-primary me-1"></i>{{ $d->nama_asli }}</td>
                <td><span class="badge bg-secondary">{{ str_replace('_',' ',ucfirst($d->kategori)) }}</span></td>
                <td class="small">{{ $d->ukuran_human }}</td>
                <td>v{{ $d->versi }}</td>
                <td class="small">{{ optional($d->uploader)->name ?? '-' }}</td>
                <td class="small">{{ optional($d->created_at)->format('d/m/Y') ?? '-' }}</td>
                <td class="text-nowrap">
                    <a href="{{ route('documents.download', $d) }}" class="btn btn-sm btn-outline-primary" title="Download"><i class="bi bi-download"></i></a>
                    <a href="{{ route('documents.verify', $d->qr_code_token) }}" target="_blank" class="btn btn-sm btn-outline-info" title="Validasi QR"><i class="bi bi-qr-code"></i></a>
                    <button type="button" class="btn btn-sm btn-outline-danger" title="Hapus" onclick="confirmDeleteDoc({{ $d->id }})"><i class="bi bi-trash"></i></button>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="text-center py-4 text-muted">Belum ada dokumen</td>
            </tr>
            @endforelse
        </tbody>
    </table></div>
    <div class="d-flex justify-content-center mt-3">{{ $items->withQueryString()->links() }}</div>
</div></div>

<!-- Hidden delete form (replaces inline form that breaks DataTables) -->
<form id="delete-doc-form" method="POST" style="display:none">
    @csrf @method('DELETE')
</form>

@push('scripts')
<script>
function confirmDeleteDoc(id) {
    if (confirm('Hapus dokumen ini?')) {
        var form = document.getElementById('delete-doc-form');
        form.action = '/dokumen/' + id;
        form.submit();
    }
}
$(function() {
    // Destroy existing DataTable if already initialized (fixes re-init on pagination)
    if ($.fn.DataTable.isDataTable('#table-dokumen')) {
        $('#table-dokumen').DataTable().destroy();
    }
    $('#table-dokumen').DataTable({
        pageLength: 10,
        responsive: false,
        autoWidth: false,
        columnDefs: [
            { orderable: false, targets: [7] },
            { searchable: false, targets: [0, 7] }
        ],
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
        }
    });
});
</script>
@endpush
@endsection
