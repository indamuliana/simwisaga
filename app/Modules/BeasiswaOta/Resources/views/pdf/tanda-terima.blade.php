<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Tanda Terima Donasi OTA - {{ $donasi->nomor_transaksi }}</title>
    <style>
        @page {
            margin: 1cm 1.5cm;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 10pt;
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
            margin-bottom: 15px;
        }
        .info-table td {
            padding: 5px 4px;
            vertical-align: top;
            font-size: 10pt;
        }
        .label-col {
            width: 140px;
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
            padding: 6px 14px;
            font-size: 13pt;
            font-weight: bold;
            color: #0f172a;
            border-radius: 4px;
        }
        .terbilang-text {
            font-style: italic;
            color: #475569;
            margin-top: 4px;
            font-size: 9.5pt;
        }
        .signatures-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            text-align: center;
        }
        .signatures-table td {
            vertical-align: top;
            width: 50%;
            font-size: 9.5pt;
        }
        .sign-space {
            height: 55px;
        }
        .signer-name {
            font-weight: bold;
            text-decoration: underline;
        }
        .badge-tipe {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 8pt;
            font-weight: bold;
            background-color: #e0f2fe;
            color: #0369a1;
            text-transform: uppercase;
        }
    </style>
</head>
<body>
    <div class="header-kop">
        <p class="kop-text-yayasan">YAYASAN PRAWITAMA</p>
        <h1 class="kop-text-sekolah">{{ $school->nama ?? 'SMK WIKRAMA 1 GARUT' }}</h1>
        <p class="kop-text-kontak">
            {{ $school->alamat ?? 'Jl. Otto Iskandardinata, Tarogong Kidul, Garut' }} | Telp: {{ $school->telepon ?? '(0262) 232145' }}
        </p>
    </div>

    <div class="doc-title-block">
        <div class="doc-title">TANDA TERIMA DONASI ORANG TUA ASUH</div>
        <div class="doc-nomor">No: {{ $donasi->nomor_transaksi }}</div>
    </div>

    <table class="info-table">
        <tr>
            <td class="label-col">Telah Diterima Dari</td>
            <td class="colon-col">:</td>
            <td>
                <strong>{{ $donasi->donatur->nama }}</strong> 
                <span class="badge-tipe">Donatur {{ ucfirst($donasi->tipe_donatur) }}</span>
            </td>
        </tr>
        <tr>
            <td class="label-col">Tanggal Donasi</td>
            <td class="colon-col">:</td>
            <td>{{ $donasi->tanggal_donasi->translatedFormat('d F Y') }}</td>
        </tr>
        <tr>
            <td class="label-col">Alokasi Program</td>
            <td class="colon-col">:</td>
            <td>
                @if($donasi->kelompok)
                    Program OTA - <strong>{{ $donasi->kelompok->nama_kelompok }} ({{ $donasi->kelompok->kode_kelompok }})</strong>
                @else
                    Program OTA - <strong>Kas Bebas / Cadangan Insidental</strong>
                @endif
            </td>
        </tr>
        @if(!empty($donasi->keterangan_periode))
        <tr>
            <td class="label-col">Periode Iuran / Bulan</td>
            <td class="colon-col">:</td>
            <td>
                <strong>{{ $donasi->keterangan_periode }}</strong>
                @if($donasi->tahunAjaran)
                    (Tahun Ajaran {{ $donasi->tahunAjaran->nama }})
                @endif
            </td>
        </tr>
        @endif
        <tr>
            <td class="label-col">Jumlah Donasi</td>
            <td class="colon-col">:</td>
            <td>
                <div class="amount-box">Rp {{ number_format($donasi->nominal, 0, ',', '.') }},-</div>
                <div class="terbilang-text">(Terbilang: {{ $terbilang }})</div>
            </td>
        </tr>
        @if(!empty($donasi->keterangan))
        <tr>
            <td class="label-col">Keterangan / Catatan</td>
            <td class="colon-col">:</td>
            <td>{{ $donasi->keterangan }}</td>
        </tr>
        @endif
    </table>

    <table class="signatures-table">
        <tr>
            <td>
                <div>Penyetor / Donatur,</div>
                <div class="sign-space"></div>
                <div class="signer-name">{{ $donasi->donatur->nama }}</div>
            </td>
            <td>
                <div>{{ $titimangsa }}</div>
                <div>Penerima / Pengelola OTA,</div>
                <div class="sign-space"></div>
                <div class="signer-name">{{ $bendaharaNama }}</div>
            </td>
        </tr>
    </table>
</body>
</html>
