<?php

namespace App\Http\Controllers\Api;

use App\Models\Recommendation;
use Illuminate\Http\Request;

class MonitoringApiController extends BaseApiController
{
    public function index(Request $request)
    {
        $query = Recommendation::with(['finding.audit', 'finding.opd', 'followUps']);

        if ($request->filled('tahun')) {
            $query->whereHas('finding.audit.tahunAudit', fn($q) => $q->where('tahun', $request->tahun));
        }
        if ($request->filled('opd_id')) $query->whereHas('finding', fn($q) => $q->where('opd_id', $request->opd_id));
        if ($request->filled('status')) $query->where('status', $request->status);

        return $this->success($query->latest()->paginate(20));
    }
}
