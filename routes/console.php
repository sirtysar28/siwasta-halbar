<?php

use Illuminate\Support\Facades\Schedule;

// Early Warning System - kirim reminder setiap hari jam 08:00
Schedule::command('siwasta:send-reminders')->dailyAt('08:00')->description('Kirim reminder tindak lanjut harian');

// Bersihkan notifikasi lama setiap minggu
Schedule::command('model:prune', ['--model' => \App\Models\NotificationCustom::class])->weekly();
