<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Tanda Terima Donasi Kolektif OTA - {{ $batchKode }}</title>
    <style>
        @page {
            margin: 1.2cm 1.5cm;
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
            border-bottom: 2px solid #0f172a;
            padding-bottom: 6px;
            margin-bottom: 12px;
            text-align: center;
        }
        .kop-text-yayasan {
            font-size: 9pt;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin: 0;
            color: #475569;
        }
        .kop-text-sekolah {
            font-size: 14pt;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin: 2px 0;
            color: #0f172a;
        }
        .kop-text-kontak {
            font-size: 8pt;
            color: #64748b;
            margin: 0;
        }
        .doc-title-block {
            text-align: center;
            margin-bottom: 15px;
        }
        .doc-title {
            font-size: 13pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            text-decoration: underline;
            margin: 0;
        }
        .doc-nomor {
            font-size: 9pt;
            font-family: 'Courier New', Courier, monospace;
            color: #334155;
            margin-top: 2px;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }
        .info-table td {
            padding: 4px 2px;
            vertical-align: top;
            font-size: 9.5pt;
        }
        .label-col {
            width: 150px;
            color: #334155;
            font-weight: 600;
        }
        .colon-col {
            width: 10px;
            text-align: center;
        }
        .amount-box {
            display: inline-block;
            background-color: #f1f5f9;
            border: 1px solid #cbd5e1;
            padding: 5px 12px;
            font-size: 12pt;
            font-weight: bold;
            color: #0f172a;
            border-radius: 4px;
        }
        .terbilang-text {
            font-style: italic;
            color: #475569;
            margin-top: 3px;
            font-size: 9pt;
        }
        .rincian-table {
            width: 100%;
            border-collapse: collapse;
            margin: 12px 0 16px 0;
            font-size: 9pt;
        }
        .rincian-table th, .rincian-table td {
            border: 1px solid #cbd5e1;
            padding: 5px 8px;
        }
        .rincian-table th {
            background-color: #f8fafc;
            color: #334155;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 8pt;
        }
        .signatures-table {
            width: 100%;
            margin-top: 25px;
            border-collapse: collapse;
        }
        .signatures-table td {
            width: 50%;
            text-align: center;
            vertical-align: top;
            font-size: 9pt;
        }
        .sig-space {
            height: 60px;
        }
        .sig-name {
            font-weight: bold;
            text-decoration: underline;
        }
        .sig-role {
            font-size: 8pt;
            color: #64748b;
        }
    </style>
</head>
<body>

    <!-- KOP SURAT -->
    <div class="header-kop">
        @if($school?->yayasan)
            <div class="kop-text-yayasan">{{ $school->yayasan }}</div>
        @endif
        <div class="kop-text-sekolah">{{ $school?->nama ?? 'SEKOLAH INDONESIA' }}</div>
        <div class="kop-text-kontak">
            {{ $school?->alamat ?? 'Alamat Sekolah' }} 
            @if($school?->telepon) | Telp: {{ $school->telepon }} @endif
            @if($school?->email) | Email: {{ $school->email }} @endif
        </div>
    </div>

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
                <div class="amount-box">Rp {{ number_format($totalNominal, 0, ',', '.') }}</div>
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
    <div style="font-weight: bold; font-size: 9pt; color: #0f172a; margin-top: 10px;">
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
                <th style="width: 110px; text-align: right;">Nominal Iuran</th>
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
                <td style="text-align: right; font-size: 10pt; color: #0f172a;">Rp {{ number_format($totalNominal, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <!-- TANDA TANGAN -->
    <table class="signatures-table">
        <tr>
            <td>
                Penyetor (Koordinator Kelompok),
                <div class="sig-space"></div>
                <div class="sig-name">{{ $first->penyetor_nama ?: 'Koordinator Kelompok' }}</div>
                <div class="sig-role">Koordinator {{ $first->kelompok?->nama_kelompok }}</div>
            </td>
            <td>
                {{ $titimangsa }}<br>
                Penerima (Sekolah),
                <div class="sig-space"></div>
                <div class="sig-name">{{ $bendaharaNama }}</div>
                <div class="sig-role">Pengelola Program Beasiswa OTA</div>
            </td>
        </tr>
    </table>

</body>
</html>
