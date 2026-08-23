<?php

namespace App\Console\Commands;

use App\Models\NotificationCustom;
use App\Models\Reminder;
use App\Services\NotificationService;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class SendRemindersCommand extends Command
{
    protected $signature = 'siwasta:send-reminders';
    protected $description = 'Kirim reminder tindak lanjut yang jadwalnya jatuh tempo hari ini (Early Warning System)';

    public function handle(): int
    {
        $today = now()->toDateString();
        $reminders = Reminder::with(['recommendation.finding.opd'])
            ->whereDate('tanggal_kirim', $today)
            ->where('is_sent', false)
            ->get();

        $sent = 0;
        foreach ($reminders as $reminder) {
            $rec = $reminder->recommendation;
            if (!$rec || $rec->status === 'selesai') {
                $reminder->update(['is_sent' => true, 'sent_at' => now()]);
                continue;
            }

            $judul = $reminder->hari_sebelum === 0
                ? '⚠️ DEADLINE HARI INI'
                : "⏰ Reminder: {$reminder->hari_sebelum} hari sebelum deadline";

            $pesan = "Rekomendasi #{$rec->nomor}\n"
                   . "OPD: {$rec->finding->opd->nama}\n"
                   . "Isi: " . Str::limit($rec->isi, 150) . "\n"
                   . "Deadline: {$rec->deadline->format('d M Y')}";

            // Kirim ke OPD terkait
            NotificationService::notifyOpd($rec->finding->opd_id, $judul, $pesan, route('recommendation.show', $rec), 'reminder');
            // Kirim ke Verifikator & Inspektorat
            NotificationService::notifyRoles(['Verifikator', 'Inspektorat'], $judul, $pesan, route('recommendation.show', $rec), 'reminder');

            $reminder->update(['is_sent' => true, 'sent_at' => now()]);
            $sent++;
        }

        $this->info("{$sent} reminder berhasil dikirim pada {$today}");
        return self::SUCCESS;
    }
}
