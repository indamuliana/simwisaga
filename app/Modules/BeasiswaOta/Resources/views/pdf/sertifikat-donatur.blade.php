<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Sertifikat Donatur OTA - {{ $donasi->donatur->nama }}</title>
    <style>
        @page {
            margin: 0;
            size: A4 landscape;
        }
        body {
            font-family: 'Georgia', 'Times New Roman', serif;
            margin: 0;
            padding: 0;
            color: #1e293b;
            background-color: #ffffff;
        }

        @if($bgBase64)
        .bg-template {
            position: fixed;
            top: 0;
            left: 0;
            width: 297mm;
            height: 210mm;
            z-index: -1;
        }
        .content-container {
            padding: 3.5cm 2.5cm 2cm 2.5cm;
            text-align: center;
        }
        @else
        .certificate-wrapper {
            margin: 1.2cm;
            padding: 1.5cm 2cm;
            border: 6px double #1e3a8a;
            outline: 2px solid #ca8a04;
            outline-offset: -12px;
            background: #fafaf9;
            min-height: 175mm;
            text-align: center;
            box-sizing: border-box;
        }
        .content-container {
            text-align: center;
        }
        @endif

        .cert-header-yayasan {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 10pt;
            letter-spacing: 2px;
            color: #475569;
            text-transform: uppercase;
            margin-bottom: 2px;
        }
        .cert-header-sekolah {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 16pt;
            font-weight: 800;
            letter-spacing: 2px;
            color: #1e3a8a;
            text-transform: uppercase;
            margin-top: 0;
            margin-bottom: 12px;
        }
        .cert-title {
            font-size: 26pt;
            font-weight: bold;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 3px;
            margin-top: 10px;
            margin-bottom: 4px;
        }
        .cert-nomor {
            font-family: 'Courier New', Courier, monospace;
            font-size: 10pt;
            color: #64748b;
            margin-bottom: 20px;
        }
        .cert-subtitle {
            font-size: 12pt;
            font-style: italic;
            color: #475569;
            margin-bottom: 12px;
        }
        .recipient-name {
            font-size: 24pt;
            font-weight: bold;
            color: #1e3a8a;
            text-decoration: underline;
            margin: 8px 0 16px 0;
            letter-spacing: 1px;
        }
        .cert-body {
            font-size: 11pt;
            line-height: 1.7;
            color: #334155;
            max-width: 80%;
            margin: 0 auto 24px auto;
        }
        .cert-highlight {
            font-weight: bold;
            color: #0f172a;
        }
        .signatures-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 25px;
            text-align: center;
        }
        .signatures-table td {
            vertical-align: top;
            width: 50%;
            font-size: 10.5pt;
        }
        .sign-space {
            height: 65px;
        }
        .signer-name {
            font-weight: bold;
            text-decoration: underline;
            font-size: 11pt;
        }
        .signer-title {
            font-size: 9.5pt;
            color: #475569;
        }
    </style>
</head>
<body>
    @if($bgBase64)
        <img src="{{ $bgBase64 }}" class="bg-template" alt="Background Template">
        <div class="content-container">
            <div class="cert-nomor">Nomor: {{ $nomorSertifikat }}</div>
            <div class="cert-subtitle">Diberikan Sebagai Apresiasi Kepada:</div>
            <div class="recipient-name">{{ $donasi->donatur->nama }}</div>
            <div class="cert-body">
                Atas keikhlasan, kepedulian, dan dedikasi yang tulus sebagai <strong>Orang Tua Asuh (OTA)</strong> 
                melalui <span class="cert-highlight">{{ $donasi->kelompok ? $donasi->kelompok->nama_kelompok : 'Program Donasi Insidental' }}</span> 
                @if($donasi->keterangan_periode)
                    periode <span class="cert-highlight">{{ $donasi->keterangan_periode }}</span>
                @endif
                sebesar <span class="cert-highlight">Rp {{ number_format($donasi->nominal, 0, ',', '.') }},-</span> 
                ({{ $terbilang }}) dalam mendukung keberlangsungan pendidikan siswa di SMK Wikrama 1 Garut.
            </div>

            <table class="signatures-table">
                <tr>
                    <td>
                        <div class="signer-title">Koordinator Program OTA,</div>
                        <div class="sign-space"></div>
                        <div class="signer-name">{{ $ketuaOtaNama }}</div>
                    </td>
                    <td>
                        <div class="signer-title">{{ $titimangsa }}<br>Kepala Sekolah,</div>
                        <div class="sign-space"></div>
                        <div class="signer-name">{{ $kepsekNama }}</div>
                        <div class="signer-title">NIP: {{ $kepsekNip }}</div>
                    </td>
                </tr>
            </table>
        </div>
    @else
        <div class="certificate-wrapper">
            <div class="content-container">
                <div class="cert-header-yayasan">YAYASAN PRAWITAMA</div>
                <div class="cert-header-sekolah">{{ $school->nama ?? 'SMK WIKRAMA 1 GARUT' }}</div>
                
                <div class="cert-title">PIAGAM PENGHARGAAN</div>
                <div class="cert-nomor">Nomor: {{ $nomorSertifikat }}</div>

                <div class="cert-subtitle">Diberikan Dengan Rasa Hormat Dan Terima Kasih Kepada:</div>
                <div class="recipient-name">{{ $donasi->donatur->nama }}</div>

                <div class="cert-body">
                    Atas keikhlasan dan kontribusi mulia sebagai <strong>Orang Tua Asuh (OTA)</strong> 
                    pada <span class="cert-highlight">{{ $donasi->kelompok ? $donasi->kelompok->nama_kelompok : 'Program Peduli Pendidikan Insidental' }}</span> 
                    @if($donasi->keterangan_periode)
                        periode <span class="cert-highlight">{{ $donasi->keterangan_periode }}</span>
                    @endif
                    dengan total donasi sebesar <span class="cert-highlight">Rp {{ number_format($donasi->nominal, 0, ',', '.') }},-</span> 
                    (<em>{{ $terbilang }}</em>) untuk membantu pembiayaan pendidikan generasi penerus bangsa.
                    <br>
                    <em>Semoga Allah SWT / Tuhan Yang Maha Esa membalas segala amal kebaikan dengan keberkahan yang berlipat ganda.</em>
                </div>

                <table class="signatures-table">
                    <tr>
                        <td>
                            <div class="signer-title">Koordinator Program OTA,</div>
                            <div class="sign-space"></div>
                            <div class="signer-name">{{ $ketuaOtaNama }}</div>
                        </td>
                        <td>
                            <div class="signer-title">{{ $titimangsa }}<br>Kepala Sekolah,</div>
                            <div class="sign-space"></div>
                            <div class="signer-name">{{ $kepsekNama }}</div>
                            <div class="signer-title">NIP: {{ $kepsekNip }}</div>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    @endif
</body>
</html>
