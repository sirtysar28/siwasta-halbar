<?php

namespace App\Http\Controllers\Api;

use App\Models\NotificationCustom;
use Illuminate\Http\Request;

class NotificationApiController extends BaseApiController
{
    public function index(Request $request)
    {
        $items = NotificationCustom::where(function ($q) use ($request) {
            $q->where('user_id', $request->user()->id)->orWhere('is_broadcast', true);
        })->latest()->paginate(20);

        return $this->success([
            'items' => $items,
            'unread_count' => NotificationCustom::where('user_id', $request->user()->id)->where('dibaca', false)->count(),
        ]);
    }

    public function markAsRead($id)
    {
        $notif = NotificationCustom::where('user_id', auth()->id())->findOrFail($id);
        $notif->update(['dibaca' => true, 'dibaca_pada' => now()]);
        return $this->success($notif, 'Notifikasi ditandai dibaca');
    }
}
