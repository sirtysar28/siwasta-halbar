<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Validasi Dokumen - {{ config('app.name') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        * { font-family: 'Inter', system-ui, sans-serif; }
        body { background: #f1f5f9; color: #1e293b; }
        .verify-logo { width: 64px; height: 64px; object-fit: contain; margin-bottom: 1rem; }
        h4 { color: #0f172a; }
    </style>
</head>
<body>
<div class="container py-5">
    <div class="row justify-content-center"><div class="col-lg-7">
        <div class="card shadow"><div class="card-body p-4 text-center">
            <img src="{{ asset('images/logo.png') }}" alt="SIWASTA Logo" class="verify-logo">
            <h4 class="fw-bold text-success">Dokumen Valid ✓</h4>
            <p class="text-muted">Dokumen ini telah terdaftar di sistem {{ config('app.name') }}</p>
            <hr>
            <div class="text-start">
                <dl class="row mb-0">
                    <dt class="col-4 text-muted">Nama File</dt><dd class="col-8 fw-semibold" style="color:#1e293b">{{ $document->nama_asli }}</dd>
                    <dt class="col-4 text-muted">Kategori</dt><dd class="col-8"><span class="badge bg-primary">{{ str_replace('_',' ',ucfirst($document->kategori)) }}</span></dd>
                    <dt class="col-4 text-muted">Versi</dt><dd class="col-8" style="color:#334155">v{{ $document->versi }}</dd>
                    <dt class="col-4 text-muted">Diunggah</dt><dd class="col-8" style="color:#334155">{{ $document->created_at->format('d F Y H:i') }}</dd>
                    <dt class="col-4 text-muted">Token QR</dt><dd class="col-8 small"><code>{{ $document->qr_code_token }}</code></dd>
                </dl>
            </div>
            <hr>
            <div class="small text-muted">Sistem Informasi Pengawasan & Tindak Lanjut<br>Inspektorat Kabupaten Halmahera Barat</div>
        </div></div>
    </div></div>
</div>
</body>
</html>
