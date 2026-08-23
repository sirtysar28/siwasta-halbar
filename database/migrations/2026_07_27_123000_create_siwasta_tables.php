<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ============ MASTER OPD ============
        Schema::create('opd', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique();
            $table->string('nama');
            $table->string('kepala')->nullable();
            $table->text('alamat')->nullable();
            $table->string('email')->nullable();
            $table->string('telepon')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Add foreign key for users.opd_id
        Schema::table('users', function (Blueprint $table) {
            $table->foreign('opd_id')->references('id')->on('opd')->nullOnDelete();
        });

        // ============ MASTER AUDITOR ============
        Schema::create('auditors', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('nip')->nullable();
            $table->string('jabatan')->nullable();
            $table->string('instansi')->nullable();
            $table->string('email')->nullable();
            $table->string('telepon')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // ============ MASTER JENIS AUDIT ============
        Schema::create('jenis_audit', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('kode')->unique();
            $table->text('keterangan')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // ============ MASTER TAHUN AUDIT ============
        Schema::create('tahun_audit', function (Blueprint $table) {
            $table->id();
            $table->integer('tahun')->unique();
            $table->text('keterangan')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // ============ MASTER KATEGORI TEMUAN ============
        Schema::create('kategori_temuan', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('kode')->unique();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        // ============ AUDIT ============
        Schema::create('audits', function (Blueprint $table) {
            $table->id();
            $table->string('nomor')->unique();
            $table->string('judul');
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai')->nullable();
            $table->foreignId('jenis_audit_id')->constrained('jenis_audit')->cascadeOnDelete();
            $table->foreignId('tahun_audit_id')->constrained('tahun_audit')->cascadeOnDelete();
            $table->foreignId('auditor_id')->constrained('auditors')->cascadeOnDelete();
            $table->string('instansi');
            $table->text('keterangan')->nullable();
            $table->enum('status', ['draft', 'berjalan', 'selesai', 'ditutup'])->default('berjalan');
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });

        // Pivot audit_opd
        Schema::create('audit_opd', function (Blueprint $table) {
            $table->id();
            $table->foreignId('audit_id')->constrained('audits')->cascadeOnDelete();
            $table->foreignId('opd_id')->constrained('opd')->cascadeOnDelete();
            $table->timestamps();
        });

        // ============ TEMUAN ============
        Schema::create('findings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('audit_id')->constrained('audits')->cascadeOnDelete();
            $table->string('nomor');
            $table->string('judul');
            $table->foreignId('opd_id')->constrained('opd')->cascadeOnDelete();
            $table->decimal('nilai', 20, 2)->default(0);
            $table->foreignId('kategori_temuan_id')->constrained('kategori_temuan')->cascadeOnDelete();
            $table->enum('tingkat_risiko', ['rendah', 'sedang', 'tinggi', 'sangat_tinggi'])->default('sedang');
            $table->longText('deskripsi')->nullable();
            $table->enum('status', ['baru', 'diproses', 'selesai', 'ditolak', 'revisi'])->default('baru');
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });

        // ============ REKOMENDASI ============
        Schema::create('recommendations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('finding_id')->constrained('findings')->cascadeOnDelete();
            $table->string('nomor');
            $table->longText('isi');
            $table->date('deadline');
            $table->string('pic')->nullable();
            $table->integer('persentase')->default(0);
            $table->enum('status', ['baru', 'diproses', 'selesai', 'revisi', 'ditolak'])->default('baru');
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });

        // ============ TINDAK LANJUT ============
        Schema::create('follow_ups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recommendation_id')->constrained('recommendations')->cascadeOnDelete();
            $table->date('tanggal');
            $table->longText('uraian');
            $table->integer('persentase')->default(0);
            $table->text('catatan')->nullable();
            $table->enum('status', ['pending', 'disetujui', 'revisi', 'ditolak'])->default('pending');
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });

        // ============ DOKUMEN (Polymorphic) ============
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->morphs('documentable');
            $table->enum('kategori', ['surat', 'bukti_foto', 'laporan', 'ba', 'nota_dinas', 'file_pendukung'])->default('file_pendukung');
            $table->string('nama_asli');
            $table->string('nama_file');
            $table->string('path');
            $table->string('extensi', 10);
            $table->bigInteger('ukuran')->default(0);
            $table->integer('versi')->default(1);
            $table->string('tanda_tangan_digital')->nullable();
            $table->string('qr_code_token', 100)->nullable()->unique();
            $table->foreignId('uploaded_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });

        // ============ VERIFIKASI / APPROVAL ============
        Schema::create('approvals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('follow_up_id')->constrained('follow_ups')->cascadeOnDelete();
            $table->foreignId('verifikator_id')->constrained('users')->cascadeOnDelete();
            $table->enum('status', ['disetujui', 'revisi', 'ditolak']);
            $table->text('catatan')->nullable();
            $table->timestamps();
        });

        // ============ KOMENTAR (Polymorphic) ============
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->morphs('commentable');
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('comments')->cascadeOnDelete();
            $table->longText('isi');
            $table->timestamps();
        });

        // ============ NOTIFIKASI ============
        Schema::create('notifications_custom', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->string('tipe')->default('info'); // info, reminder, warning, success, danger
            $table->string('judul');
            $table->text('pesan');
            $table->string('url')->nullable();
            $table->boolean('dibaca')->default(false);
            $table->boolean('is_broadcast')->default(false);
            $table->timestamp('dibaca_pada')->nullable();
            $table->timestamps();
        });

        // ============ REMINDER (Early Warning) ============
        Schema::create('reminders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recommendation_id')->constrained('recommendations')->cascadeOnDelete();
            $table->integer('hari_sebelum'); // 30, 14, 7, 3, 1, 0
            $table->date('tanggal_kirim');
            $table->boolean('is_sent')->default(false);
            $table->timestamp('sent_at')->nullable();
            $table->string('media')->default('email'); // email, whatsapp, telegram, sms, push
            $table->timestamps();
        });

        // ============ LOGIN ACTIVITY ============
        Schema::create('login_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('ip_address');
            $table->string('device');
            $table->string('browser');
            $table->string('platform');
            $table->timestamp('login_at');
            $table->timestamp('logout_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['opd_id']);
        });
        Schema::dropIfExists('login_activities');
        Schema::dropIfExists('reminders');
        Schema::dropIfExists('notifications_custom');
        Schema::dropIfExists('comments');
        Schema::dropIfExists('approvals');
        Schema::dropIfExists('documents');
        Schema::dropIfExists('follow_ups');
        Schema::dropIfExists('recommendations');
        Schema::dropIfExists('findings');
        Schema::dropIfExists('audit_opd');
        Schema::dropIfExists('audits');
        Schema::dropIfExists('kategori_temuan');
        Schema::dropIfExists('tahun_audit');
        Schema::dropIfExists('jenis_audit');
        Schema::dropIfExists('auditors');
        Schema::dropIfExists('opd');
    }
};
