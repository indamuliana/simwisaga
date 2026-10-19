<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kalender Pendidikan & Agenda Kegiatan Sekolah</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 1.5cm 1.5cm 1.5cm 1.5cm;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 10pt;
            line-height: 1.4;
            color: #1a1a1a;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .header h1 {
            font-size: 14pt;
            margin: 0;
            text-transform: uppercase;
            font-weight: bold;
        }
        .header h2 {
            font-size: 12pt;
            margin: 2px 0;
            font-weight: bold;
        }
        .header p {
            font-size: 8.5pt;
            margin: 2px 0;
            color: #444;
        }
        .title-block {
            text-align: center;
            margin: 15px 0;
        }
        .title-block h3 {
            font-size: 11pt;
            margin: 0;
            text-transform: uppercase;
            text-decoration: underline;
        }
        .title-block p {
            font-size: 9pt;
            margin: 3px 0;
            color: #555;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        th, td {
            border: 1px solid #777;
            padding: 5px 7px;
            font-size: 8.5pt;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
            text-align: left;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 7.5pt;
            font-weight: bold;
            color: #fff;
        }
        .badge-final {
            background-color: #166534;
            color: #fff;
        }
        .section-title {
            font-size: 9.5pt;
            font-weight: bold;
            margin: 12px 0 6px 0;
            text-transform: uppercase;
            border-left: 3px solid #014694;
            padding-left: 6px;
        }
        .ttd-table {
            width: 100%;
            margin-top: 30px;
            border: none;
        }
        .ttd-table td {
            border: none;
            padding: 0;
            vertical-align: top;
        }
    </style>
</head>
<body>
    {{-- Kop Surat --}}
    <div class="header">
        <h1>{{ $sekolah->nama ?? 'SMK WIKRAMA 1 GARUT' }}</h1>
        <h2>KALENDER PENDIDIKAN & AGENDA KEGIATAN RESMI</h2>
        <p>{{ $sekolah->alamat ?? 'Jl. Otista No. 123 Garut, Jawa Barat' }} | Telp: {{ $sekolah->telepon ?? '-' }} | Website: {{ $sekolah->website ?? '-' }}</p>
    </div>

    <div class="title-block">
        <h3>AGENDA KEGIATAN TAHUN AJARAN {{ $tahunAjaran->nama ?? date('Y').'/'.(date('Y')+1) }}</h3>
        <p>Dicetak pada: {{ now()->translatedFormat('l, d F Y H:i') }} WIB</p>
    </div>

    {{-- Daftar Agenda Kegiatan --}}
    <div class="section-title">I. DAFTAR AGENDA KEGIATAN SEKOLAH</div>
    <table>
        <thead>
            <tr>
                <th class="text-center" style="width: 25px;">No</th>
                <th style="width: 130px;">Tanggal / Waktu</th>
                <th>Nama Kegiatan</th>
                <th style="width: 100px;">Kategori</th>
                <th style="width: 90px;">Lokasi</th>
                <th class="text-center" style="width: 60px;">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($agendas as $idx => $agenda)
            <tr>
                <td class="text-center">{{ $idx + 1 }}</td>
                <td>
                    <strong>{{ $agenda->tgl_mulai->translatedFormat('d M Y') }}</strong>
                    @if($agenda->tgl_mulai->format('Y-m-d') !== $agenda->tgl_selesai->format('Y-m-d'))
                        <br>&mdash; {{ $agenda->tgl_selesai->translatedFormat('d M Y') }}
                    @endif
                    <div style="font-size: 7.5pt; color: #555;">
                        {{ $agenda->is_all_day ? 'Sepanjang Hari' : substr($agenda->jam_mulai,0,5).' - '.substr($agenda->jam_selesai,0,5).' WIB' }}
                    </div>
                </td>
                <td>
                    <strong>{{ $agenda->judul }}</strong>
                    @if($agenda->deskripsi)
                        <div style="font-size: 7.5pt; color: #555;">{{ $agenda->deskripsi }}</div>
                    @endif
                    <div style="font-size: 7.5pt; color: #777;">Audiens: {{ ucfirst($agenda->audiens) }}</div>
                </td>
                <td>
                    <span class="badge" style="background-color: {{ $agenda->display_warna }}">
                        {{ $agenda->kategori?->nama ?? 'Umum' }}
                    </span>
                </td>
                <td>{{ $agenda->lokasi ?: '-' }}</td>
                <td class="text-center">
                    @if($agenda->is_final)
                        <span class="badge badge-final">FINAL</span>
                    @else
                        <span style="font-size: 7.5pt; color: #b45309;">Draft</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center" style="padding: 15px; color: #777;">
                    Tidak ada agenda kegiatan yang terdata.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Daftar Hari Libur --}}
    @if($liburs->isNotEmpty())
    <div class="section-title">II. JADWAL HARI LIBUR & CUTI RESMI</div>
    <table>
        <thead>
            <tr>
                <th class="text-center" style="width: 25px;">No</th>
                <th style="width: 140px;">Rentang Tanggal</th>
                <th>Nama Hari Libur</th>
                <th style="width: 100px;">Jenis</th>
                <th class="text-center" style="width: 70px;">Libur KBM</th>
            </tr>
        </thead>
        <tbody>
            @foreach($liburs as $idx => $libur)
            <tr>
                <td class="text-center">{{ $idx + 1 }}</td>
                <td>
                    {{ $libur->tgl_mulai->translatedFormat('d M Y') }}
                    @if($libur->tgl_mulai->format('Y-m-d') !== $libur->tgl_selesai->format('Y-m-d'))
                        &mdash; {{ $libur->tgl_selesai->translatedFormat('d M Y') }}
                    @endif
                </td>
                <td>
                    <strong>{{ $libur->nama }}</strong>
                    @if($libur->keterangan)
                        <span style="font-size: 7.5pt; color: #666;">({{ $libur->keterangan }})</span>
                    @endif
                </td>
                <td>{{ ucfirst(str_replace('_', ' ', $libur->jenis)) }}</td>
                <td class="text-center">{{ $libur->is_kbm_libur ? 'Ya' : 'Tidak' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    {{-- Tanda Tangan Pengesahan --}}
    <table class="ttd-table">
        <tr>
            <td style="width: 60%;"></td>
            <td style="width: 40%; text-align: center;">
                <p>Garut, {{ now()->translatedFormat('d F Y') }}</p>
                <p style="font-weight: bold; margin-bottom: 50px;">Mengetahui,<br>Kepala Sekolah</p>
                <p style="font-weight: bold; text-decoration: underline;">
                    {{ $sekolah->nama_kepala_sekolah ?? 'Kepala Sekolah' }}
                </p>
                <p style="font-size: 8pt; color: #555;">
                    NIP. {{ $sekolah->nip_kepala_sekolah ?? '-' }}
                </p>
            </td>
        </tr>
    </table>
</body>
</html>
