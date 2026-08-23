<?php

namespace Database\Seeders;

use App\Models\Audit;
use App\Models\Approval;
use App\Models\Auditor;
use App\Models\Document;
use App\Models\Finding;
use App\Models\FollowUp;
use App\Models\JenisAudit;
use App\Models\KategoriTemuan;
use App\Models\NotificationCustom;
use App\Models\Opd;
use App\Models\Recommendation;
use App\Models\Reminder;
use App\Models\TahunAudit;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolesAndPermissionsSeeder::class,
            MasterDataSeeder::class,
            DemoDataSeeder::class,
        ]);
    }
}

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            'Super Administrator', 'Administrator', 'Auditor', 'Inspektorat',
            'Kepala OPD', 'Operator OPD', 'Verifikator', 'Pimpinan', 'Guest',
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
        }

        $permissions = [
            'view dashboard', 'manage audit', 'manage temuan', 'manage rekomendasi',
            'manage tindak lanjut', 'verifikasi tindak lanjut', 'manage dokumen',
            'manage master data', 'manage users', 'view laporan', 'view monitoring',
            'manage notifikasi', 'view audit trail',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        // Super Admin gets all permissions
        Role::where('name', 'Super Administrator')->first()->givePermissionTo(Permission::all());
        Role::where('name', 'Administrator')->first()->givePermissionTo(Permission::all());
    }
}

class MasterDataSeeder extends Seeder
{
    public function run(): void
    {
        // OPD
        $opds = [
            ['kode' => 'OPD-001', 'nama' => 'Dinas Pendidikan', 'kepala' => 'Drs. Anton Tumbol, M.Si', 'email' => 'disdik@halbar.go.id', 'telepon' => '081234560001'],
            ['kode' => 'OPD-002', 'nama' => 'Dinas Kesehatan', 'kepala' => 'dr. Maria Soplanit, M.Kes', 'email' => 'dinkes@halbar.go.id', 'telepon' => '081234560002'],
            ['kode' => 'OPD-003', 'nama' => 'Dinas Pekerjaan Umum', 'kepala' => 'Ir. Yopi Poluan', 'email' => 'dpu@halbar.go.id', 'telepon' => '081234560003'],
            ['kode' => 'OPD-004', 'nama' => 'Dinas Sosial', 'kepala' => 'Hendrik Tjandra, S.Sos', 'email' => 'dinsos@halbar.go.id', 'telepon' => '081234560004'],
            ['kode' => 'OPD-005', 'nama' => 'Badan Keuangan Daerah', 'kepala' => 'Selvi Latuconsina, S.E', 'email' => 'bkd@halbar.go.id', 'telepon' => '081234560005'],
            ['kode' => 'OPD-006', 'nama' => 'Dinas Pertanian', 'kepala' => 'Ir. Jufri Dai', 'email' => 'distan@halbar.go.id', 'telepon' => '081234560006'],
            ['kode' => 'OPD-007', 'nama' => 'Sekretariat Daerah', 'kepala' => 'Drs. Ronald Salampessy', 'email' => 'setda@halbar.go.id', 'telepon' => '081234560007'],
            ['kode' => 'OPD-008', 'nama' => 'Badan Perencanaan Pembangunan', 'kepala' => 'Charles Tetelepta, M.T', 'email' => 'bappeda@halbar.go.id', 'telepon' => '081234560008'],
        ];
        foreach ($opds as $o) Opd::create($o);

        // Auditor
        $auditors = [
            ['nama' => 'BPK Perwakilan Maluku Utara', 'instansi' => 'BPK', 'jabatan' => 'Auditor BPK'],
            ['nama' => 'Tim Audit BPKP', 'instansi' => 'BPKP', 'jabatan' => 'Auditor BPKP'],
            ['nama' => 'Inspeur Ahmad Sahal', 'nip' => '197801012005011001', 'instansi' => 'Inspektorat Halbar', 'jabatan' => 'Auditor Inspektorat'],
            ['nama' => 'Inspectur Maya Soumilasé', 'nip' => '198203152008012003', 'instansi' => 'Inspektorat Halbar', 'jabatan' => 'Auditor Inspektorat'],
            ['nama' => 'Tim SPI Internal', 'instansi' => 'SPI Internal', 'jabatan' => 'Auditor Internal'],
        ];
        foreach ($auditors as $a) Auditor::create($a);

        // Jenis Audit
        $jenis = [
            ['nama' => 'Audit BPK', 'kode' => 'BPK', 'keterangan' => 'Audit Badan Pemeriksa Keuangan'],
            ['nama' => 'Audit BPKP', 'kode' => 'BPKP', 'keterangan' => 'Audit Badan Pengawasan Keuangan & Pembangunan'],
            ['nama' => 'Inspektorat', 'kode' => 'INSP', 'keterangan' => 'Audit Inspektorat Daerah'],
            ['nama' => 'SPI', 'kode' => 'SPI', 'keterangan' => 'Satuan Pengawasan Internal'],
            ['nama' => 'Audit Internal', 'kode' => 'INT', 'keterangan' => 'Audit Internal OPD'],
        ];
        foreach ($jenis as $j) JenisAudit::create($j);

        // Tahun
        foreach ([2023, 2024, 2025, 2026] as $thn) {
            TahunAudit::create(['tahun' => $thn, 'is_active' => $thn >= 2024]);
        }

        // Kategori Temuan
        $kategori = [
            ['nama' => 'Keuangan', 'kode' => 'KEU', 'keterangan' => 'Temuan terkait keuangan'],
            ['nama' => 'Administrasi', 'kode' => 'ADM', 'keterangan' => 'Temuan terkait administrasi'],
            ['nama' => 'Aset', 'kode' => 'AST', 'keterangan' => 'Temuan terkait aset daerah'],
            ['nama' => 'SDM', 'kode' => 'SDM', 'keterangan' => 'Temuan terkait kepegawaian'],
            ['nama' => 'Pengadaan', 'kode' => 'PNG', 'keterangan' => 'Temuan terkait pengadaan barang/jasa'],
            ['nama' => 'Infrastruktur', 'kode' => 'INF', 'keterangan' => 'Temuan terkait infrastruktur'],
        ];
        foreach ($kategori as $k) KategoriTemuan::create($k);

        // Users Demo
        $adminId = User::create([
            'name' => 'Super Admin', 'email' => 'admin@siwasta.go.id',
            'password' => 'password', 'is_active' => true,
        ])->assignRole('Super Administrator')->id;

        $verifikator = User::create([
            'name' => 'Tim Verifikator', 'email' => 'verifikator@siwasta.go.id',
            'password' => 'password', 'is_active' => true,
        ])->assignRole('Verifikator');

        $inspektur = User::create([
            'name' => 'Inspektur', 'email' => 'inspektorat@siwasta.go.id',
            'password' => 'password', 'is_active' => true,
        ])->assignRole('Inspektorat');

        $pimpinan = User::create([
            'name' => 'Bupati Halmahera Barat', 'email' => 'pimpinan@siwasta.go.id',
            'password' => 'password', 'is_active' => true,
        ])->assignRole('Pimpinan');

        // Operator OPD
        $operator1 = User::create([
            'name' => 'Operator Dinas Pendidikan', 'email' => 'operator1@siwasta.go.id',
            'password' => 'password', 'opd_id' => 1, 'is_active' => true,
        ])->assignRole('Operator OPD');

        $operator2 = User::create([
            'name' => 'Operator Dinas Kesehatan', 'email' => 'operator2@siwasta.go.id',
            'password' => 'password', 'opd_id' => 2, 'is_active' => true,
        ])->assignRole('Operator OPD');
    }
}

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        // Audit
        $audit1 = Audit::create([
            'nomor' => 'AUD/BPK/001/2025', 'judul' => 'Audit Keuangan Semester I TA 2025',
            'tanggal_mulai' => '2025-01-15', 'tanggal_selesai' => '2025-03-30',
            'jenis_audit_id' => 1, 'tahun_audit_id' => 3, 'auditor_id' => 1,
            'instansi' => 'Pemkab Halmahera Barat', 'keterangan' => 'Audit reguler semester I',
            'status' => 'selesai', 'created_by' => 1,
        ]);
        $audit1->opds()->sync([1, 2, 3]);

        $audit2 = Audit::create([
            'nomor' => 'AUD/INSP/002/2025', 'judul' => 'Audit Kinerja Dinas Pendidikan',
            'tanggal_mulai' => '2025-04-01', 'tanggal_selesai' => '2025-05-15',
            'jenis_audit_id' => 3, 'tahun_audit_id' => 3, 'auditor_id' => 3,
            'instansi' => 'Dinas Pendidikan', 'keterangan' => 'Audit kinerja',
            'status' => 'berjalan', 'created_by' => 1,
        ]);
        $audit2->opds()->sync([1]);

        $audit3 = Audit::create([
            'nomor' => 'AUD/BPKP/003/2024', 'judul' => 'Audit Pengadaan Barang & Jasa',
            'tanggal_mulai' => '2024-08-01', 'tanggal_selesai' => '2024-10-30',
            'jenis_audit_id' => 2, 'tahun_audit_id' => 2, 'auditor_id' => 2,
            'instansi' => 'Pemkab Halmahera Barat', 'status' => 'selesai', 'created_by' => 1,
        ]);
        $audit3->opds()->sync([3, 4, 6]);

        // Findings for audit1
        $f1 = Finding::create([
            'audit_id' => 1, 'nomor' => '01', 'judul' => 'Penyimpangan Penggunaan APBD',
            'opd_id' => 1, 'nilai' => 2500000000, 'kategori_temuan_id' => 1,
            'tingkat_risiko' => 'tinggi', 'deskripsi' => 'Ditemukan penyimpangan penggunaan APBD sebesar Rp 2,5 Miliar pada kegiatan pembangunan sekolah.',
            'status' => 'diproses', 'created_by' => 1,
        ]);
        $f2 = Finding::create([
            'audit_id' => 1, 'nomor' => '02', 'judul' => 'Tidak Ada Dokumen Pendukung Pengadaan Obat',
            'opd_id' => 2, 'nilai' => 850000000, 'kategori_temuan_id' => 2,
            'tingkat_risiko' => 'sedang', 'deskripsi' => 'Pengadaan obat tidak dilengkapi dokumen pendukung yang memadai.',
            'status' => 'diproses', 'created_by' => 1,
        ]);
        $f3 = Finding::create([
            'audit_id' => 1, 'nomor' => '03', 'judul' => 'Aset Tetap Belum Direkonsiliasi',
            'opd_id' => 3, 'nilai' => 1200000000, 'kategori_temuan_id' => 3,
            'tingkat_risiko' => 'sedang', 'deskripsi' => 'Aset tetap belum direkonsiliasi dengan buku besar.',
            'status' => 'selesai', 'created_by' => 1,
        ]);
        $f4 = Finding::create([
            'audit_id' => 3, 'nomor' => '01', 'judul' => 'Proses Tender Tidak Transparan',
            'opd_id' => 3, 'nilai' => 3500000000, 'kategori_temuan_id' => 5,
            'tingkat_risiko' => 'sangat_tinggi', 'deskripsi' => 'Proses tender pengadaan infrastruktur dinilai tidak transparan.',
            'status' => 'revisi', 'created_by' => 1,
        ]);

        // Recommendations
        $rec1 = Recommendation::create([
            'finding_id' => 1, 'nomor' => '1.1', 'isi' => 'Mengembalikan dana penyimpangan APBD ke kas daerah dan melakukan perbaikan administrasi keuangan.',
            'deadline' => Carbon::now()->addDays(20), 'pic' => 'Kepala Dinas Pendidikan', 'persentase' => 75, 'status' => 'diproses', 'created_by' => 1,
        ]);
        $rec2 = Recommendation::create([
            'finding_id' => 1, 'nomor' => '1.2', 'isi' => 'Memberikan sanksi kepada pihak yang bertanggung jawab atas penyimpangan.',
            'deadline' => Carbon::now()->addDays(5), 'pic' => 'Sekretariat', 'persentase' => 25, 'status' => 'diproses', 'created_by' => 1,
        ]);
        $rec3 = Recommendation::create([
            'finding_id' => 2, 'nomor' => '2.1', 'isi' => 'Melengkapi seluruh dokumen pendukung pengadaan obat.',
            'deadline' => Carbon::now()->subDays(10), 'pic' => 'Kepala Dinas Kesehatan', 'persentase' => 50, 'status' => 'revisi', 'created_by' => 1,
        ]);
        $rec4 = Recommendation::create([
            'finding_id' => 3, 'nomor' => '3.1', 'isi' => 'Melakukan rekonsiliasi aset tetap dengan buku besar secara berkala.',
            'deadline' => Carbon::now()->subDays(30), 'pic' => 'Kepala DPU', 'persentase' => 100, 'status' => 'selesai', 'created_by' => 1,
        ]);
        $rec5 = Recommendation::create([
            'finding_id' => 4, 'nomor' => '1.1', 'isi' => 'Mengulangi proses tender sesuai prosedur pengadaan yang berlaku.',
            'deadline' => Carbon::now()->addDays(45), 'pic' => 'Kepala DPU', 'persentase' => 0, 'status' => 'baru', 'created_by' => 1,
        ]);

        // Generate Reminders untuk Early Warning System
        $reminderDays = array_map('intval', explode(',', config('app.reminder_days', '30,14,7,3,1')));
        foreach ([$rec1, $rec2, $rec3, $rec4, $rec5] as $rec) {
            foreach ($reminderDays as $d) {
                $tgl = $rec->deadline->copy()->subDays($d);
                if ($tgl->greaterThanOrEqualTo(Carbon::now()->subDays(60))) {
                    Reminder::create([
                        'recommendation_id' => $rec->id, 'hari_sebelum' => $d,
                        'tanggal_kirim' => $tgl, 'media' => 'email',
                        'is_sent' => $tgl->isPast(),
                        'sent_at' => $tgl->isPast() ? $tgl : null,
                    ]);
                }
            }
            Reminder::create(['recommendation_id' => $rec->id, 'hari_sebelum' => 0, 'tanggal_kirim' => $rec->deadline, 'media' => 'email', 'is_sent' => $rec->deadline->isPast(), 'sent_at' => $rec->deadline->isPast() ? $rec->deadline : null]);
        }

        // Follow Ups
        $fu1 = FollowUp::create([
            'recommendation_id' => 1, 'tanggal' => Carbon::now()->subDays(30), 'uraian' => 'Telah mengembalikan dana sebesar Rp 1,5 Miliar ke kas daerah.',
            'persentase' => 50, 'catatan' => 'Proses pengembalian sisanya dalam proses', 'status' => 'disetujui', 'created_by' => 5,
        ]);
        FollowUp::create([
            'recommendation_id' => 1, 'tanggal' => Carbon::now()->subDays(5), 'uraian' => 'Telah mengembalikan total Rp 1,8 Miliar dan perbaikan administrasi.',
            'persentase' => 75, 'catatan' => 'Sisa 25% dalam proses', 'status' => 'pending', 'created_by' => 5,
        ]);
        FollowUp::create([
            'recommendation_id' => 3, 'tanggal' => Carbon::now()->subDays(15), 'uraian' => 'Dokumen pendukung sedang dilengkapi.',
            'persentase' => 50, 'status' => 'revisi', 'catatan' => 'Perlu dokumen tambahan', 'created_by' => 6,
        ]);
        FollowUp::create([
            'recommendation_id' => 4, 'tanggal' => Carbon::now()->subDays(40), 'uraian' => 'Rekonsiliasi aset tetap telah selesai dilakukan.',
            'persentase' => 100, 'status' => 'disetujui', 'created_by' => 5,
        ]);

        // Approval
        Approval::create(['follow_up_id' => 1, 'verifikator_id' => 3, 'status' => 'disetujui', 'catatan' => 'Dokumen lengkap dan sesuai']);
        Approval::create(['follow_up_id' => 3, 'verifikator_id' => 3, 'status' => 'revisi', 'catatan' => 'Mohon lengkapi dokumen BA']);
        Approval::create(['follow_up_id' => 4, 'verifikator_id' => 3, 'status' => 'disetujui', 'catatan' => 'Selesai dengan baik']);

        // Notifications
        NotificationCustom::create(['user_id' => 1, 'tipe' => 'info', 'judul' => 'Selamat Datang', 'pesan' => 'Selamat datang di SIWASTA-HALBAR. Sistem siap digunakan.', 'url' => route('dashboard')]);
        NotificationCustom::create(['user_id' => null, 'is_broadcast' => true, 'tipe' => 'reminder', 'judul' => 'Pengingat Tenggat Waktu', 'pesan' => 'Mohon segera tindak lanjuti rekomendasi yang mendekati deadline.', 'url' => route('monitoring.index')]);
        NotificationCustom::create(['user_id' => 5, 'tipe' => 'warning', 'judul' => 'Rekomendasi Perlu Revisi', 'pesan' => 'Tindak lanjut Anda perlu direvisi oleh verifikator.', 'url' => route('follow-up.show', 3)]);
    }
}
