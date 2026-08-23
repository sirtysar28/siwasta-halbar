<?php

namespace App\Services;

use App\Models\TahunAudit;

class TahunAuditService
{
    public static function list()
    {
        return TahunAudit::orderByDesc('tahun')->pluck('tahun', 'id');
    }

    public static function years()
    {
        return TahunAudit::orderByDesc('tahun')->pluck('tahun', 'tahun');
    }
}
