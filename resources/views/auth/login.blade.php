<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - {{ config('app.name') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        * { font-family: 'Inter', system-ui, -apple-system, sans-serif; }
        body {
            background: linear-gradient(160deg, #0f172a 0%, #1e293b 40%, #0f172a 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            max-width: 960px;
            width: 95%;
            border-radius: 1.2rem;
            overflow: hidden;
            box-shadow: 0 25px 80px rgba(0,0,0,.5);
        }
        .login-banner {
            background: linear-gradient(160deg, rgba(30,64,175,.4), rgba(14,116,144,.3));
            backdrop-filter: blur(8px);
            color: #e2e8f0;
            padding: 3rem 2.5rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            min-height: 560px;
        }
        .login-banner .logo-img {
            width: 80px;
            height: 80px;
            object-fit: contain;
            margin-bottom: 1.5rem;
            /* filter: brightness(0) invert(1); */
            border-radius: 1rem;
        }
        .login-banner h2 { color: #ffffff; font-weight: 700; }
        .login-banner .subtitle { color: #94a3b8; }
        .login-banner .org-name { color: #64748b; font-size: .88rem; }
        .login-banner .features { font-size: .84rem; color: #cbd5e1; }
        .login-banner .features i { color: #38bdf8; }
        .login-form {
            padding: 3rem 2.5rem;
            background: #ffffff;
        }
        .login-form h4 { color: #0f172a; font-weight: 700; }
        .login-form .welcome-sub { color: #64748b; font-size: .9rem; }
        .form-label { color: #334155; font-weight: 500; }
        .form-control {
            padding: .7rem 1rem;
            border-radius: .5rem;
            border-color: #cbd5e1;
            color: #0f172a;
            font-size: .9rem;
        }
        .form-control::placeholder { color: #94a3b8; }
        .form-control:focus {
            box-shadow: 0 0 0 .2rem rgba(30,64,175,.15);
            border-color: #3b82f6;
            color: #0f172a;
        }
        .input-group-text { background: #f1f5f9; color: #475569; border-color: #cbd5e1; }
        .btn-primary {
            background: #1e40af;
            border: none;
            padding: .75rem;
            font-weight: 600;
            font-size: .95rem;
            border-radius: .5rem;
        }
        .btn-primary:hover { background: #1e3a8a; }
        .btn-outline-secondary { border-color: #cbd5e1; color: #64748b; }
        .form-check-label { color: #64748b; }
        .demo-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: .5rem;
        }
        .demo-box code { color: #1e40af; }
        .copyright { color: #94a3b8; font-size: .78rem; }
        @media (max-width: 767.98px) {
            .login-banner { min-height: auto; padding: 2rem 1.5rem; }
            .login-form { padding: 2rem 1.5rem; }
        }
    </style>
</head>
<body>
    <div class="login-card mx-auto row g-0">
        <!-- Banner -->
        <div class="col-md-6 login-banner">
            <img src="{{ asset('images/logo.png') }}" alt="SIWASTA Logo" class="logo-img">
            <h2 class="mb-2">SIWASTA-HALBAR</h2>
            <p class="subtitle mb-1">Sistem Informasi Pengawasan & Tindak Lanjut</p>
            <p class="org-name mb-4">Inspektorat Kabupaten Halmahera Barat</p>
            <div class="mt-auto features">
                <p class="mb-2"><i class="bi bi-check-circle-fill me-2"></i>Monitoring real-time tindak lanjut</p>
                <p class="mb-2"><i class="bi bi-check-circle-fill me-2"></i>Early Warning System otomatis</p>
                <p class="mb-2"><i class="bi bi-check-circle-fill me-2"></i>Verifikasi & validasi dokumen</p>
                <p class="mb-0"><i class="bi bi-check-circle-fill me-2"></i>Laporan & analisis komprehensif</p>
            </div>
        </div>

        <!-- Form -->
        <div class="col-md-6 login-form">
            <h4 class="fw-bold mb-1">Selamat Datang</h4>
            <p class="welcome-sub mb-4">Silakan masuk untuk melanjutkan</p>

            @if(session('success'))
                <div class="alert alert-success py-2" style="font-size:.85rem">{{ session('success') }}</div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger py-2" style="font-size:.85rem">
                    @foreach($errors->all() as $err)<div>{{ $err }}</div>@endforeach
                </div>
            @endif

            <form method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label small">Email</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="nama@email.com" autofocus>
                    </div>
                    @error('email')
                        <div class="invalid-feedback" style="font-size:.8rem">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label small">Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-lock"></i></span>
                        <input type="password" name="password" id="password" class="form-control" placeholder="••••••••">
                        <button class="btn btn-outline-secondary" type="button" onclick="const p=document.getElementById('password'); p.type = p.type==='password'?'text':'password'; this.querySelector('i').className = p.type==='password'?'bi bi-eye':'bi bi-eye-slash'"><i class="bi bi-eye"></i></button>
                    </div>
                </div>
                <div class="mb-4 form-check">
                    <input type="checkbox" name="remember" class="form-check-input" id="remember">
                    <label class="form-check-label small" for="remember">Ingat saya</label>
                </div>
                <button type="submit" class="btn btn-primary w-100 mb-3"><i class="bi bi-box-arrow-in-right me-2"></i>Masuk</button>
            </form>

            <div class="demo-box small mt-4 p-3">
                <strong style="color:#334155"><i class="bi bi-info-circle me-1"></i>Akun Demo:</strong><br>
                <code>admin@siwasta.go.id</code> / <code>password</code>
            </div>
            <p class="copyright text-center mt-3">&copy; {{ date('Y') }} Pemerintah Kab. Halmahera Barat</p>
        </div>
    </div>
</body>
</html>
