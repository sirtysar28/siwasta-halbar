<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class AuditTrailController extends Controller
{
    public function index(Request $request)
    {
        $query = Activity::with('causer');

        if ($s = $request->get('q')) {
            $query->where(fn($q) => $q->where('description', 'like', "%{$s}%")
                ->orWhere('log_name', 'like', "%{$s}%"));
        }
        if ($request->filled('log_name')) $query->where('log_name', $request->log_name);
        if ($request->filled('user_id')) $query->where('causer_id', $request->user_id);
        if ($request->filled('tanggal')) $query->whereDate('created_at', $request->tanggal);

        $items = $query->latest()->paginate(10)->appends($request->query());
        $logNames = Activity::select('log_name')->distinct()->pluck('log_name', 'log_name');

        return view('audit-trail.index', compact('items', 'logNames'));
    }

    public function show(Activity $activity)
    {
        return view('audit-trail.show', ['item' => $activity]);
    }
}
