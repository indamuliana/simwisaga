<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>DAFTAR HADIR KELOMPOK - {{ strtoupper($kelompok->nama) }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 7mm 7mm 14mm 7mm;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 7.5pt;
            color: #0f172a;
            line-height: 1.15;
            margin: 0;
            padding: 0;
        }
        
        .header-kop {
            width: 100%;
            margin-bottom: 8px;
            text-align: center;
        }
        .kop-image {
            width: 100%;
            height: auto;
            display: block;
        }
        .kop-text-fallback {
            border-bottom: 2.5px double #0f172a;
            padding-bottom: 6px;
            text-align: center;
        }
        .kop-text-yayasan {
            font-size: 9.5pt;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin: 0;
            color: #334155;
        }
        .kop-text-sekolah {
            font-size: 13pt;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin: 2px 0;
            color: #0f172a;
        }
        .kop-text-kontak {
            font-size: 7.5pt;
            color: #475569;
            margin: 0;
        }

        .header-title {
            text-align: center;
            margin-top: 8px;
            margin-bottom: 10px;
        }
        .header-title h1 {
            font-size: 11pt;
            font-weight: bold;
            margin: 1px 0;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        .header-title h2 {
            font-size: 9.5pt;
            font-weight: 600;
            margin: 1px 0;
            color: #1e293b;
        }

        .meta-table {
            width: 100%;
            margin-bottom: 6px;
            border-collapse: collapse;
            font-size: 8.5pt;
        }
        .meta-table td {
            padding: 1.5px 0;
            vertical-align: top;
        }

        table.grid-table {
            width: 100%;
            border-collapse: collapse;
        }
        table.grid-table th, table.grid-table td {
            border: 0.5pt solid #111;
            box-sizing: border-box;
            overflow: hidden;
        }
        table.grid-table thead th {
            background-color: #f1f5f9;
            font-weight: bold;
            text-align: center;
            vertical-align: middle;
            font-size: 7.5pt;
            padding: 3px 2px;
        }
        table.grid-table tbody td {
            font-size: 7.5pt;
            height: 18px;
            padding: 2px 3px;
            vertical-align: middle;
        }
        .text-center { text-align: center; }
        .text-left { text-align: left; }

        .footer-ttd {
            width: 100%;
            margin-top: 15px;
            border-collapse: collapse;
        }
        .footer-ttd td {
            font-size: 8.5pt;
            vertical-align: top;
        }
    </style>
</head>
<body>

    @php
        $school = \App\Modules\Sistem\Models\School::current();
        $kopPath = $school->kop_surat_path ? public_path('storage/' . $school->kop_surat_path) : null;
    @endphp

    <div class="header-kop">
        @if ($kopPath && file_exists($kopPath))
            <img src="{{ $kopPath }}" class="kop-image" alt="Kop Surat">
        @else
            <div class="kop-text-fallback">
                <p class="kop-text-yayasan">YAYASAN PRAWITAMA</p>
                <h2 class="kop-text-sekolah">{{ $school->nama ?? 'SMK WIKRAMA 1 GARUT' }}</h2>
                <p class="kop-text-kontak">Jl. Otto Iskandardinata Kp. Tanjung Kidul, RT.003/RW.007, Pananjung, Kec. Tarogong Kaler, Kabupaten Garut, Jawa Barat 44151</p>
            </div>
        @endif
    </div>

    <div class="header-title">
        <h1>DAFTAR HADIR SISWA - KELOMPOK KHUSUS</h1>
        <h2>KELOMPOK: {{ strtoupper($kelompok->nama) }} {{ $kelompok->kode ? '(' . $kelompok->kode . ')' : '' }}</h2>
        <div style="font-size: 8pt; color: #475569; margin-top: 2px;">TAHUN PELAJARAN: {{ $tahunPelajaran }}</div>
    </div>

    <table class="meta-table">
        <tr>
            <td style="width: 15%; font-weight: bold;">Kategori Kelompok</td>
            <td style="width: 35%;">: {{ $kelompok->kategori ?: '-' }}</td>
            <td style="width: 15%; font-weight: bold;">Materi / Kegiatan</td>
            <td style="width: 35%;">: {{ $mapel ?: 'Kegiatan Kelompok' }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold;">Guru Pembimbing</td>
            <td>: {{ $namaGuru ?: '-' }}</td>
            <td style="font-weight: bold;">Total Siswa</td>
            <td>: {{ $siswas->count() }} Orang {{ $kelompok->kapasitas ? '(Kuota maks: ' . $kelompok->kapasitas . ')' : '' }}</td>
        </tr>
    </table>

    <table class="grid-table">
        <thead>
            <tr>
                <th rowspan="2" style="width: 20px;">NO</th>
                <th rowspan="2" style="width: 55px;">NIS</th>
                <th rowspan="2">NAMA SISWA</th>
                <th rowspan="2" style="width: 20px;">JK</th>
                <th rowspan="2" style="width: 65px;">ROMBEL</th>
                <th colspan="{{ $jumlahKolom }}" style="text-align: center;">PERTEMUAN / TANGGAL KEHADIRAN</th>
            </tr>
            <tr>
                @for ($k = 1; $k <= $jumlahKolom; $k++)
                    <th style="width: 18px; font-weight: normal; font-size: 7pt;">{{ $k }}</th>
                @endfor
            </tr>
        </thead>
        <tbody>
            @forelse ($siswas as $idx => $siswa)
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td class="text-center">{{ $siswa->nipd ?: $siswa->nisn ?: '-' }}</td>
                    <td>
                        {{ $siswa->nama }}
                        @if (!empty($siswa->pivot->keterangan))
                            <span style="font-size: 6.5pt; color: #0284c7; font-style: italic;">({{ $siswa->pivot->keterangan }})</span>
                        @endif
                    </td>
                    <td class="text-center">{{ $siswa->jk }}</td>
                    <td class="text-center" style="font-size: 7pt;">{{ $siswa->rombel?->nama ?? '-' }}</td>
                    @for ($k = 1; $k <= $jumlahKolom; $k++)
                        <td></td>
                    @endfor
                </tr>
            @empty
                <tr>
                    <td colspan="{{ 5 + $jumlahKolom }}" class="text-center" style="padding: 10px; color: #64748b;">
                        Belum ada siswa terdaftar di kelompok ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <table class="footer-ttd">
        <tr>
            <td style="width: 60%;"></td>
            <td style="width: 40%; text-align: left; padding-left: 20px;">
                <p style="margin: 0;">Garut, ......................................... 20....</p>
                <p style="margin: 2px 0 45px 0;">Guru / Pembimbing Kelompok,</p>
                <p style="margin: 0; font-weight: bold; text-decoration: underline;">{{ $namaGuru ?: '......................................................' }}</p>
                @if (!empty($nipGuru))
                    <p style="margin: 2px 0 0 0; font-size: 7.5pt;">NIP. {{ $nipGuru }}</p>
                @endif
            </td>
        </tr>
    </table>

</body>
</html>
