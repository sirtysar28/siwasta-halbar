@php
    $user = auth()->user();
    $isAdmin = $user->hasRole(['Super Administrator', 'Administrator']);
    $isInspektorat = $user->hasRole(['Super Administrator', 'Administrator', 'Inspektorat', 'Auditor', 'Verifikator']);
    $canManage = $user->hasRole(['Super Administrator', 'Administrator', 'Inspektorat', 'Kepala OPD', 'Operator OPD']);
@endphp

<div class="nav flex-column pb-4">
    <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
        <i class="bi bi-speedometer2"></i> Dashboard
    </a>

    @if($isInspektorat)
        <a class="nav-link {{ request()->routeIs('audit.*') ? 'active' : '' }}" href="{{ route('audit.index') }}">
            <i class="bi bi-clipboard-check"></i> Manajemen Audit
        </a>
    @endif

    @if($user->hasRole(['Super Administrator', 'Administrator', 'Inspektorat', 'Auditor', 'Operator OPD']))
        <a class="nav-link {{ request()->routeIs('finding.*') ? 'active' : '' }}" href="{{ route('finding.index') }}">
            <i class="bi bi-search"></i> Temuan
        </a>
    @endif

    <a class="nav-link {{ request()->routeIs('follow-up.*') ? 'active' : '' }}" href="{{ route('follow-up.index') }}">
        <i class="bi bi-arrow-repeat"></i> Tindak Lanjut
    </a>

    <a class="nav-link {{ request()->routeIs('recommendation.*') ? 'active' : '' }} {{ request()->routeIs('finding.show') ? 'active' : '' }}" href="{{ route('follow-up.index') }}">
        <i class="bi bi-list-check"></i> Rekomendasi
    </a>

    <a class="nav-link {{ request()->routeIs('documents.*') ? 'active' : '' }}" href="{{ route('documents.index') }}">
        <i class="bi bi-folder"></i> Dokumen
    </a>

    <a class="nav-link {{ request()->routeIs('monitoring.*') ? 'active' : '' }}" href="{{ route('monitoring.index') }}">
        <i class="bi bi-graph-up"></i> Monitoring
    </a>

    <a class="nav-link {{ request()->routeIs('laporan.*') ? 'active' : '' }}" href="{{ route('laporan.index') }}">
        <i class="bi bi-file-earmark-bar-graph"></i> Laporan
    </a>

    <a class="nav-link {{ request()->routeIs('early-warning.*') ? 'active' : '' }}" href="{{ route('early-warning.index') }}">
        <i class="bi bi-alarm"></i> Early Warning
    </a>

    <a class="nav-link {{ request()->routeIs('notifications.*') ? 'active' : '' }}" href="{{ route('notifications.index') }}">
        <i class="bi bi-bell"></i> Notifikasi
    </a>

    @if($isAdmin)
        <div class="nav-header">Administrasi</div>
    @endif

    @if($isAdmin)
        <a class="nav-link {{ request()->routeIs('master.opd.*') ? 'active' : '' }}" href="{{ route('master.opd.index') }}">
            <i class="bi bi-building"></i> Master OPD
        </a>
        <a class="nav-link {{ request()->routeIs('master.auditor.*') ? 'active' : '' }}" href="{{ route('master.auditor.index') }}">
            <i class="bi bi-people"></i> Master Auditor
        </a>
        <a class="nav-link {{ request()->routeIs('master.jenis-audit.*') ? 'active' : '' }}" href="{{ route('master.jenis-audit.index') }}">
            <i class="bi bi-tag"></i> Jenis Audit
        </a>
        <a class="nav-link {{ request()->routeIs('master.tahun-audit.*') ? 'active' : '' }}" href="{{ route('master.tahun-audit.index') }}">
            <i class="bi bi-calendar3"></i> Tahun Audit
        </a>
        <a class="nav-link {{ request()->routeIs('master.kategori-temuan.*') ? 'active' : '' }}" href="{{ route('master.kategori-temuan.index') }}">
            <i class="bi bi-collection"></i> Kategori Temuan
        </a>
        <a class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}">
            <i class="bi bi-person-gear"></i> Manajemen User
        </a>
        <a class="nav-link {{ request()->routeIs('audit-trail.*') ? 'active' : '' }}" href="{{ route('audit-trail.index') }}">
            <i class="bi bi-clock-history"></i> Audit Trail
        </a>
    @endif
</div>
