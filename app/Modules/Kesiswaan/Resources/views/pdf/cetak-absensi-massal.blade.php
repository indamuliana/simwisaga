<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>DAFTAR HADIR MURID MASSAL - SMKS WIKRAMA 1 GARUT</title>
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
        
        /* Kop Surat Resmi — Menjaga Rasio Alami Gambar */
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
            font-size: 14pt;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin: 2px 0 3px 0;
            color: #0f172a;
        }
        .kop-text-kontak {
            font-size: 7.5pt;
            color: #475569;
            margin: 0;
        }

        /* Judul Dokumen */
        .header-title {
            text-align: center;
            margin-top: 10px;
            margin-bottom: 12px;
        }
        .header-title h1 {
            font-size: 11pt;
            font-weight: bold;
            margin: 2px 0;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        /* Informasi Metadata di Atas Tabel */
        .meta-table {
            width: 100%;
            margin-bottom: 4px;
            border-collapse: collapse;
            font-family: "Times New Roman", Times, serif;
            font-size: 10pt;
        }
        .meta-table td {
            padding: 1px 0;
            vertical-align: top;
        }

        /* Tabel Presensi Grid */
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
            font-size: 6.5pt;
            padding: 2px 1px;
        }
        table.grid-table thead th.col-data-header {
            font-family: Tahoma, Geneva, sans-serif;
            text-align: center;
        }
        table.grid-table thead th.col-dates-super {
            font-size: 5.5pt;
            letter-spacing: 0.5px;
            padding: 2px 0;
            font-weight: normal;
            background-color: #f8fafc;
        }
        table.grid-table thead th.col-day-empty {
            height: 12px;
            padding: 0;
            background-color: #ffffff;
        }

        /* Styling Sel Data Siswa */
        table.grid-table tbody td {
            height: 15px;
            font-family: Tahoma, Geneva, sans-serif;
            font-weight: normal;
        }
        table.grid-table tbody td.col-no-td {
            text-align: center;
            font-size: 6.5pt;
            padding: 1px;
        }
        table.grid-table tbody td.col-nis-td {
            text-align: center;
            font-size: 6.5pt;
            padding: 1px;
            white-space: nowrap;
        }
        table.grid-table tbody td.col-nama-td {
            text-align: left;
            padding: 1px 3px;
            font-size: 6.5pt;
            line-height: 1.15;
            white-space: nowrap;
        }
        table.grid-table tbody td.col-jk-td {
            text-align: center;
            font-size: 6.5pt;
            padding: 1px;
        }
        table.grid-table tbody td.col-prog-td {
            text-align: center;
            font-size: 6.5pt;
            padding: 1px;
        }
        table.grid-table tbody td.col-rayon-td {
            text-align: center;
            font-size: 6pt;
            padding: 1px 2px;
            white-space: nowrap;
        }
        table.grid-table tbody td.col-day-td {
            text-align: center;
            padding: 0;
            font-family: Arial, Helvetica, sans-serif;
        }

        /* Di Bawah Tabel */
        .summary-left {
            margin-top: 12px;
            font-family: "Times New Roman", Times, serif;
            font-size: 11pt;
            text-align: left;
            color: #1e293b;
        }
        .summary-left .prog-hint {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 6.5pt;
            color: #64748b;
            margin-top: 2px;
        }

        /* Tanda Tangan */
        .signature-box {
            float: right;
            width: 250px;
            text-align: center;
            font-family: "Times New Roman", Times, serif;
            font-size: 11pt;
            margin-top: 35px;
            page-break-inside: avoid;
        }
        .signature-line {
            border-bottom: 1px solid #000;
            width: 200px;
            margin: 55px auto 2px auto;
        }
        .signature-name {
            font-family: "Times New Roman", Times, serif;
            font-size: 11pt;
            font-weight: bold;
        }
        .signature-nip {
            font-family: "Times New Roman", Times, serif;
            font-size: 11pt;
            color: #444;
            margin-top: 1px;
        }

        /* Footer Selalu di Paling Bawah Kertas */
        .footer-note {
            position: fixed;
            bottom: -9mm;
            left: 0;
            right: 0;
            height: 13px;
            font-size: 6.5pt;
            color: #64748b;
            border-top: 0.5pt solid #cbd5e1;
            padding-top: 2px;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>

    @php
        $school = \App\Modules\Sistem\Models\School::current();
        $kopSuratExists = !empty($school->kop_surat_path) && file_exists(public_path('storage/' . $school->kop_surat_path));
    @endphp

    @foreach($groups as $gIdx => $group)
        <div class="sheet-group">
            {{-- Kop Surat Resmi Sekolah --}}
            <div class="header-kop">
                @if($kopSuratExists)
                    <img src="{{ public_path('storage/' . $school->kop_surat_path) }}" class="kop-image" alt="Kop Surat">
                @else
                    <div class="kop-text-fallback">
                        <p class="kop-text-yayasan">YAYASAN PRAWITAMA</p>
                        <h1 class="kop-text-sekolah">{{ $school->nama ?? 'SMK WIKRAMA 1 GARUT' }}</h1>
                        <p class="kop-text-kontak">
                            {{ $school->alamat ?? 'Jl. Otto Iskandardinata, Kp. Tanjung Kidul, Tarogong Kidul, Garut' }} | 
                            Telp: {{ $school->telepon ?? '(0262) 232145' }} | Email: {{ $school->email ?? 'smkwikrama1garut@gmail.com' }}
                        </p>
                    </div>
                @endif
            </div>

            {{-- Judul Dokumen --}}
            <div class="header-title">
                <h1>DAFTAR HADIR MURID</h1>
                <h1>SMKS WIKRAMA 1 GARUT</h1>
                <h1>TAHUN PELAJARAN {{ $tahunPelajaran }}</h1>
            </div>

            {{-- Bar Informasi Filter & Mapel --}}
            <table class="meta-table">
                <tr>
                    <td style="width: 55%;">
                        <b>Kriteria / Kelas:</b> {{ $group['label'] }}
                    </td>
                    <td style="width: 45%; text-align: right;">
                        <b>Mata Pelajaran:</b> {{ $mapel ?: '........................................................' }}
                    </td>
                </tr>
            </table>

            {{-- Tabel Data Siswa Portrait: Lebar menyesuaikan isi --}}
            <table class="grid-table">
                <thead>
                    <tr>
                        <th rowspan="2" class="col-data-header" style="width: 20px; white-space: nowrap;">No</th>
                        <th rowspan="2" class="col-data-header" style="width: 50px; white-space: nowrap;">NIS</th>
                        <th rowspan="2" class="col-data-header" style="width: 140px; white-space: nowrap;">Nama Siswa</th>
                        <th rowspan="2" class="col-data-header" style="width: 20px; white-space: nowrap;">JK</th>
                        <th rowspan="2" class="col-data-header" title="Program: U = Unggulan, R = Reguler" style="width: 25px; white-space: nowrap;">Prog</th>
                        <th rowspan="2" class="col-data-header" style="width: 40px; white-space: nowrap;">Rayon</th>
                        <th colspan="{{ $jumlahKolom }}" class="col-dates-super">........................................................</th>
                    </tr>
                    <tr>
                        @for($d = 1; $d <= $jumlahKolom; $d++)
                            <th class="col-day-empty">&nbsp;</th>
                        @endfor
                    </tr>
                </thead>
                <tbody>
                    @foreach($group['siswas'] as $idx => $s)
                        @php
                            $progNama = strtolower($s->program?->nama ?? '');
                            $progSingkat = str_contains($progNama, 'unggul') ? 'U' : (str_contains($progNama, 'regul') ? 'R' : ($s->program ? substr($s->program->nama, 0, 1) : '-'));
                        @endphp
                        <tr>
                            <td class="col-no-td">{{ $idx + 1 }}</td>
                            <td class="col-nis-td">{{ $s->nipd ?: $s->nisn }}</td>
                            <td class="col-nama-td">{{ $s->nama }}</td>
                            <td class="col-jk-td">{{ $s->jk }}</td>
                            <td class="col-prog-td">{{ $progSingkat }}</td>
                            <td class="col-rayon-td">{{ $s->rayon?->nama ?? '-' }}</td>
                            @for($d = 1; $d <= $jumlahKolom; $d++)
                                <td class="col-day-td">&nbsp;</td>
                            @endfor
                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{-- Di Bawah Tabel: Jumlah Siswa di Kiri & Tanda Tangan di Kanan --}}
            <div class="summary-left">
                Jumlah: <b>{{ $group['siswas']->count() }}</b> Siswa (L: {{ $group['siswas']->where('jk', 'L')->count() }}, P: {{ $group['siswas']->where('jk', 'P')->count() }})
                <div class="prog-hint">* Program: <b>U</b> = Unggulan, <b>R</b> = Reguler</div>
            </div>

            <div class="signature-box">
                <div>Garut, ........................... ({{ date('Y') }})</div>
                <div style="margin-top: 3px;">Guru Mata Pelajaran,</div>
                <div class="signature-line"></div>
                <div class="signature-name">
                    {{ $namaGuru ?: '( ................................................ )' }}
                </div>
                <div class="signature-nip">
                    NIP. {{ $nipGuru ?: '..........................................' }}
                </div>
            </div>
            <div style="clear: both;"></div>
        </div>

        @if(!$loop->last)
            <div class="page-break"></div>
        @endif
    @endforeach

    {{-- Footer Selalu di Paling Bawah Kertas --}}
    <div class="footer-note">
        <table style="width: 100%; border: none; border-collapse: collapse;">
            <tr>
                <td style="text-align: left; border: none; padding: 0;">
                    Dicetak dari Sistem Informasi Pengelolaan Sekolah (SIRAGA) SMKS Wikrama 1 Garut
                </td>
                <td style="text-align: right; border: none; padding: 0;">
                    Tanggal Cetak: {{ now()->translatedFormat('d/m/Y H:i') }}
                </td>
            </tr>
        </table>
    </div>

</body>
</html>
