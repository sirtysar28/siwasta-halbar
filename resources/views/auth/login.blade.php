<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
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

        /* ===== Captcha 5 digit (stabil) ===== */
        .captcha-box {
            display: inline-flex;
            align-items: center;
            gap: .55rem;
            background:
                repeating-linear-gradient(45deg, #eef2ff 0 6px, #e0e7ff 6px 12px);
            border: 1px dashed #94a3b8;
            border-radius: .5rem;
            padding: .5rem 1.05rem;
            font-family: 'Courier New', monospace;
            font-weight: 800;
            font-size: 1.4rem;
            letter-spacing: .1em;
            color: #1e3a8a;
            user-select: none;
            position: relative;
            overflow: hidden;
        }
        .captcha-box::after {
            content: '';
            position: absolute;
            left: -5%;
            right: -5%;
            top: 50%;
            height: 2px;
            background: rgba(30,58,138,.3);
            transform: rotate(-4deg);
        }
        .captcha-box span { display: inline-block; }
        .captcha-hint { color: #94a3b8; font-size: .72rem; }

        /* ===== Footer nav 4 ikon (khusus mobile) ===== */
        .mobile-bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 64px;
            background: rgba(15,23,42,.94);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-top: 1px solid #334155;
            display: flex;
            z-index: 1050;
            padding-bottom: env(safe-area-inset-bottom, 0px);
        }
        .mobile-nav-item {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 3px;
            color: #94a3b8;
            text-decoration: none;
            font-size: .62rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .03em;
        }
        .mobile-nav-item i { font-size: 1.25rem; color: #38bdf8; }
        .mobile-nav-item:active,
        .mobile-nav-item:hover { color: #ffffff; }

        /* ===== Responsive mobile ===== */
        @media (max-width: 767.98px) {
            body {
                padding: 1rem .75rem calc(80px + env(safe-area-inset-bottom, 0px));
                align-items: flex-start;
            }
            .login-card {
                width: 100%;
                border-radius: .9rem;
                box-shadow: 0 10px 40px rgba(0,0,0,.45);
            }
            .login-banner {
                min-height: auto;
                padding: 1.75rem 1.25rem 1.5rem;
                text-align: center;
            }
            .login-banner .logo-img {
                width: 58px;
                height: 58px;
                margin-bottom: .75rem;
            }
            .login-banner h2 { font-size: 1.45rem; }
            .login-banner .subtitle { font-size: .85rem; }
            .login-banner .features {
                font-size: .78rem;
                text-align: left;
                column-count: 2;
                column-gap: 1rem;
            }
            .login-banner .features p { margin-bottom: .45rem; break-inside: avoid; }
            .login-form { padding: 1.75rem 1.25rem; }
            .login-form h4 { font-size: 1.15rem; }
            .captcha-box { font-size: 1.25rem; padding: .45rem .9rem; }
        }
        @media (max-width: 400px) {
            body { padding-left: .5rem; padding-right: .5rem; }
            .login-banner { padding: 1.4rem 1rem 1.2rem; }
            .login-banner .features { column-count: 1; }
            .login-form { padding: 1.4rem 1rem; }
            .mobile-nav-item { font-size: .58rem; }
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

                {{-- ===== Captcha 5 digit (stabil) ===== --}}
                <div class="mb-3">
                    <label class="form-label small">Kode Keamanan (Captcha)</label>
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <div class="captcha-box" id="captchaBox" title="Kode captcha stabil">
                            @foreach(str_split($captchaCode ?? \App\Http\Controllers\AuthController::CAPTCHA_CODE) as $i => $digit)
                                <span style="transform: rotate({{ [-6, 4, -3, 5, -4][$i] ?? 0 }}deg)">{{ $digit }}</span>
                            @endforeach
                        </div>
                        <span class="captcha-hint">Masukkan 5 digit<br>di sebelah kiri</span>
                    </div>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-shield-lock"></i></span>
                        <input type="text" name="captcha" class="form-control @error('captcha') is-invalid @enderror" placeholder="Ketik 5 digit kode di atas" maxlength="5" inputmode="numeric" pattern="[0-9]{5}" autocomplete="off">
                    </div>
                    @error('captcha')
                        <div class="invalid-feedback d-block" style="font-size:.8rem">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4 form-check">
                    <input type="checkbox" name="remember" class="form-check-input" id="remember">
                    <label class="form-check-label small" for="remember">Ingat saya</label>
                </div>
                <button type="submit" class="btn btn-primary w-100 mb-3"><i class="bi bi-box-arrow-in-right me-2"></i>Masuk</button>
            </form>

            <div class="demo-box small mt-4 p-3">
                <strong style="color:#334155"><i class="bi bi-info-circle me-1"></i>Akun Demo:</strong><br>
                <code>admin@siwasta.go.id</code> / <code>password</code><br>
                <span class="text-secondary">Captcha: <code>{{ \App\Http\Controllers\AuthController::CAPTCHA_CODE }}</code></span>
            </div>
            <p class="copyright text-center mt-3 mb-0">&copy; {{ date('Y') }} Pemerintah Kab. Halmahera Barat</p>
        </div>
    </div>

    {{-- ===== Footer 4 ikon (khusus tampilan mobile) ===== --}}
    <nav class="mobile-bottom-nav d-md-none">
        <a href="{{ route('dashboard') }}" class="mobile-nav-item">
            <i class="bi bi-speedometer2"></i>
            <span>Dashboard</span>
        </a>
        <a href="{{ route('monitoring.index') }}" class="mobile-nav-item">
            <i class="bi bi-graph-up-arrow"></i>
            <span>Monitoring</span>
        </a>
        <a href="{{ route('early-warning.index') }}" class="mobile-nav-item">
            <i class="bi bi-exclamation-triangle"></i>
            <span>Warning</span>
        </a>
        <a href="{{ route('laporan.index') }}" class="mobile-nav-item">
            <i class="bi bi-file-earmark-bar-graph"></i>
            <span>Laporan</span>
        </a>
    </nav>
</body>
</html>
