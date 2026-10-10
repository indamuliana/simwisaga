<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Rekapitulasi Presensi Kehadiran Siswa</title>
    <style>
        @page {
            margin: 1cm 1.2cm 1.5cm 1.2cm;
            @bottom-right {
                content: "Halaman " counter(page) " dari " counter(pages);
                font-size: 8pt;
                color: #6b7280;
            }
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 9pt;
            color: #1f2937;
            line-height: 1.3;
            margin: 0;
            padding: 0;
        }
        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .mb-1 { margin-bottom: 0.25rem; }
        .mb-2 { margin-bottom: 0.5rem; }
        .mb-4 { margin-bottom: 1rem; }
        .w-full { width: 100%; }

        .header-kop {
            width: 100%;
            margin-bottom: 12px;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 8px;
            text-align: center;
        }
        .kop-image {
            width: 100%;
            max-height: 80px;
            object-fit: contain;
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

        .title-block {
            text-align: center;
            margin-bottom: 12px;
        }
        .document-title {
            margin: 0 0 4px 0;
            font-size: 12pt;
            font-weight: bold;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        .header-info {
            font-size: 8.5pt;
            color: #4b5563;
            margin-top: 2px;
        }

        table.content-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8.5pt;
        }
        table.content-table th, table.content-table td {
            border: 1px solid #9ca3af;
            padding: 4px 4px;
        }
        table.content-table th {
            background-color: #f3f4f6;
            font-weight: bold;
            text-align: center;
            color: #111827;
        }
        .bg-light { background-color: #fafafa; }
        .badge-danger { color: #dc2626; font-weight: bold; }

        .signature-block {
            margin-top: 20px;
            page-break-inside: avoid;
            width: 100%;
        }
        .sign-table {
            width: 100%;
            border-collapse: collapse;
        }
        .sign-table td {
            border: none;
            vertical-align: top;
            font-size: 9pt;
        }
        .sign-space {
            height: 50px;
        }
        .signer-name {
            font-weight: bold;
            text-decoration: underline;
        }
        .signer-nip {
            font-size: 8pt;
            color: #4b5563;
            margin-top: 1px;
        }

        .footer-note {
            margin-top: 15px;
            font-size: 7.5pt;
            color: #9ca3af;
            border-top: 1px solid #e5e7eb;
            padding-top: 3px;
        }
    </style>
</head>
<body>
    {{-- Kop Surat --}}
    <div class="header-kop">
        @if(!empty($school->kop_surat_path) && file_exists(public_path('storage/' . $school->kop_surat_path)))
            <img src="{{ public_path('storage/' . $school->kop_surat_path) }}" class="kop-image" alt="Kop Surat">
        @else
            <p class="kop-text-yayasan">YAYASAN PRAWITAMA</p>
            <h1 class="kop-text-sekolah">{{ $school->nama ?? 'SMK WIKRAMA 1 GARUT' }}</h1>
            <p class="kop-text-kontak">
                {{ $school->alamat ?? 'Jl. Otto Iskandardinata, Kp. Tanjung Kidul, Tarogong Kidul, Garut' }} |
                Telp: {{ $school->telepon ?? '(0262) 232145' }} | Email: {{ $school->email ?? 'smkwikrama1garut@gmail.com' }}
            </p>
        @endif
    </div>

    {{-- Judul & Metadata Filter --}}
    <div class="title-block">
        <h2 class="document-title">REKAPITULASI PRESENSI KEHADIRAN SISWA</h2>
        <div class="header-info">
            <strong>Periode:</strong> {{ $periode_label }} &nbsp;|&nbsp; 
            <strong>Total Hari Efektif Sekolah:</strong> {{ $hari_efektif }} Hari
        </div>
        <div class="header-info">
            <strong>Rombel:</strong> {{ $rombel_nama }} &nbsp;|&nbsp; 
            <strong>Rayon:</strong> {{ $rayon_nama }} &nbsp;|&nbsp; 
            <strong>Gender:</strong> {{ $gender_nama }} &nbsp;|&nbsp; 
            <strong>Program:</strong> {{ $program_nama }}
        </div>
    </div>

    {{-- Tabel Rekap Absensi (11 Kolom Output) --}}
    <table class="content-table">
        <thead>
            <tr>
                <th style="width: 3%;">No</th>
                <th style="width: 9%;">NIS</th>
                <th style="width: 19%;" class="text-left">Nama Siswa</th>
                <th style="width: 10%;">Rombel</th>
                <th style="width: 4%;">H</th>
                <th style="width: 4%;">S</th>
                <th style="width: 4%;">I</th>
                <th style="width: 4%;">A</th>
                <th style="width: 14%;">Tanggal S</th>
                <th style="width: 14%;">Tanggal I</th>
                <th style="width: 14%;">Tanggal A</th>
                <th style="width: 5%;">Hadir %</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rekap as $index => $row)
                <tr class="{{ $index % 2 === 1 ? 'bg-light' : '' }}">
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center" style="font-family: monospace;">{{ $row['nis'] }}</td>
                    <td class="text-left" style="padding-left: 5px;">{{ $row['nama'] }}</td>
                    <td class="text-center">{{ $row['rombel'] }}</td>
                    <td class="text-center font-bold" style="color: #059669;">{{ $row['h'] }}</td>
                    <td class="text-center" style="color: #d97706;">{{ $row['s'] }}</td>
                    <td class="text-center" style="color: #2563eb;">{{ $row['i'] }}</td>
                    <td class="text-center {{ $row['a'] >= 3 ? 'badge-danger' : '' }}">{{ $row['a'] }}</td>
                    <td class="text-left" style="padding-left: 4px; font-size: 7.5pt;">{{ $row['tgl_s'] }}</td>
                    <td class="text-left" style="padding-left: 4px; font-size: 7.5pt;">{{ $row['tgl_i'] }}</td>
                    <td class="text-left" style="padding-left: 4px; font-size: 7.5pt; {{ $row['a'] >= 3 ? 'color: #dc2626; font-weight: bold;' : '' }}">{{ $row['tgl_a'] }}</td>
                    <td class="text-center font-bold">{{ $row['persentase'] }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="12" class="text-center" style="padding: 20px; color: #6b7280;">
                        Tidak ada data siswa atau absensi untuk filter dan periode yang dipilih.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Tanda Tangan --}}
    <div class="signature-block">
        <table class="sign-table">
            <tr>
                <td style="width: 65%;"></td>
                <td style="width: 35%; text-align: center;">
                    <div>Garut, {{ now()->translatedFormat('d F Y') }}</div>
                    <div style="margin-top: 3px;">Mengetahui,<br><strong>Kepala Sekolah</strong></div>
                    <div class="sign-space"></div>
                    <div class="signer-name">{{ $school->kepala_sekolah ?? 'H. Dedik, M.Pd.' }}</div>
                    <div class="signer-nip">NIP: {{ $school->nip_kepsek ?? '-' }}</div>
                </td>
            </tr>
        </table>
    </div>

    {{-- Catatan Kaki --}}
    <div class="footer-note">
        <table style="width: 100%; border: none;">
            <tr style="border: none;">
                <td style="border: none; text-align: left; font-size: 7pt; color: #9ca3af;">
                    Dicetak dari Sistem Informasi Pengelolaan Sekolah (SIRAGA) SMK Wikrama 1 Garut
                </td>
                <td style="border: none; text-align: right; font-size: 7pt; color: #9ca3af;">
                    Waktu Cetak: {{ now()->translatedFormat('d/m/Y H:i') }} WIB
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
