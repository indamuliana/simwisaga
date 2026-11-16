<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekapitulasi Penerimaan Kas</title>
    <style>
        @page {
            margin: 1.2cm;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 8.5pt;
            color: #1e293b;
        }
        .header-kop {
            text-align: center;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 6px;
            margin-bottom: 12px;
        }
        .header-kop h2 {
            margin: 0;
            font-size: 13pt;
            text-transform: uppercase;
        }
        .header-kop div {
            font-size: 8pt;
            color: #64748b;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .data-table th, .data-table td {
            border: 1px solid #cbd5e1;
            padding: 4px 6px;
        }
        .data-table th {
            background-color: #f1f5f9;
            font-weight: 700;
        }
    </style>
</head>
<body>
    <div class="header-kop">
        <h2>{{ $school->nama_sekolah ?? 'SMK WIKRAMA 1 GARUT' }}</h2>
        <div>LAPORAN REKAPITULASI PENERIMAAN KAS SISWA</div>
        <div style="font-weight: bold; color: #0284c7; margin-top: 2px;">
            Periode: {{ date('d/m/Y', strtotime($startDate)) }} s.d. {{ date('d/m/Y', strtotime($endDate)) }}
            @if($rekening) | Akun: {{ $rekening->nama_bank }} @endif
        </div>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 4%;">No</th>
                <th style="width: 14%;">No. Kwitansi</th>
                <th style="width: 12%;">Tanggal</th>
                <th style="width: 24%;">Nama Siswa / Rombel</th>
                <th style="width: 26%;">Uraian Pos Terbayar</th>
                <th style="width: 20%; text-align: right;">Nominal</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transaksis as $idx => $t)
                <tr>
                    <td style="text-align: center;">{{ $idx + 1 }}</td>
                    <td><strong>{{ $t->nomor_transaksi }}</strong></td>
                    <td>{{ $t->tanggal_bayar->format('d/m/Y H:i') }}</td>
                    <td>{{ $t->siswa?->nama }} ({{ $t->siswa?->rombel?->nama ?? '-' }})</td>
                    <td>
                        {{ $t->items->map(fn($i) => ($i->posBiaya?->kode ?? 'POS') . ': Rp ' . number_format($i->nominal_dibayar, 0, ',', '.'))->join(', ') }}
                    </td>
                    <td style="text-align: right; font-weight: bold;">Rp {{ number_format($t->total_bayar, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center; color: #64748b; padding: 10px;">Tidak ada transaksi penerimaan pada rentang tanggal ini.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr style="background-color: #f8fafc; font-weight: bold;">
                <td colspan="5" style="text-align: right; font-size: 9pt;">TOTAL PENERIMAAN:</td>
                <td style="text-align: right; font-size: 9.5pt; color: #0284c7;">Rp {{ number_format($totalPenerimaan, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>
</body>
</html>

