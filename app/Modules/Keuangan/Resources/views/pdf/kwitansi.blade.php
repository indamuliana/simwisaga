<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kwitansi - {{ $transaksi->nomor_transaksi }}</title>
    <style>
        @page {
            margin: 0.8cm 1cm 0.8cm 1cm;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 8.5pt;
            color: #1e293b;
            line-height: 1.3;
        }
        .header-table {
            width: 100%;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 6px;
            margin-bottom: 10px;
        }
        .school-name {
            font-size: 13pt;
            font-weight: 800;
            color: #0f172a;
            text-transform: uppercase;
        }
        .school-address {
            font-size: 7.5pt;
            color: #64748b;
        }
        .doc-title {
            text-align: right;
            font-size: 14pt;
            font-weight: 800;
            color: #0284c7;
            letter-spacing: 1px;
        }
        .doc-number {
            text-align: right;
            font-size: 8pt;
            color: #475569;
            font-weight: bold;
        }
        .meta-table {
            width: 100%;
            margin-bottom: 8px;
        }
        .meta-table td {
            padding: 1.5px 0;
            vertical-align: top;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
            margin-bottom: 10px;
        }
        .data-table th {
            background-color: #f1f5f9;
            border-top: 1px solid #cbd5e1;
            border-bottom: 1px solid #cbd5e1;
            padding: 5px 6px;
            text-align: left;
            font-size: 8pt;
            font-weight: 700;
            color: #334155;
        }
        .data-table td {
            border-bottom: 1px solid #e2e8f0;
            padding: 4px 6px;
            font-size: 8pt;
        }
        .terbilang-box {
            background-color: #f8fafc;
            border-left: 3px solid #0284c7;
            padding: 4px 8px;
            font-style: italic;
            font-size: 8pt;
            color: #334155;
            margin-bottom: 10px;
        }
        .footer-table {
            width: 100%;
            margin-top: 10px;
        }
        .qr-box {
            text-align: center;
            width: 100px;
        }
        .ttd-box {
            text-align: center;
            width: 180px;
            font-size: 8pt;
        }
    </style>
</head>
<body>
    <table class="header-table">
        <tr>
            <td style="width: 60%;">
                <div class="school-name">{{ $school->nama_sekolah ?? 'SMK WIKRAMA 1 GARUT' }}</div>
                <div class="school-address">{{ $school->alamat ?? 'Jl. Otto Iskandardinata, Tarogong Kidul, Garut' }} | Telp: {{ $school->telepon ?? '-' }}</div>
            </td>
            <td style="width: 40%;">
                <div class="doc-title">BUKTI PEMBAYARAN</div>
                <div class="doc-number">No: {{ $transaksi->nomor_transaksi }}</div>
                <div class="doc-number">Tgl: {{ $transaksi->tanggal_bayar->format('d/m/Y H:i') }} WIB</div>
            </td>
        </tr>
    </table>

    <table class="meta-table">
        <tr>
            <td style="width: 15%; font-weight: bold;">Telah Terima Dari</td>
            <td style="width: 2%;">:</td>
            <td style="width: 43%;">{{ $transaksi->nama_penyetor ?: ($transaksi->siswa?->nama ?? '-') }}</td>
            <td style="width: 15%; font-weight: bold;">Metode / Kas</td>
            <td style="width: 2%;">:</td>
            <td style="width: 25%;">{{ $transaksi->rekening?->nama_bank ?? 'Kasir Loket' }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold;">Nama Siswa</td>
            <td>:</td>
            <td style="font-weight: bold; color: #0f172a;">{{ $transaksi->siswa?->nama }}</td>
            <td style="font-weight: bold;">Kelas / Rayon</td>
            <td>:</td>
            <td>{{ $transaksi->siswa?->rombel?->nama ?? '-' }} ({{ $transaksi->siswa?->rayon?->nama ?? '-' }})</td>
        </tr>
        <tr>
            <td style="font-weight: bold;">NISN / NIPD</td>
            <td>:</td>
            <td>{{ $transaksi->siswa?->nisn ?? '-' }} / {{ $transaksi->siswa?->nipd ?? '-' }}</td>
            <td style="font-weight: bold;">Sisa Tunggakan</td>
            <td>:</td>
            <td style="color: #b91c1c; font-weight: bold;">Rp {{ number_format($sisaTunggakan, 0, ',', '.') }}</td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 50%;">Uraian Pembayaran</th>
                <th style="width: 20%; text-align: right;">Nominal Disetor</th>
                <th style="width: 25%; text-align: right;">Sisa Tagihan Pos</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transaksi->items as $idx => $item)
                <tr>
                    <td>{{ $idx + 1 }}</td>
                    <td>
                        <strong>{{ $item->tagihan?->label_tagihan ?? ($item->posBiaya?->nama ?? 'Biaya Pendidikan') }}</strong>
                        @if($item->keterangan)
                            <div style="font-size: 7.5pt; color: #64748b;">{{ $item->keterangan }}</div>
                        @endif
                    </td>
                    <td style="text-align: right; font-weight: bold;">Rp {{ number_format($item->nominal_dibayar, 0, ',', '.') }}</td>
                    <td style="text-align: right; color: {{ $item->sisa_setelah_bayar <= 0 ? '#15803d' : '#b91c1c' }}; font-weight: bold;">
                        {{ $item->sisa_setelah_bayar <= 0 ? 'LUNAS' : 'Rp ' . number_format($item->sisa_setelah_bayar, 0, ',', '.') }}
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="background-color: #f8fafc; font-weight: bold;">
                <td colspan="2" style="text-align: right; font-size: 9pt; padding: 6px;">TOTAL PEMBAYARAN:</td>
                <td style="text-align: right; font-size: 9pt; color: #0284c7; padding: 6px;">Rp {{ number_format($transaksi->total_bayar, 0, ',', '.') }}</td>
                <td></td>
            </tr>
        </tfoot>
    </table>

    <div class="terbilang-box">
        <strong>Terbilang:</strong> {{ $terbilang }}
    </div>

    <table class="footer-table">
        <tr>
            <td style="width: 15%; text-align: center; vertical-align: top;">
                @if($qrCodePng)
                    <img src="{{ $qrCodePng }}" style="width: 80px; height: 80px;" alt="QR Code"><br>
                    <span style="font-size: 6.5pt; color: #64748b;">Scan Validasi Keaslian</span>
                @endif
            </td>
            <td style="width: 45%; vertical-align: top; padding-left: 10px; font-size: 7.5pt; color: #64748b;">
                <strong>Catatan:</strong>
                <div>1. Bukti pembayaran ini sah dan diterbitkan secara elektronik oleh sistem SIWARAGA.</div>
                <div>2. Simpan kwitansi ini sebagai bukti pembayaran resmi administrasi sekolah.</div>
                <div>3. QR Code dapat dipindai untuk memastikan catatan transaksi sah tercatat pada database.</div>
            </td>
            <td class="ttd-box" style="vertical-align: top;">
                Garut, {{ $transaksi->tanggal_bayar->format('d/m/Y') }}<br>
                Petugas / Bendahara Sekolah,<br><br><br><br>
                <strong>{{ $transaksi->user?->name ?? 'Bendahara Sekolah' }}</strong>
            </td>
        </tr>
    </table>
</body>
</html>

