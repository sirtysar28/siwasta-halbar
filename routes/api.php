<?php

use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\DashboardApiController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    // Auth
    Route::post('/login', [AuthApiController::class, 'login']);
    Route::post('/register', [AuthApiController::class, 'register']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthApiController::class, 'logout']);
        Route::get('/profile', [AuthApiController::class, 'profile']);
        Route::post('/refresh', [AuthApiController::class, 'refresh']);

        // Dashboard
        Route::get('/dashboard/statistik', [DashboardApiController::class, 'statistik']);
        Route::get('/dashboard/grafik', [DashboardApiController::class, 'grafik']);
        Route::get('/dashboard/progress', [DashboardApiController::class, 'progress']);

        // Master Data
        Route::apiResource('opd', \App\Http\Controllers\Api\OpdApiController::class);
        Route::apiResource('auditor', \App\Http\Controllers\Api\AuditorApiController::class);
        Route::apiResource('jenis-audit', \App\Http\Controllers\Api\JenisAuditApiController::class);
        Route::apiResource('tahun-audit', \App\Http\Controllers\Api\TahunAuditApiController::class);
        Route::apiResource('kategori-temuan', \App\Http\Controllers\Api\KategoriTemuanApiController::class);

        // Audit
        Route::apiResource('audit', \App\Http\Controllers\Api\AuditApiController::class);
        Route::post('/audit/{audit}/dokumen', [\App\Http\Controllers\Api\AuditApiController::class, 'uploadDocument']);

        // Temuan
        Route::apiResource('temuan', \App\Http\Controllers\Api\FindingApiController::class)->parameters(['temuan' => 'finding']);

        // Rekomendasi
        Route::apiResource('rekomendasi', \App\Http\Controllers\Api\RecommendationApiController::class)->parameters(['rekomendasi' => 'recommendation']);

        // Tindak Lanjut
        Route::apiResource('tindak-lanjut', \App\Http\Controllers\Api\FollowUpApiController::class)->parameters(['tindak-lanjut' => 'follow_up']);
        Route::post('/tindak-lanjut/{follow_up}/bukti', [\App\Http\Controllers\Api\FollowUpApiController::class, 'uploadBukti']);

        // Verifikasi
        Route::post('/tindak-lanjut/{follow_up}/verifikasi', [\App\Http\Controllers\Api\FollowUpApiController::class, 'verify']);

        // Dokumen
        Route::get('/dokumen', [\App\Http\Controllers\Api\DocumentApiController::class, 'index']);
        Route::get('/dokumen/{document}/download', [\App\Http\Controllers\Api\DocumentApiController::class, 'download']);

        // Notifikasi
        Route::get('/notifikasi', [\App\Http\Controllers\Api\NotificationApiController::class, 'index']);
        Route::post('/notifikasi/{id}/read', [\App\Http\Controllers\Api\NotificationApiController::class, 'markAsRead']);

        // Monitoring
        Route::get('/monitoring', [\App\Http\Controllers\Api\MonitoringApiController::class, 'index']);

        // Laporan
        Route::get('/laporan/{type}', [\App\Http\Controllers\Api\LaporanApiController::class, 'index']);
    });
});
