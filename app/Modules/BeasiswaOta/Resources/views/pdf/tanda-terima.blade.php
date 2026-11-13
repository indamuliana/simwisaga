<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Tanda Terima Donasi OTA - {{ $donasi->nomor_transaksi }}</title>
    <style>
        @page {
            size: 210mm 148mm;
            /* Margin proporsional 2.0cm kiri-kanan agar tidak mepet ke tepi kertas */
            margin: 0.8cm 2.0cm 0.8cm 2.0cm;
        }
        * {
            box-sizing: border-box;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 9pt;
            line-height: 1.35;
            color: #0f172a;
            margin: 0;
            padding: 0;
            page-break-inside: avoid;
        }

        /* Container Kwitansi (Tanpa garis bingkai hitam) */
        .receipt-container {
            padding: 0;
            box-sizing: border-box;
            background-color: #ffffff;
            page-break-inside: avoid;
        }

        .header-kop {
            width: 100%;
            text-align: center;
            margin-top: 0;
            margin-bottom: 8px;
        }
        .kop-image {
            display: block;
            margin: 0 auto;
            max-width: 100%;
            max-height: 86px;
            height: auto;
        }

        .doc-title-block {
            text-align: center;
            margin-bottom: 12px;
            page-break-inside: avoid;
        }
        .doc-title {
            font-size: 12.5pt;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 0;
            color: #0f172a;
        }
        .doc-nomor {
            font-size: 8.5pt;
            font-family: 'Courier New', Courier, monospace;
            color: #334155;
            margin-top: 2px;
            font-weight: bold;
            letter-spacing: 0.5px;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
            page-break-inside: avoid;
        }
        .info-table td {
            padding: 3.5px 2px;
            vertical-align: top;
            font-size: 9.5pt;
        }
        .label-col {
            width: 145px;
            color: #334155;
            font-weight: 600;
        }
        .colon-col {
            width: 10px;
            text-align: center;
            color: #334155;
            font-weight: bold;
        }
        .value-col {
            color: #0f172a;
        }
        .amount-box {
            display: inline-block;
            background-color: #f8fafc;
            border: 1.5px solid #0f172a;
            padding: 3px 12px;
            font-size: 11.5pt;
            font-weight: 800;
            color: #0f172a;
            border-radius: 4px;
            letter-spacing: 0.5px;
        }
        .terbilang-text {
            font-style: italic;
            color: #334155;
            margin-left: 8px;
            font-size: 9pt;
            font-weight: 500;
        }

        /* Tabel Tanda Tangan: Menggunakan struktur baris terpisah agar penanda tangan sejajar sempurna */
        .signatures-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
            margin-bottom: 4px;
            page-break-inside: avoid;
        }
        .signatures-table td {
            width: 50%;
            text-align: center;
            padding: 0 10px;
        }
        .sig-date {
            color: #334155;
            font-size: 8.5pt;
            font-weight: 500;
            margin-bottom: 4px;
        }
        .sig-title {
            font-weight: 600;
            color: #1e293b;
            font-size: 8.5pt;
            padding-bottom: 4px;
        }
        .sign-space-donor {
            height: 90px;
        }
        .qr-wrapper {
            margin: 2px auto 1px auto;
            text-align: center;
            line-height: 1;
        }
        .qr-img {
            display: inline-block;
            width: 84px;
            height: 84px;
            border: none;
            background-color: #ffffff;
        }
        .qr-hint {
            font-size: 6.5pt;
            color: #64748b;
            letter-spacing: 0.3px;
            margin-top: 1px;
            margin-bottom: 2px;
            text-transform: uppercase;
            font-weight: bold;
        }
        .signer-name {
            font-weight: bold;
            text-decoration: underline;
            color: #0f172a;
            font-size: 9pt;
            padding-top: 4px;
        }
        .signer-role {
            font-size: 8pt;
            color: #64748b;
            margin-top: 1px;
        }
        .badge-tipe {
            display: inline-block;
            padding: 1px 7px;
            border-radius: 3px;
            font-size: 7.5pt;
            font-weight: bold;
            background-color: #e0f2fe;
            color: #0369a1;
            text-transform: uppercase;
            margin-left: 6px;
            border: 1px solid #bae6fd;
        }
        .receipt-footer {
            border-top: 1px dashed #e2e8f0;
            margin-top: 8px;
            padding-top: 3px;
            font-size: 7pt;
            color: #94a3b8;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="receipt-container">
        {{-- Header Kop Surat Sekolah (Rata Tengah, Tanpa Garis Atas Pemisah) --}}
        @if(!empty($kopPath) && file_exists($kopPath))
            <div class="header-kop">
                <img src="{{ $kopPath }}" class="kop-image" alt="Kop Surat">
            </div>
        @endif

        {{-- Judul Tanda Terima & Nomor Bukti --}}
        <div class="doc-title-block">
            <div class="doc-title">TANDA TERIMA DONASI ORANG TUA ASUH</div>
            <div class="doc-nomor">Nomor Bukti: {{ $donasi->nomor_transaksi }}</div>
        </div>

        {{-- Rincian Donasi (Tanpa Keterangan/Catatan) --}}
        <table class="info-table">
            <tr>
                <td class="label-col">Telah Diterima Dari</td>
                <td class="colon-col">:</td>
                <td class="value-col">
                    <strong>{{ $donasi->donatur->nama }}</strong> 
                    <span class="badge-tipe">Donatur {{ ucfirst($donasi->tipe_donatur) }}</span>
                </td>
            </tr>
            <tr>
                <td class="label-col">Jumlah Donasi</td>
                <td class="colon-col">:</td>
                <td class="value-col">
                    <span class="amount-box">Rp {{ number_format($donasi->nominal, 0, ',', '.') }},-</span>
                    <span class="terbilang-text"># {{ $terbilang }} #</span>
                </td>
            </tr>
            <tr>
                <td class="label-col">Tanggal Donasi</td>
                <td class="colon-col">:</td>
                <td class="value-col">{{ $donasi->tanggal_donasi->translatedFormat('d F Y') }}</td>
            </tr>
            <tr>
                <td class="label-col">Alokasi Program</td>
                <td class="colon-col">:</td>
                <td class="value-col">
                    @if($donasi->kelompok)
                        Program OTA — <strong>{{ $donasi->kelompok->nama_kelompok }} ({{ $donasi->kelompok->kode_kelompok }})</strong>
                    @else
                        Program OTA — <strong>Kas Bebas / Cadangan Insidental</strong>
                    @endif
                </td>
            </tr>
            @if(!empty($donasi->keterangan_periode))
            <tr>
                <td class="label-col">Periode Iuran</td>
                <td class="colon-col">:</td>
                <td class="value-col">
                    <strong>{{ $donasi->keterangan_periode }}</strong>
                    @if($donasi->tahunAjaran)
                        <span style="color: #64748b;">(Tahun Ajaran {{ $donasi->tahunAjaran->nama }})</span>
                    @endif
                </td>
            </tr>
            @endif
        </table>

        {{-- Penanda Tangan: Penyetor / Donatur Sejajar dengan Penerima / Bendahara OTA --}}
        <table class="signatures-table">
            {{-- Baris 1: Tanggal di sisi kanan --}}
            <tr>
                <td></td>
                <td>
                    <div class="sig-date">{{ $titimangsa }}</div>
                </td>
            </tr>
            {{-- Baris 2: Jabatan Penanda Tangan (Sejajar Sempurna) --}}
            <tr>
                <td class="sig-title">Penyetor / Donatur,</td>
                <td class="sig-title">Penerima / Bendahara OTA,</td>
            </tr>
            {{-- Baris 3: Area Tanda Tangan & QR Code --}}
            <tr>
                <td style="vertical-align: middle;">
                    <div class="sign-space-donor"></div>
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
            {{-- Baris 4: Nama Penanda Tangan (Sejajar Sempurna) --}}
            <tr>
                <td style="vertical-align: top;">
                    <div class="signer-name">( {{ $donasi->donatur->nama }} )</div>
                </td>
                <td style="vertical-align: top;">
                    <div class="signer-name">{{ $bendaharaNama }}</div>
                    @if(strcasecmp($bendaharaNama, 'Bendahara Program OTA') !== 0)
                        <div class="signer-role">Bendahara Program OTA</div>
                    @endif
                </td>
            </tr>
        </table>

        <div class="receipt-footer">
            Dokumen tanda terima elektronik sah • Verifikasi keaslian dokumen via scan barcode QR Code di atas
        </div>
    </div>
</body>
</html>
