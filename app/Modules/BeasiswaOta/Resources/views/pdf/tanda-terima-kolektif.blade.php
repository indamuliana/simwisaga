<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Tanda Terima Donasi Kolektif OTA - {{ $batchKode }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 1.0cm 1.5cm 1.2cm 1.5cm;
        }
        * {
            box-sizing: border-box;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 9.5pt;
            line-height: 1.4;
            color: #1e293b;
            margin: 0;
            padding: 0;
        }

        .header-kop {
            width: 100%;
            text-align: center;
            margin-top: 4px;
            margin-bottom: 10px;
        }
        .kop-image {
            display: block;
            margin: 0 auto;
            max-width: 100%;
            max-height: 90px;
            height: auto;
        }

        .doc-title-block {
            text-align: center;
            margin-bottom: 15px;
            page-break-inside: avoid;
        }
        .doc-title {
            font-size: 13pt;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 0;
            color: #0f172a;
        }
        .doc-nomor {
            font-size: 9pt;
            font-family: 'Courier New', Courier, monospace;
            color: #334155;
            margin-top: 3px;
            font-weight: bold;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
            page-break-inside: avoid;
        }
        .info-table td {
            padding: 4px 2px;
            vertical-align: top;
            font-size: 9.5pt;
        }
        .label-col {
            width: 165px;
            color: #334155;
            font-weight: 600;
        }
        .colon-col {
            width: 10px;
            text-align: center;
            color: #334155;
            font-weight: bold;
        }
        .amount-box {
            display: inline-block;
            background-color: #f8fafc;
            border: 1.5px solid #0f172a;
            padding: 4px 14px;
            font-size: 12pt;
            font-weight: 800;
            color: #0f172a;
            border-radius: 4px;
        }
        .terbilang-text {
            font-style: italic;
            color: #334155;
            margin-top: 4px;
            font-size: 9pt;
            font-weight: 500;
        }

        .section-header {
            font-weight: bold;
            font-size: 9.5pt;
            color: #0f172a;
            margin: 12px 0 6px 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .rincian-table {
            width: 100%;
            border-collapse: collapse;
            margin: 6px 0 16px 0;
            font-size: 9pt;
        }
        .rincian-table th, .rincian-table td {
            border: 1px solid #cbd5e1;
            padding: 5px 8px;
        }
        .rincian-table th {
            background-color: #f1f5f9;
            color: #1e293b;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 8pt;
            letter-spacing: 0.5px;
        }

        .signatures-table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
            page-break-inside: avoid;
        }
        .signatures-table td {
            width: 50%;
            text-align: center;
            vertical-align: top;
            font-size: 9pt;
            padding: 0 15px;
        }
        .sig-title {
            font-weight: 600;
            color: #1e293b;
        }
        .sig-space {
            height: 92px;
        }
        .qr-wrapper {
            margin: 4px auto 2px auto;
            text-align: center;
            line-height: 1;
        }
        .qr-img {
            display: inline-block;
            width: 88px;
            height: 88px;
            border: none;
            background-color: #ffffff;
        }
        .qr-hint {
            font-size: 7pt;
            color: #64748b;
            letter-spacing: 0.3px;
            margin-top: 2px;
            margin-bottom: 3px;
            text-transform: uppercase;
            font-weight: bold;
        }
        .sig-name {
            font-weight: bold;
            text-decoration: underline;
            color: #0f172a;
        }
        .sig-role {
            font-size: 8pt;
            color: #64748b;
            margin-top: 1px;
        }
    </style>
</head>
<body>

    <!-- KOP SURAT SEKOLAH -->
    @if(!empty($kopPath) && file_exists($kopPath))
        <div class="header-kop">
            <img src="{{ $kopPath }}" class="kop-image" alt="Kop Surat">
        </div>
    @elseif(!empty($school->kop_surat_path) && file_exists(public_path('storage/' . $school->kop_surat_path)))
        <div class="header-kop">
            <img src="{{ public_path('storage/' . $school->kop_surat_path) }}" class="kop-image" alt="Kop Surat">
        </div>
    @endif

    <!-- JUDUL DOKUMEN -->
    <div class="doc-title-block">
        <h2 class="doc-title">TANDA TERIMA DONASI KOLEKTIF KELOMPOK</h2>
        <div class="doc-nomor">Kode Batch Transaksi: {{ $batchKode }}</div>
    </div>

    <!-- INFORMASI PENERIMAAN -->
    <table class="info-table">
        <tr>
            <td class="label-col">Kelompok Donatur</td>
            <td class="colon-col">:</td>
            <td><strong>{{ $first->kelompok?->nama_kelompok ?? 'Kelompok Donatur OTA' }}</strong> ({{ $first->kelompok?->kode_kelompok }})</td>
        </tr>
        <tr>
            <td class="label-col">Koordinator / Penyetor</td>
            <td class="colon-col">:</td>
            <td><strong>{{ $first->penyetor_nama ?: ($first->kelompok?->koordinator_nama ?: 'Koordinator Kelompok') }}</strong></td>
        </tr>
        <tr>
            <td class="label-col">Tanggal Penyetoran</td>
            <td class="colon-col">:</td>
            <td>{{ $first->tanggal_donasi->translatedFormat('d F Y') }}</td>
        </tr>
        <tr>
            <td class="label-col">Total Nominal Disetor</td>
            <td class="colon-col">:</td>
            <td>
                <div class="amount-box">Rp {{ number_format($totalNominal, 0, ',', '.') }},-</div>
                <div class="terbilang-text"># {{ $terbilang }} #</div>
            </td>
        </tr>
        <tr>
            <td class="label-col">Untuk Keperluan</td>
            <td class="colon-col">:</td>
            <td>
                Penyetoran Iuran Donatur Beasiswa Orang Tua Asuh (OTA) 
                Kelompok {{ $first->kelompok?->nama_kelompok }} 
                sebanyak {{ $donasis->count() }} orang anggota donatur.
            </td>
        </tr>
    </table>

    <!-- TABEL RINCIAN ANGGOTA DONATUR YANG DISETORKAN -->
    <div class="section-header">
        Rincian Donatur Anggota Kelompok yang Disetorkan:
    </div>

    <table class="rincian-table">
        <thead>
            <tr>
                <th style="width: 25px; text-align: center;">No</th>
                <th style="width: 120px;">No. Transaksi</th>
                <th>Nama Donatur</th>
                <th style="width: 140px;">Periode Iuran</th>
                <th style="width: 95px;">Kontak HP</th>
                <th style="width: 115px; text-align: right;">Nominal Iuran</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @foreach($donasis as $item)
                <tr>
                    <td style="text-align: center;">{{ $no++ }}</td>
                    <td style="font-family: 'Courier New', monospace; font-size: 8pt;">{{ $item->nomor_transaksi }}</td>
                    <td><strong>{{ $item->donatur->nama }}</strong></td>
                    <td style="font-size: 8pt;">{{ $item->keterangan_periode ?: ($item->jumlah_bulan > 1 ? $item->jumlah_bulan . ' Bulan' : '-') }}</td>
                    <td style="font-size: 8pt; color: #64748b;">{{ $item->donatur->nomor_telepon ?: '-' }}</td>
                    <td style="text-align: right; font-weight: bold;">Rp {{ number_format($item->nominal, 0, ',', '.') }}</td>
                </tr>
            @endforeach
            <tr style="background-color: #f1f5f9; font-weight: bold;">
                <td colspan="5" style="text-align: right; padding-right: 10px;">TOTAL SETORAN KOLEKTIF ({{ $donasis->count() }} Donatur):</td>
                <td style="text-align: right; font-size: 9.5pt; color: #0f172a;">Rp {{ number_format($totalNominal, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <!-- TANDA TANGAN (KOORDINATOR & BENDAHARA DENGAN QR CODE SEJAJAR SEMPURNA) -->
    <table class="signatures-table">
        <tr>
            <td style="width: 50%;"></td>
            <td style="width: 50%;">
                <div style="margin-bottom: 4px; color: #334155; font-size: 9pt;">{{ $titimangsa }}</div>
            </td>
        </tr>
        <tr>
            <td class="sig-title">Penyetor (Koordinator Kelompok),</td>
            <td class="sig-title">Penerima (Sekolah),</td>
        </tr>
        <tr>
            <td style="vertical-align: middle;">
                <div class="sig-space"></div>
            </td>
            <td style="vertical-align: middle;">
                <div class="qr-wrapper">
                    @if(!empty($qrCodePng))
                        <img src="{{ $qrCodePng }}" class="qr-img" alt="QR Verifikasi">
                    @endif
                </div>
                <div class="qr-hint">Scan untuk Verifikasi Sah</div>
            </td>
        </tr>
        <tr>
            <td style="vertical-align: top;">
                <div class="sig-name">{{ $first->penyetor_nama ?: 'Koordinator Kelompok' }}</div>
                <div class="sig-role">Koordinator {{ $first->kelompok?->nama_kelompok }}</div>
            </td>
            <td style="vertical-align: top;">
                <div class="sig-name">{{ $bendaharaNama }}</div>
                <div class="sig-role">Pengelola Program Beasiswa OTA</div>
            </td>
        </tr>
    </table>

</body>
</html>
