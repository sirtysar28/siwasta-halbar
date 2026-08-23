<!DOCTYPE html>
<html lang="id">
<head><meta charset="UTF-8"><title>Monitoring Tindak Lanjut</title>
<style>
body{font-family:'Inter',Arial,sans-serif;font-size:11px;color:#1e293b;}
.header{text-align:center;border-bottom:3px double #1e40af;padding-bottom:10px;margin-bottom:15px;}
.header img{width:45px;height:45px;object-fit:contain;margin-bottom:5px;}
table{width:100%;border-collapse:collapse;margin-top:10px;}
th{background:#1e40af;color:#fff;padding:6px;text-align:left;font-size:10px;}
td{padding:5px;border-bottom:1px solid #e2e8f0;}
.danger{background:#fef2f2;}
.success{background:#f0fdf4;}
</style></head>
<body>
<div class="header"><img src="{{ public_path('images/logo.png') }}" alt="Logo"><h2>MONITORING TINDAK LANJUT</h2><h3>Sistem Informasi Pengawasan - Inspektorat Kab. Halmahera Barat</h3><p>Periode: {{ now()->format('d F Y') }}</p></div>
<table><thead><tr><th>No</th><th>OPD</th><th>Temuan</th><th>Rekomendasi</th><th>Deadline</th><th>Hari</th><th>%</th><th>Status</th></tr></thead>
<tbody>@foreach($items as $i=>$r)
@php $sisa = now()->startOfDay()->diffInDays($r->deadline, false); @endphp
<tr class="{{ $sisa<0?'danger':($r->status=='selesai'?'success':'') }}">
<td>{{ $i+1 }}</td><td>{{ $r->finding?->opd?->nama }}</td><td>{{ Str::limit($r->finding?->judul,30) }}</td><td>{{ Str::limit($r->isi,50) }}</td>
<td>{{ $r->deadline?->format('d/m/Y') }}</td><td>{{ $sisa<0?'Lewat '.abs($sisa):$sisa.' hari' }}</td><td>{{ $r->persentase }}%</td><td>{{ ucfirst($r->status) }}</td></tr>@endforeach
</tbody></table>
<p style="margin-top:20px;font-size:9px;color:#64748b;">Dokumen ini diterbitkan secara otomatis oleh SIWASTA-HALBAR pada {{ now()->format('d/m/Y H:i') }}</p>
</body></html>
