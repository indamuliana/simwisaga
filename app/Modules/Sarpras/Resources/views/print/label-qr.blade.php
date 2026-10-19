<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Label QR Code Aset - {{ $school->nama ?? 'SIRAGA' }}</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            margin: 0;
            padding: 15px;
            background-color: #f8fafc;
            color: #0f172a;
        }
        .no-print-bar {
            background-color: #ffffff;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            padding: 12px 18px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .btn {
            background-color: #2563eb;
            color: #ffffff;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            font-weight: bold;
            font-size: 13px;
            cursor: pointer;
        }
        .btn:hover {
            background-color: #1d4ed8;
        }
        .grid-container {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
        }
        .label-card {
            border: 1.5px solid #000000;
            border-radius: 6px;
            padding: 8px 10px;
            background-color: #ffffff;
            display: flex;
            align-items: center;
            gap: 10px;
            page-break-inside: avoid;
        }
        .qr-box {
            flex-shrink: 0;
            width: 80px;
            text-align: center;
        }
        .qr-box img {
            width: 75px;
            height: 75px;
            display: block;
            margin: 0 auto;
        }
        .info-box {
            flex: 1;
            min-width: 0;
        }
        .school-title {
            font-size: 8pt;
            font-weight: bold;
            text-transform: uppercase;
            border-bottom: 1px solid #000000;
            padding-bottom: 2px;
            margin-bottom: 3px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .item-name {
            font-size: 9pt;
            font-weight: bold;
            margin: 0;
            line-height: 1.2;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .item-code {
            font-size: 8.5pt;
            font-family: monospace;
            font-weight: bold;
            color: #0f172a;
            margin: 2px 0;
        }
        .item-room {
            font-size: 7.5pt;
            color: #334155;
            margin: 0;
        }
        .item-footer {
            font-size: 6.5pt;
            color: #64748b;
            margin-top: 3px;
            font-style: italic;
        }

        @media print {
            body {
                background-color: #ffffff;
                padding: 0;
            }
            .no-print-bar {
                display: none !important;
            }
            .grid-container {
                gap: 8px;
            }
            .label-card {
                border: 1px solid #000000;
            }
        }
    </style>
</head>
<body>

    <div class="no-print-bar">
        <div>
            <strong>Lembar Cetak Stiker Label QR Code Aset</strong>
            <div style="font-size: 11px; color: #64748b;">Total: {{ $asets->count() }} label siap dicetak (Gunakan kertas stiker A4 / Tom & Jerry).</div>
        </div>
        <div>
            <button type="button" class="btn" onclick="window.print()">🖨️ Cetak Label Sekarang</button>
        </div>
    </div>

    <div class="grid-container">
        @forelse($asets as $a)
            <div class="label-card">
                <div class="qr-box">
                    {!! \App\Modules\Sarpras\Services\QrCodeService::renderHtml($a->kode_aset, 75) !!}
                </div>
                <div class="info-box">
                    <div class="school-title">{{ $school->nama ?? 'SMK WIKRAMA 1 GARUT' }}</div>
                    <p class="item-name" title="{{ $a->nama_barang }}">{{ $a->nama_barang }}</p>
                    <div class="item-code">{{ $a->kode_aset }}</div>
                    <p class="item-room">Lokasi: <strong>{{ $a->ruangan?->nama_ruangan ?? '-' }}</strong></p>
                    <div class="item-footer">Inventaris Resmi Sekolah &bull; {{ $a->tahun_perolehan }}</div>
                </div>
            </div>
        @empty
            <div style="grid-column: span 3; text-align: center; padding: 40px; color: #64748b;">
                Tidak ada data aset untuk dicetak labelnya.
            </div>
        @endforelse
    </div>

</body>
</html>
