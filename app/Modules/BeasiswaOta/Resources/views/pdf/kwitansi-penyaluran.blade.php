<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Kwitansi Penyaluran Beasiswa - {{ $alokasi->nomor_kwitansi }}</title>
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
            border-bottom: 3px double #0f172a;
            padding-bottom: 8px;
            margin-bottom: 14px;
            text-align: center;
        }
        .kop-text-yayasan {
            font-size: 10pt;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin: 0;
            color: #475569;
        }
        .kop-text-sekolah {
            font-size: 15pt;
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
            margin-bottom: 16px;
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
            margin-top: 3px;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
        }
        .info-table td {
            padding: 3px 4px;
            vertical-align: top;
            font-size: 9.5pt;
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
        .content-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
        }
        .content-table th, .content-table td {
            border: 1px solid #cbd5e1;
            padding: 6px 8px;
            font-size: 9pt;
        }
        .content-table th {
            background-color: #f1f5f9;
            font-weight: bold;
            text-align: left;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .amount-box {
            background-color: #f8fafc;
            border: 1px solid #cbd5e1;
            padding: 8px 12px;
            margin-bottom: 16px;
            border-radius: 4px;
        }
        .signatures-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            text-align: center;
            page-break-inside: avoid;
        }
        .signatures-table td {
            vertical-align: top;
            width: 33.33%;
            font-size: 9pt;
            padding: 0 6px;
        }
        .sign-space {
            height: 60px;
        }
        .signer-name {
            font-weight: bold;
            text-decoration: underline;
        }
        .signer-title {
            font-size: 8.5pt;
            color: #475569;
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
        <div class="doc-title">BUKTI PENYALURAN BEASISWA ORANG TUA ASUH</div>
        <div class="doc-nomor">Nomor: {{ $alokasi->nomor_kwitansi }}</div>
    </div>

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

    <table class="content-table">
        <thead>
            <tr>
                <th style="width: 30px;" class="text-center">No</th>
                <th>Komponen Pembiayaan</th>
                <th style="width: 140px;">Bulan / Periode</th>
                <th style="width: 120px;" class="text-right">Nominal</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @foreach($alokasi->details as $item)
            <tr>
                <td class="text-center">{{ $no++ }}</td>
                <td>
                    <strong>{{ $item->nama_komponen }}</strong>
                    <span style="font-size: 8pt; color: #64748b; text-transform: uppercase;">({{ $item->jenis_komponen }})</span>
                </td>
                <td>
                    @if($item->bulan)
                        Bulan {{ $item->bulan }} {{ $item->tahun ?? '' }}
                    @else
                        -
                    @endif
                </td>
                <td class="text-right">Rp {{ number_format($item->nominal, 0, ',', '.') }}</td>
                <td>{{ $item->keterangan ?? '-' }}</td>
            </tr>
            @endforeach
            <tr style="background-color: #f8fafc; font-weight: bold;">
                <td colspan="3" class="text-right">Total Disalurkan ke Siswa:</td>
                <td class="text-right">Rp {{ number_format($alokasi->total_nominal, 0, ',', '.') }}</td>
                <td></td>
            </tr>
            @if($alokasi->surplus_dialihkan > 0)
            <tr style="background-color: #fefce8; font-style: italic;">
                <td colspan="3" class="text-right">Surplus Kelompok Dialihkan ke Kas Insidental:</td>
                <td class="text-right">Rp {{ number_format($alokasi->surplus_dialihkan, 0, ',', '.') }}</td>
                <td>Subsidi silang OTA</td>
            </tr>
            @endif
        </tbody>
    </table>

    <div class="amount-box">
        <strong>Total Dana Penyaluran: </strong>
        <span style="font-size: 11pt; font-weight: bold; color: #0f172a;">
            Rp {{ number_format($alokasi->total_nominal, 0, ',', '.') }},-
        </span>
        <div style="font-style: italic; color: #475569; margin-top: 2px;">
            Terbilang: {{ $terbilang }}
        </div>
        @if(!empty($alokasi->catatan))
        <div style="font-size: 8.5pt; color: #334155; margin-top: 4px;">
            Catatan: {{ $alokasi->catatan }}
        </div>
        @endif
    </div>

    <table class="signatures-table">
        <tr>
            <td>
                <div>Penerima Manfaat / Siswa,</div>
                <div class="sign-space"></div>
                <div class="signer-name">{{ $alokasi->siswa->nama }}</div>
                <div class="signer-title">NISN: {{ $alokasi->siswa->nisn ?? '-' }}</div>
            </td>
            <td>
                <div>Pengelola / Bendahara OTA,</div>
                <div class="sign-space"></div>
                <div class="signer-name">{{ $bendaharaNama }}</div>
                <div class="signer-title">Pengelola Program</div>
            </td>
            <td>
                <div>{{ $titimangsa }}</div>
                <div>Mengetahui, Kepala Sekolah</div>
                <div class="sign-space"></div>
                <div class="signer-name">{{ $kepsekNama }}</div>
                <div class="signer-title">NIP: {{ $kepsekNip }}</div>
            </td>
        </tr>
    </table>
</body>
</html>
