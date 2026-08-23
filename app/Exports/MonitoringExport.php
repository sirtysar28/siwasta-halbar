<?php

namespace App\Exports;

use App\Models\Recommendation;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MonitoringExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    public function __construct(private $items) {}

    public function collection(): Collection
    {
        return collect($this->items);
    }

    public function headings(): array
    {
        return [
            'No', 'OPD', 'Audit', 'Nomor Temuan', 'Judul Temuan',
            'Nomor Rekomendasi', 'Isi Rekomendasi', 'PIC',
            'Deadline', 'Status', 'Progress (%)', 'Hari Tersisa', 'Keterangan',
        ];
    }

    public function map($row): array
    {
        static $no = 0;
        $no++;
        $finding = $row->finding;
        $hariTersisa = now()->startOfDay()->diffInDays($row->deadline, false);

        return [
            $no,
            $finding?->opd?->nama ?? '-',
            $finding?->audit?->nomor ?? '-',
            $finding?->nomor ?? '-',
            $finding?->judul ?? '-',
            $row->nomor,
            $row->isi,
            $row->pic ?? '-',
            $row->deadline?->format('d-m-Y'),
            ucfirst($row->status),
            $row->persentase,
            $hariTersisa < 0 ? 'Lewat ' . abs($hariTersisa) . ' hari' : $hariTersisa . ' hari',
            $hariTersisa < 0 ? 'LEWAT DEADLINE' : ($hariTersisa == 0 ? 'DEADLINE HARI INI' : '-'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true], 'fill' => ['fillType' => 'solid', 'color' => ['rgb' => '0d6efd']]],
        ];
    }
}
