<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ReportExport implements FromCollection, WithHeadings, WithStyles
{
    public function __construct(private $data, private string $type) {}

    public function collection(): Collection
    {
        return collect($this->data)->map(fn($row) => $this->mapRow($row));
    }

    public function headings(): array
    {
        return match ($this->type) {
            'audit' => ['Nomor', 'Judul', 'Jenis Audit', 'Tahun', 'Auditor', 'Instansi', 'Tanggal Mulai', 'Status', 'Jumlah Temuan'],
            'temuan' => ['Nomor', 'Judul', 'OPD', 'Kategori', 'Nilai', 'Risiko', 'Status', 'Audit'],
            'rekomendasi' => ['Nomor', 'Isi Rekomendasi', 'Deadline', 'PIC', 'Status', 'Progress (%)', 'OPD'],
            'opd' => ['Kode', 'Nama OPD', 'Kepala', 'Total Temuan', 'Total Rekomendasi Selesai'],
            'auditor' => ['Nama', 'NIP', 'Jabatan', 'Instansi', 'Total Audit'],
            'deadline' => ['Nomor Rekomendasi', 'OPD', 'Deadline', 'Hari Tersisa', 'Status'],
            'tindak_lanjut' => ['Tanggal', 'OPD', 'Uraian', 'Persentase', 'Status', 'Verifikator'],
            default => ['Data'],
        };
    }

    private function mapRow($row): array
    {
        return match ($this->type) {
            'audit' => [
                $row->nomor, $row->judul, $row->jenisAudit?->nama, $row->tahunAudit?->tahun,
                $row->auditor?->nama, $row->instansi, $row->tanggal_mulai?->format('d-m-Y'),
                ucfirst($row->status), $row->findings_count ?? $row->findings->count(),
            ],
            'temuan' => [
                $row->nomor, $row->judul, $row->opd?->nama, $row->kategoriTemuan?->nama,
                number_format($row->nilai), ucfirst($row->tingkat_risiko), ucfirst($row->status),
                $row->audit?->nomor,
            ],
            'rekomendasi' => [
                $row->nomor, $row->isi, $row->deadline?->format('d-m-Y'), $row->pic ?? '-',
                ucfirst($row->status), $row->persentase, $row->finding?->opd?->nama ?? '-',
            ],
            'opd' => [
                $row->kode, $row->nama, $row->kepala ?? '-',
                $row->findings_count ?? $row->findings->count(),
                $row->findings->flatMap->recommendations->where('status', 'selesai')->count(),
            ],
            'auditor' => [
                $row->nama, $row->nip ?? '-', $row->jabatan ?? '-', $row->instansi ?? '-',
                $row->audits_count ?? $row->audits->count(),
            ],
            'deadline' => [
                $row->nomor, $row->finding?->opd?->nama ?? '-', $row->deadline?->format('d-m-Y'),
                now()->startOfDay()->diffInDays($row->deadline, false), ucfirst($row->status),
            ],
            'tindak_lanjut' => [
                $row->tanggal?->format('d-m-Y'), $row->recommendation?->finding?->opd?->nama ?? '-',
                $row->uraian, $row->persentase . '%', ucfirst($row->status),
                $row->approvals->first()?->verifikator?->name ?? '-',
            ],
            default => [],
        };
    }

    public function styles(Worksheet $sheet)
    {
        return [1 => ['font' => ['bold' => true]]];
    }
}
