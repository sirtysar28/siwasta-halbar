<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - {{ config('app.name') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #1a56db;
            --sidebar-width: 260px;
            --sidebar-bg: #1e293b;
            --sidebar-hover: #334155;
        }
        body { font-family: 'Inter', system-ui, -apple-system, sans-serif; background: #f1f5f9; color: #1e293b; }
        .sidebar { width: var(--sidebar-width); height: 100vh; background: var(--sidebar-bg); position: fixed; top: 0; left: 0; z-index: 1030; transition: transform .3s; overflow-y: auto; overflow-x: hidden; }
        .sidebar::-webkit-scrollbar { width: 5px; }
        .sidebar::-webkit-scrollbar-track { background: transparent; }
        .sidebar::-webkit-scrollbar-thumb { background: #475569; border-radius: 10px; }
        .sidebar .brand { padding: 1.2rem 1.5rem; border-bottom: 1px solid #334155; }
        .sidebar .brand h5 { color: #ffffff; margin: 0; font-size: 1.1rem; font-weight: 700; letter-spacing: .02em; }
        .sidebar .brand small { color: #94a3b8; font-size: .72rem; }
        .sidebar .nav-link { color: #cbd5e1; padding: .65rem 1.5rem; border-radius: .4rem; margin: 2px 8px; display: flex; align-items: center; gap: .7rem; font-size: .9rem; }
        .sidebar .nav-link:hover { background: var(--sidebar-hover); color: #fff; }
        .sidebar .nav-link.active { background: var(--primary-color); color: #fff; }
        .sidebar .nav-link i { font-size: 1.05rem; width: 22px; text-align: center; }
        .sidebar .nav-header { color: #64748b; font-size: .7rem; text-transform: uppercase; letter-spacing: .08em; padding: 1rem 1.5rem .3rem; font-weight: 700; }
        .main-content { margin-left: var(--sidebar-width); padding: 0; }
        .topbar { background: #fff; padding: .75rem 1.5rem; border-bottom: 1px solid #e2e8f0; position: sticky; top: 0; z-index: 1020; display: flex; align-items: center; justify-content: space-between; }
        .content { padding: 1.5rem; }
        .card { border: none; border-radius: .65rem; box-shadow: 0 1px 3px rgba(0,0,0,.06); }
        .stat-card { transition: transform .2s; }
        .stat-card:hover { transform: translateY(-3px); }
        .notification-dropdown { min-width: 360px; max-height: 400px; overflow-y: auto; }
        .notification-item { padding: .75rem 1rem; border-bottom: 1px solid #f1f5f9; }
        .notification-item.unread { background: #eff6ff; }
        .table thead th { font-size: .8rem; text-transform: uppercase; letter-spacing: .03em; color: #475569; border-bottom: 2px solid #e2e8f0; font-weight: 600; }
        .table td { vertical-align: middle; }
        .btn-group-sm > .btn, .btn-sm { font-size: .8rem; }
        .avatar-sm { width: 36px; height: 36px; border-radius: 50%; object-fit: cover; }
        .progress { height: 8px; border-radius: 10px; }
        .toggle-btn { background: none; border: none; font-size: 1.3rem; color: #475569; }
        /* ===== Footer nav 4 ikon (khusus mobile, setelah login) ===== */
        .mobile-bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 64px;
            background: rgba(255,255,255,.97);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-top: 1px solid #e2e8f0;
            box-shadow: 0 -4px 16px rgba(0,0,0,.08);
            display: flex;
            z-index: 1015;
            padding-bottom: env(safe-area-inset-bottom, 0px);
        }
        .mobile-nav-item {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 3px;
            color: #64748b;
            text-decoration: none;
            font-size: .62rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .03em;
            transition: color .15s;
        }
        .mobile-nav-item i { font-size: 1.25rem; color: #64748b; }
        .mobile-nav-item:active,
        .mobile-nav-item.active { color: var(--primary-color); }
        .mobile-nav-item:active i,
        .mobile-nav-item.active i { color: var(--primary-color); }
        @media (max-width: 991.98px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.show { transform: translateX(0); }
            .main-content { margin-left: 0; }
            .content { padding: 1.25rem 1rem calc(80px + env(safe-area-inset-bottom, 0px)); }
        }
    </style>
    @stack('styles')
</head>
<body>
    <!-- Sidebar -->
    <nav class="sidebar" id="sidebar">
        <div class="brand text-center">
            <img src="{{ asset('images/logo.png') }}" alt="SIWASTA Logo" style="width: 48px; height: 48px; object-fit: contain; margin-bottom: 6px;">
            <h5 class="mb-0">SIWASTA</h5>
            <small>Pengawasan & Tindak Lanjut<br>Kab. Halmahera Barat</small>
        </div>
        <div class="mt-2">
            @include('partials.sidebar')
        </div>
    </nav>

    <!-- Main -->
    <div class="main-content">
        <!-- Topbar -->
        <div class="topbar">
            <div class="d-flex align-items-center gap-3">
                <button class="toggle-btn d-lg-none" onclick="document.getElementById('sidebar').classList.toggle('show')"><i class="bi bi-list"></i></button>
                <h6 class="mb-0 text-secondary">@yield('page-title', 'Dashboard')</h6>
            </div>
            <div class="d-flex align-items-center gap-2">
                <!-- Reminders -->
                <a href="{{ route('early-warning.index') }}" class="btn btn-sm btn-outline-warning position-relative" title="Early Warning">
                    <i class="bi bi-bell"></i>
                </a>
                <!-- Notifications -->
                <div class="dropdown">
                    <button class="btn btn-sm btn-outline-secondary position-relative" data-bs-toggle="dropdown">
                        <i class="bi bi-bell-fill"></i>
                        @php $unread = auth()->user()?->unreadNotificationsCount() ?? 0; @endphp
                        @if($unread > 0)
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size:.6rem">{{ $unread > 9 ? '9+' : $unread }}</span>
                        @endif
                    </button>
                    <div class="dropdown-menu dropdown-menu-end notification-dropdown p-0">
                        <div class="d-flex justify-content-between align-items-center p-2 border-bottom">
                            <strong>Notifikasi</strong>
                            <a href="{{ route('notifications.read-all') }}" class="btn btn-sm btn-link p-0" onclick="event.preventDefault(); this.nextElementSibling?.click()">
                                Tandai dibaca
                            </a>
                        </div>
                        @php
                            $notifs = \App\Models\NotificationCustom::where(function($q){$q->where('user_id', auth()->id())->orWhere('is_broadcast', true);})
                                ->latest()->limit(5)->get();
                        @endphp
                        @forelse($notifs as $n)
                            <a href="{{ route('notifications.read', $n) }}" class="notification-item text-decoration-none d-block {{ $n->dibaca ? '' : 'unread' }} text-dark">
                                <div class="d-flex gap-2">
                                    <i class="bi {{ $n->tipe_icon }}"></i>
                                    <div class="flex-grow-1">
                                        <div class="fw-semibold small">{{ $n->judul }}</div>
                                        <div class="text-muted" style="font-size:.75rem">{{ Str::limit($n->pesan, 60) }}</div>
                                        <small class="text-secondary">{{ $n->created_at->diffForHumans() }}</small>
                                    </div>
                                </div>
                            </a>
                        @empty
                            <div class="text-center text-muted py-4"><i class="bi bi-inbox fs-2 d-block"></i>Tidak ada notifikasi</div>
                        @endforelse
                        <div class="text-center border-top p-2">
                            <a href="{{ route('notifications.index') }}" class="btn btn-sm btn-link">Lihat Semua</a>
                        </div>
                    </div>
                </div>
                <!-- Profile -->
                <div class="dropdown">
                    <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" data-bs-toggle="dropdown">
                        <img src="{{ auth()->user()->avatar_url }}" class="avatar-sm" alt="">
                        <div class="ms-2 d-none d-md-block">
                            <div class="fw-semibold" style="font-size:.85rem; color:#1e293b">{{ auth()->user()->name }}</div>
                            <div class="text-muted" style="font-size:.72rem">{{ auth()->user()->role_name }}</div>
                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="{{ route('profile') }}"><i class="bi bi-person me-2"></i>Profil Saya</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="bi bi-box-arrow-right me-2"></i>Keluar</a></li>
                    </ul>
                </div>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
            </div>
        </div>

        <!-- Content -->
        <main class="content">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show"><i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
            @endif
            @yield('content')
        </main>
    </div>

    {{-- ===== Footer 4 ikon (khusus tampilan mobile, setelah login) ===== --}}
    <nav class="mobile-bottom-nav d-lg-none">
        <a href="{{ route('dashboard') }}" class="mobile-nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="bi bi-speedometer2"></i>
            <span>Dashboard</span>
        </a>
        <a href="{{ route('monitoring.index') }}" class="mobile-nav-item {{ request()->routeIs('monitoring.*') ? 'active' : '' }}">
            <i class="bi bi-graph-up-arrow"></i>
            <span>Monitoring</span>
        </a>
        <a href="{{ route('early-warning.index') }}" class="mobile-nav-item {{ request()->routeIs('early-warning.*') ? 'active' : '' }}">
            <i class="bi bi-exclamation-triangle"></i>
            <span>Warning</span>
        </a>
        <a href="{{ route('laporan.index') }}" class="mobile-nav-item {{ request()->routeIs('laporan.*') ? 'active' : '' }}">
            <i class="bi bi-file-earmark-bar-graph"></i>
            <span>Laporan</span>
        </a>
    </nav>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        window.csrf = '{{ csrf_token() }}';
        $(function () {
            $('.datatable').DataTable({
                pageLength: 10, responsive: true,
                language: { url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/id.json' }
            });
            $('.select2').select2({ theme: 'bootstrap-5', width: '100%' });
        });
        function confirmDelete(formId) {
            if (confirm('Apakah Anda yakin ingin menghapus data ini?')) document.getElementById(formId).submit();
        }
    </script>
    @stack('scripts')
</body>
</html>
