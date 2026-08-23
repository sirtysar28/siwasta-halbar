<!DOCTYPE html>
<html lang="id"><head><meta charset="UTF-8"><title>{{ $title }}</title>
<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
    body{font-family:'Inter',Arial,sans-serif;font-size:11px;color:#1e293b;}
    .header{text-align:center;border-bottom:3px double #1e40af;padding-bottom:8px;margin-bottom:10px;}
    .header img{width:50px;height:50px;object-fit:contain;margin-bottom:5px;}
    table{width:100%;border-collapse:collapse;}
    th{background:#1e40af;color:#ffffff;padding:5px;font-weight:600;}
    td{padding:4px;border-bottom:1px solid #e2e8f0;color:#334155;}
</style>
</head><body>
<div class="header">
    <img src="{{ public_path('images/logo.png') }}" alt="Logo">
    <h2 style="margin:0;font-size:16px;color:#0f172a;">{{ $title }}</h2>
    <p style="margin:2px 0 0;font-size:10px;color:#64748b;">Inspektorat Kabupaten Halmahera Barat | Dicetak: {{ now()->format('d F Y H:i') }}</p>
</div>
<table><thead><tr><th>No</th><th>Keterangan</th><th>Detail</th></tr></thead><tbody>
@foreach($data as $i=>$item)<tr><td>{{ $i+1 }}</td><td>{{ $item->nama ?? $item->judul ?? $item->nomor ?? '-' }}</td><td>{{ json_encode($item->toArray(), JSON_UNESCAPED_UNICODE) }}</td></tr>@endforeach
</tbody></table>
</body></html>
