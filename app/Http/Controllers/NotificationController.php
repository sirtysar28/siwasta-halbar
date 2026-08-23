<?php

namespace App\Http\Controllers;

use App\Models\NotificationCustom;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'inbox');

        $query = NotificationCustom::where(function ($q) {
            $q->where('user_id', auth()->id())->orWhere('is_broadcast', true);
        });

        if ($tab === 'unread') $query->where('dibaca', false);
        if ($tab === 'reminder') $query->where('tipe', 'reminder');
        if ($tab === 'broadcast') $query->where('is_broadcast', true);

        $items = $query->latest()->paginate(10);

        return view('notifications.index', compact('items', 'tab'));
    }

    public function markAsRead(NotificationCustom $notification)
    {
        if (!$notification->dibaca) {
            $notification->update(['dibaca' => true, 'dibaca_pada' => now()]);
        }
        return redirect($notification->url ?? route('notifications.index'));
    }

    public function markAllRead()
    {
        NotificationCustom::where('user_id', auth()->id())->where('dibaca', false)
            ->update(['dibaca' => true, 'dibaca_pada' => now()]);
        return back()->with('success', 'Semua notifikasi ditandai dibaca.');
    }

    public function destroy(NotificationCustom $notification)
    {
        $notification->delete();
        return back()->with('success', 'Notifikasi dihapus.');
    }

    public function broadcastForm()
    {
        return view('notifications.broadcast');
    }

    public function broadcast(Request $request)
    {
        $data = $request->validate([
            'judul' => 'required|string|max:255',
            'pesan' => 'required|string',
            'tipe' => 'required|in:info,success,warning,danger,reminder',
        ]);
        NotificationService::broadcast($data['judul'], $data['pesan'], $data['tipe']);
        activity()->log('Membroadcast notifikasi: ' . $data['judul']);
        return back()->with('success', 'Notifikasi berhasil dibroadcast ke semua user.');
    }
}
