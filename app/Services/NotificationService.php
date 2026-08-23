<?php

namespace App\Services;

use App\Models\NotificationCustom;
use App\Models\User;

class NotificationService
{
    public static function send(int $userId, string $judul, string $pesan, string $tipe = 'info', ?string $url = null): void
    {
        NotificationCustom::create([
            'user_id' => $userId,
            'judul' => $judul,
            'pesan' => $pesan,
            'tipe' => $tipe,
            'url' => $url,
        ]);
    }

    public static function notifyOpd(int $opdId, string $judul, string $pesan, ?string $url = null, string $tipe = 'info'): void
    {
        $users = User::where('opd_id', $opdId)->where('is_active', true)->get();
        foreach ($users as $user) {
            self::send($user->id, $judul, $pesan, $tipe, $url);
        }
    }

    public static function broadcast(string $judul, string $pesan, string $tipe = 'info', ?string $url = null): void
    {
        NotificationCustom::create([
            'user_id' => null,
            'judul' => $judul,
            'pesan' => $pesan,
            'tipe' => $tipe,
            'url' => $url,
            'is_broadcast' => true,
        ]);
    }

    public static function notifyRoles(array $roles, string $judul, string $pesan, ?string $url = null, string $tipe = 'info'): void
    {
        $users = User::role($roles)->where('is_active', true)->get();
        foreach ($users as $user) {
            self::send($user->id, $judul, $pesan, $tipe, $url);
        }
    }
}
