<?php

namespace App\Http\Controllers\Api;

use App\Models\Audit;
use App\Models\Auditor;
use App\Models\Finding;
use App\Models\FollowUp;
use App\Models\Opd;
use App\Models\Recommendation;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LaporanApiController extends Controller
{
    public function index(Request $request, string $type)
    {
        $data = match ($type) {
            'audit' => Audit::with(['jenisAudit', 'tahunAudit', 'auditor'])->get(),
            'temuan' => Finding::with(['audit', 'opd', 'kategoriTemuan'])->get(),
            'rekomendasi' => Recommendation::with(['finding.audit', 'finding.opd'])->get(),
            'opd' => Opd::withCount('findings')->get(),
            'auditor' => Auditor::withCount('audits')->get(),
            'deadline' => Recommendation::with(['finding.opd'])->where('status', '!=', 'selesai')->orderBy('deadline')->get(),
            'tindak_lanjut' => FollowUp::with(['recommendation.finding.opd', 'approvals'])->get(),
            default => collect(),
        };

        return $this->jsonResponse($data);
    }

    private function jsonResponse($data, string $message = 'OK', int $code = 200)
    {
        return response()->json(['status' => 'success', 'message' => $message, 'data' => $data], $code);
    }
}
