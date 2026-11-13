<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Kwitansi Penyaluran Beasiswa - {{ $alokasi->nomor_kwitansi }}</title>
    <style>
        @page {
            size: A4 portrait;
            /* Margin 1.5cm kiri-kanan simetris dan proporsional */
            margin: 1.0cm 1.5cm 1.2cm 1.5cm;
        }
        * {
            box-sizing: border-box;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 9pt;
            line-height: 1.35;
            color: #1e293b;
            margin: 0;
            padding: 0;
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
            font-size: 12pt;
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
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            page-break-inside: avoid;
        }
        .info-table td {
            padding: 3px 2px;
            vertical-align: top;
            font-size: 9pt;
        }
        .label-col {
            width: 135px;
            color: #334155;
            font-weight: 600;
        }
        .colon-col {
            width: 8px;
            text-align: center;
            color: #334155;
            font-weight: bold;
        }

        .content-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            page-break-inside: avoid;
        }
        .content-table th, .content-table td {
            border: 1px solid #cbd5e1;
            padding: 5px 8px;
            font-size: 8.5pt;
        }
        .content-table th {
            background-color: #f1f5f9;
            font-weight: 700;
            text-align: left;
            text-transform: uppercase;
            font-size: 8pt;
            letter-spacing: 0.3px;
            color: #1e293b;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }

        .amount-box {
            background-color: #f8fafc;
            border: 1.5px solid #0f172a;
            padding: 6px 12px;
            margin-bottom: 12px;
            border-radius: 4px;
            page-break-inside: avoid;
        }

        /* Tabel Tanda Tangan: 3 Kolom Sejajar Sempurna */
        .signatures-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            text-align: center;
            page-break-inside: avoid;
        }
        .signatures-table td {
            vertical-align: top;
            width: 33.33%;
            font-size: 8.5pt;
            padding: 0 6px;
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
            padding-bottom: 4px;
        }
        .sign-space {
            height: 84px;
        }
        .qr-wrapper {
            margin: 2px auto 1px auto;
            text-align: center;
            line-height: 1;
        }
        .qr-img {
            display: inline-block;
            width: 78px;
            height: 78px;
            border: none;
            background-color: #ffffff;
        }
        .qr-hint {
            font-size: 6pt;
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
        .signer-title {
            font-size: 8pt;
            color: #64748b;
            margin-top: 1px;
        }
        .doc-footer {
            border-top: 1px dashed #e2e8f0;
            margin-top: 12px;
            padding-top: 3px;
            font-size: 7pt;
            color: #94a3b8;
            text-align: center;
        }
    </style>
</head>
<body>
    {{-- Header Kop Surat Sekolah Resmi dari Sistem --}}
    @if(!empty($kopPath) && file_exists($kopPath))
        <div class="header-kop">
            <img src="{{ $kopPath }}" class="kop-image" alt="Kop Surat">
        </div>
    @elseif(!empty($school->kop_surat_path) && file_exists(public_path('storage/' . $school->kop_surat_path)))
        <div class="header-kop">
            <img src="{{ public_path('storage/' . $school->kop_surat_path) }}" class="kop-image" alt="Kop Surat">
        </div>
    @endif

    {{-- Judul Kwitansi Penyaluran --}}
    <div class="doc-title-block">
        <div class="doc-title">BUKTI PENYALURAN BEASISWA ORANG TUA ASUH</div>
        <div class="doc-nomor">Nomor Bukti: {{ $alokasi->nomor_kwitansi }}</div>
    </div>

    {{-- Informasi Siswa & Penyaluran --}}
    <table class="info-table">
        <tr>
            <td class="label-col">Nama Siswa Penerima</td>
            <td class="colon-col">:</td>
            <td><strong>{{ $alokasi->siswa->nama }}</strong></td>
            <td class="label-col">Tanggal Alokasi</td>
            <td class="colon-col">:</td>
            <td>{{ $alokasi->tanggal_alokasi->translatedFormat('d F Y') }}</td>
        </tr>
        <tr>
            <td class="label-col">NISN / NIPD</td>
            <td class="colon-col">:</td>
            <td>{{ $alokasi->siswa->nisn ?? '-' }} / {{ $alokasi->siswa->nipd ?? '-' }}</td>
            <td class="label-col">Periode Semester</td>
            <td class="colon-col">:</td>
            <td>{{ $alokasi->semester?->label ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label-col">Kelas / Rombel</td>
            <td class="colon-col">:</td>
            <td>{{ $alokasi->siswa->rombel?->nama ?? '-' }}</td>
            <td class="label-col">Sumber Pembiayaan</td>
            <td class="colon-col">:</td>
            <td>
                @if($alokasi->tipe_alokasi === 'rutin_semester')
                    Kelompok OTA: <strong>{{ $alokasi->kelompok?->nama_kelompok ?? '-' }}</strong>
                @else
                    <strong>Kas Donasi Insidental Fleksibel</strong>
                @endif
            </td>
        </tr>
    </table>

    {{-- Tabel Komponen Penyaluran (Tanpa Kolom Keterangan, Tanpa Baris Surplus) --}}
    <table class="content-table">
        <thead>
            <tr>
                <th style="width: 28px;" class="text-center">No</th>
                <th>Komponen</th>
                <th style="width: 160px;">Bulan / Periode</th>
                <th style="width: 140px;" class="text-right">Nominal</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @foreach($alokasi->details as $item)
            <tr>
                <td class="text-center">{{ $no++ }}</td>
                <td>
                    <strong>{{ $item->nama_komponen }}</strong>
                </td>
                <td>
                    @if($item->bulan)
                        Bulan {{ $item->bulan }} {{ $item->tahun ?? '' }}
                    @else
                        -
                    @endif
                </td>
                <td class="text-right font-medium">Rp {{ number_format($item->nominal, 0, ',', '.') }}</td>
            </tr>
            @endforeach
            <tr style="background-color: #f8fafc; font-weight: bold;">
                <td colspan="3" class="text-right" style="padding-right: 10px;">TOTAL DISALURKAN KE SISWA:</td>
                <td class="text-right" style="font-size: 9.5pt; color: #0f172a;">Rp {{ number_format($alokasi->total_nominal, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    {{-- Kotak Total Dana Penyaluran & Terbilang --}}
    <div class="amount-box">
        <div style="font-size: 11pt; font-weight: 800; color: #0f172a;">
            Total Dana Penyaluran: Rp {{ number_format($alokasi->total_nominal, 0, ',', '.') }},-
        </div>
        <div style="font-style: italic; color: #334155; margin-top: 2px; font-size: 8.5pt;">
            # Terbilang: {{ $terbilang }} #
        </div>
        @if(!empty($alokasi->catatan))
        <div style="font-size: 8pt; color: #475569; margin-top: 3px; font-style: italic;">
            Catatan: {{ $alokasi->catatan }}
        </div>
        @endif
    </div>

    {{-- Tabel Tanda Tangan: 3 Kolom Sejajar Sempurna --}}
    <table class="signatures-table">
        {{-- Baris 1: Tanggal di atas Kolom Kepala Sekolah --}}
        <tr>
            <td></td>
            <td></td>
            <td>
                <div class="sig-date">{{ $titimangsa }}</div>
            </td>
        </tr>
        {{-- Baris 2: Jabatan Penanda Tangan (Sejajar Sempurna) --}}
        <tr>
            <td class="sig-title">Penerima Manfaat / Siswa,</td>
            <td class="sig-title">Pengelola / Bendahara OTA,</td>
            <td class="sig-title">Mengetahui, Kepala Sekolah,</td>
        </tr>
        {{-- Baris 3: Ruang Tanda Tangan & QR Code Verifikasi --}}
        <tr>
            <td style="vertical-align: middle;">
                <div class="sign-space"></div>
            </td>
            <td style="vertical-align: middle;">
                <div class="qr-wrapper">
                    @if(!empty($qrCodePng))
                        <img src="{{ $qrCodePng }}" class="qr-img" alt="QR Verifikasi">
                    @endif
                </div>
                <div class="qr-hint">Scan untuk Verifikasi Sah</div>
            </td>
            <td style="vertical-align: middle;">
                <div class="sign-space"></div>
            </td>
        </tr>
        {{-- Baris 4: Nama Penanda Tangan (Sejajar Sempurna) --}}
        <tr>
            <td style="vertical-align: top;">
                <div class="signer-name">{{ $alokasi->siswa->nama }}</div>
                <div class="signer-title">NISN: {{ $alokasi->siswa->nisn ?? '-' }}</div>
            </td>
            <td style="vertical-align: top;">
                <div class="signer-name">{{ $bendaharaNama }}</div>
                <div class="signer-title">Bendahara Program OTA</div>
            </td>
            <td style="vertical-align: top;">
                <div class="signer-name">{{ $kepsekNama }}</div>
                <div class="signer-title">NIP: {{ $kepsekNip }}</div>
            </td>
        </tr>
    </table>

    <div class="doc-footer">
        Dokumen bukti penyaluran elektronik sah • Verifikasi keaslian via scan barcode QR Code di atas
    </div>
</body>
</html>
