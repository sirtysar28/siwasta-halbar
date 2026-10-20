<?php

use App\Http\Controllers\AuditController;
use App\Http\Controllers\AuditTrailController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\EarlyWarningController;
use App\Http\Controllers\FindingController;
use App\Http\Controllers\FollowUpController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\Master\AuditorController;
use App\Http\Controllers\Master\JenisAuditController;
use App\Http\Controllers\Master\KategoriTemuanController;
use App\Http\Controllers\Master\OpdController;
use App\Http\Controllers\Master\TahunAuditController;
use App\Http\Controllers\MonitoringController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\RecommendationController;
use App\Http\Controllers\TerminalController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Guest
Route::middleware('guest')->group(function () {
    Route::get('/', fn() => redirect()->route('login'))->name('home');
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

// Public document verification via QR
Route::get('/dokumen/verify/{token}', [DocumentController::class, 'verifyQr'])->name('documents.verify');

// Authenticated
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile
    Route::get('/profil', [AuthController::class, 'profile'])->name('profile');
    Route::put('/profil', [AuthController::class, 'updateProfile'])->name('profile.update');
    Route::put('/profil/password', [AuthController::class, 'updatePassword'])->name('profile.password');

    // Master OPD
    Route::resource('master/opd', OpdController::class)->names([
        'index' => 'master.opd.index', 'create' => 'master.opd.create',
        'store' => 'master.opd.store', 'show' => 'master.opd.show',
        'edit' => 'master.opd.edit', 'update' => 'master.opd.update',
        'destroy' => 'master.opd.destroy',
    ])->parameters(['opd' => 'opd']);

    // Master Auditor
    Route::resource('master/auditor', AuditorController::class)->names([
        'index' => 'master.auditor.index', 'create' => 'master.auditor.create',
        'store' => 'master.auditor.store', 'show' => 'master.auditor.show',
        'edit' => 'master.auditor.edit', 'update' => 'master.auditor.update',
        'destroy' => 'master.auditor.destroy',
    ])->parameters(['auditor' => 'auditor']);

    // Master Jenis Audit
    Route::resource('master/jenis-audit', JenisAuditController::class)->except('show')->names([
        'index' => 'master.jenis-audit.index', 'create' => 'master.jenis-audit.create',
        'store' => 'master.jenis-audit.store', 'edit' => 'master.jenis-audit.edit',
        'update' => 'master.jenis-audit.update', 'destroy' => 'master.jenis-audit.destroy',
    ])->parameters(['jenis-audit' => 'jenisAudit']);

    // Master Tahun Audit
    Route::resource('master/tahun-audit', TahunAuditController::class)->except('create', 'show', 'edit')->names([
        'index' => 'master.tahun-audit.index', 'store' => 'master.tahun-audit.store',
        'update' => 'master.tahun-audit.update', 'destroy' => 'master.tahun-audit.destroy',
    ])->parameters(['tahun-audit' => 'tahunAudit']);

    // Master Kategori Temuan
    Route::resource('master/kategori-temuan', KategoriTemuanController::class)->except('create', 'show', 'edit')->names([
        'index' => 'master.kategori-temuan.index', 'store' => 'master.kategori-temuan.store',
        'update' => 'master.kategori-temuan.update', 'destroy' => 'master.kategori-temuan.destroy',
    ])->parameters(['kategori-temuan' => 'kategoriTemuan']);

    // Audit
    Route::resource('audit', AuditController::class)->parameters(['audit' => 'audit']);

    // Temuan (Finding)
    Route::resource('temuan', FindingController::class)->names([
        'index' => 'finding.index', 'create' => 'finding.create', 'store' => 'finding.store',
        'show' => 'finding.show', 'edit' => 'finding.edit', 'update' => 'finding.update',
        'destroy' => 'finding.destroy',
    ])->parameters(['temuan' => 'finding']);
    Route::post('/temuan/{finding}/dokumen', [FindingController::class, 'uploadDocument'])->name('finding.upload-document');

    // Rekomendasi
    Route::get('/rekomendasi/{recommendation}', [RecommendationController::class, 'show'])->name('recommendation.show');
    Route::post('/rekomendasi', [RecommendationController::class, 'store'])->name('recommendation.store');
    Route::put('/rekomendasi/{recommendation}', [RecommendationController::class, 'update'])->name('recommendation.update');
    Route::delete('/rekomendasi/{recommendation}', [RecommendationController::class, 'destroy'])->name('recommendation.destroy');

    // Tindak Lanjut
    Route::resource('tindak-lanjut', FollowUpController::class)->only(['index', 'show', 'destroy'])->names([
        'index' => 'follow-up.index', 'show' => 'follow-up.show', 'destroy' => 'follow-up.destroy',
    ])->parameters(['tindak-lanjut' => 'followUp']);
    Route::post('/tindak-lanjut', [FollowUpController::class, 'store'])->name('follow-up.store');
    Route::post('/tindak-lanjut/{followUp}/verifikasi', [FollowUpController::class, 'verify'])->name('follow-up.verify');

    // Dokumen
    Route::resource('dokumen', DocumentController::class)->only(['index', 'destroy'])->names([
        'index' => 'documents.index', 'destroy' => 'documents.destroy',
    ]);
    Route::get('/dokumen/{document}/download', [DocumentController::class, 'download'])->name('documents.download');

    // Monitoring
    Route::get('/monitoring', [MonitoringController::class, 'index'])->name('monitoring.index');
    Route::get('/monitoring/export/{format}', [MonitoringController::class, 'export'])->name('monitoring.export');

    // Laporan
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/grafik', [LaporanController::class, 'grafik'])->name('laporan.grafik');
    Route::get('/laporan/export/{type}/{format}', [LaporanController::class, 'export'])->name('laporan.export');

    // Early Warning
    Route::get('/early-warning', [EarlyWarningController::class, 'index'])->name('early-warning.index');
    Route::post('/early-warning/{reminder}/send', [EarlyWarningController::class, 'sendNow'])->name('early-warning.send');
    Route::post('/early-warning/overdue/send', [EarlyWarningController::class, 'sendOverdue'])->name('early-warning.overdue');

    // Notifikasi
    Route::resource('notifikasi', NotificationController::class)->except(['create', 'edit', 'update', 'store'])->names([
        'index' => 'notifications.index', 'show' => 'notifications.show',
        'destroy' => 'notifications.destroy',
    ])->parameters(['notifikasi' => 'notification']);
    Route::get('/notifikasi/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifikasi/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.read-all');
    Route::get('/broadcast', [NotificationController::class, 'broadcastForm'])->name('notifications.broadcast.form');
    Route::post('/broadcast', [NotificationController::class, 'broadcast'])->name('notifications.broadcast');

    // Audit Trail
    Route::get('/audit-trail', [AuditTrailController::class, 'index'])->name('audit-trail.index');
    Route::get('/audit-trail/{activity}', [AuditTrailController::class, 'show'])->name('audit-trail.show');

    // User Management (admin only)
    Route::middleware('role:Super Administrator|Administrator')->group(function () {
        Route::resource('manajemen-user', UserController::class)->parameters(['manajemen-user' => 'user'])->names([
            'index' => 'users.index', 'create' => 'users.create', 'store' => 'users.store',
            'show' => 'users.show', 'edit' => 'users.edit', 'update' => 'users.update',
            'destroy' => 'users.destroy',
        ]);
        Route::post('/manajemen-user/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');
        Route::get('/manajemen-user/{user}/login-activities', [UserController::class, 'loginActivities'])->name('users.login-activities');

        // Terminal (admin only)
        Route::get('/terminal', [TerminalController::class, 'index'])->name('terminal.index');
        Route::post('/terminal/run', [TerminalController::class, 'run'])->name('terminal.run');
    });
});
