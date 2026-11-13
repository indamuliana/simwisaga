<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Sertifikat Donatur OTA - {{ $donasi->donatur->nama }}</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 0;
        }
        * {
            box-sizing: border-box;
        }
        body {
            font-family: 'Georgia', 'Times New Roman', serif;
            margin: 0;
            padding: 0;
            color: #1e293b;
            background-color: #ffffff;
            page-break-inside: avoid;
            @if(!empty($bgBase64))
            background-image: url("{{ $bgBase64 }}");
            background-position: top left;
            background-repeat: no-repeat;
            background-size: 297mm 210mm;
            @endif
        }

        /* Fallback bingkai jika belum ada gambar template kustom yang diunggah */
        @if(empty($bgBase64))
        .certificate-wrapper {
            margin: 1.0cm 1.2cm;
            padding: 1.2cm 1.8cm;
            border: 5px double #1e3a8a;
            outline: 2px solid #ca8a04;
            outline-offset: -10px;
            background: #fafaf9;
            box-sizing: border-box;
            page-break-inside: avoid;
        }
        @endif

        /* Container Isi: Menggunakan margin horizontal simetris agar posisi teks presisi di tengah bingkai emas */
        .content-container {
            margin: 28mm 40mm 15mm 40mm;
            text-align: center;
            page-break-inside: avoid;
        }

        .cert-title {
            font-size: 28pt;
            font-weight: bold;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 3px;
            margin-top: 0;
            margin-bottom: 4px;
        }
        .cert-nomor {
            font-family: 'Courier New', Courier, monospace;
            font-size: 10pt;
            color: #475569;
            letter-spacing: 0.5px;
            margin-bottom: 20px;
            font-weight: bold;
        }
        .cert-subtitle {
            font-size: 12pt;
            font-style: italic;
            color: #475569;
            margin-bottom: 8px;
        }
        .recipient-name {
            font-size: 26pt;
            font-weight: bold;
            color: #1e3a8a;
            text-decoration: underline;
            margin: 8px 0 20px 0;
            letter-spacing: 0.5px;
        }
        .cert-body {
            font-size: 11pt;
            line-height: 1.75;
            color: #334155;
            max-width: 90%;
            margin: 0 auto 22px auto;
        }
        .cert-highlight {
            font-weight: bold;
            color: #0f172a;
        }

        /* Tabel Tanda Tangan & QR Code 3 Kolom Sejajar Sempurna */
        .signatures-table {
            width: 92%;
            margin: 20px auto 0 auto;
            border-collapse: collapse;
            text-align: center;
            page-break-inside: avoid;
        }
        .signatures-table td {
            vertical-align: top;
            padding: 0 4px;
        }
        .col-signer {
            width: 38%;
        }
        .col-qr {
            width: 24%;
        }
        .sig-date {
            font-size: 9.5pt;
            color: #334155;
            font-weight: 500;
            margin-bottom: 3px;
        }
        .sig-title {
            font-size: 9.5pt;
            font-weight: 600;
            color: #1e293b;
            padding-bottom: 3px;
        }
        .sign-space {
            height: 52px;
        }
        .signer-name {
            font-weight: bold;
            text-decoration: underline;
            font-size: 10.5pt;
            color: #0f172a;
            padding-top: 2px;
        }
        .signer-title {
            font-size: 8.5pt;
            color: #64748b;
            margin-top: 1px;
        }

        .qr-wrapper {
            margin: 0 auto;
            text-align: center;
            line-height: 1;
        }
        .qr-img {
            display: inline-block;
            width: 68px;
            height: 68px;
            border: none;
            background-color: #ffffff;
        }
        .qr-hint {
            font-size: 6.5pt;
            color: #64748b;
            letter-spacing: 0.3px;
            margin-top: 3px;
            text-transform: uppercase;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="{{ empty($bgBase64) ? 'certificate-wrapper' : '' }}">
        <div class="content-container">
            {{-- Judul Piagam & Nomor Resmi (Tanpa Logo/Kop Sekolah) --}}
            <div class="cert-title">PIAGAM PENGHARGAAN</div>
            <div class="cert-nomor">Nomor: {{ $nomorSertifikat }}</div>

            {{-- Penerima Piagam --}}
            <div class="cert-subtitle">Diberikan Sebagai Apresiasi Kepada:</div>
            <div class="recipient-name">{{ $donasi->donatur->nama }}</div>

            {{-- Kalimat Apresiasi (Tanpa Menyebutkan Nominal Uang Donasi) --}}
            <div class="cert-body">
                Atas keikhlasan, kepedulian, dan dedikasi yang tulus sebagai <strong>Orang Tua Asuh (OTA)</strong> 
                melalui <span class="cert-highlight">{{ $donasi->kelompok ? $donasi->kelompok->nama_kelompok : 'Program Peduli Pendidikan Insidental' }}</span> 
                @if($donasi->keterangan_periode)
                    periode <span class="cert-highlight">{{ $donasi->keterangan_periode }}</span>
                @endif
                dalam mendukung keberlangsungan pendidikan siswa di SMK Wikrama 1 Garut.
                <br>
                <em style="font-size: 9.5pt; color: #475569;">
                    Semoga Allah SWT / Tuhan Yang Maha Esa membalas segala amal kebaikan dengan keberkahan yang berlipat ganda.
                </em>
            </div>

            {{-- Tanda Tangan & QR Code Verifikasi Sah --}}
            <table class="signatures-table">
                {{-- Baris 1: Tanggal di sisi kanan --}}
                <tr>
                    <td class="col-signer"></td>
                    <td class="col-qr"></td>
                    <td class="col-signer">
                        <div class="sig-date">{{ $titimangsa }}</div>
                    </td>
                </tr>
                {{-- Baris 2: Jabatan Penanda Tangan (Sejajar Sempurna) --}}
                <tr>
                    <td class="col-signer">
                        <div class="sig-title">Koordinator Program OTA,</div>
                    </td>
                    <td class="col-qr"></td>
                    <td class="col-signer">
                        <div class="sig-title">Kepala Sekolah,</div>
                    </td>
                </tr>
                {{-- Baris 3: Ruang Tanda Tangan & QR Code di Tengah --}}
                <tr>
                    <td class="col-signer" style="vertical-align: middle;">
                        <div class="sign-space"></div>
                    </td>
                    <td class="col-qr" style="vertical-align: middle;">
                        <div class="qr-wrapper">
                            @if(!empty($qrCodePng))
                                <img src="{{ $qrCodePng }}" class="qr-img" alt="QR Verifikasi">
                            @endif
                        </div>
                        <div class="qr-hint">Scan untuk Verifikasi Sah</div>
                    </td>
                    <td class="col-signer" style="vertical-align: middle;">
                        <div class="sign-space"></div>
                    </td>
                </tr>
                {{-- Baris 4: Nama Penanda Tangan (Sejajar Sempurna) --}}
                <tr>
                    <td class="col-signer" style="vertical-align: top;">
                        <div class="signer-name">{{ $ketuaOtaNama }}</div>
                        <div class="signer-title">Pengelola Beasiswa OTA</div>
                    </td>
                    <td class="col-qr" style="vertical-align: top;"></td>
                    <td class="col-signer" style="vertical-align: top;">
                        <div class="signer-name">{{ $kepsekNama }}</div>
                        <div class="signer-title">NIP: {{ $kepsekNip }}</div>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>
