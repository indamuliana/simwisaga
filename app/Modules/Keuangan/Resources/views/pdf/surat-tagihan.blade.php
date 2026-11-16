<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Pemberitahuan Tagihan - {{ $siswa->nama }}</title>
    <style>
        @page {
            margin: 1.5cm 1.5cm 1.5cm 1.5cm;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 9.5pt;
            color: #1e293b;
            line-height: 1.4;
        }
        .header-kop {
            width: 100%;
            border-bottom: 3px double #0f172a;
            padding-bottom: 8px;
            margin-bottom: 16px;
            text-align: center;
        }
        .kop-sekolah {
            font-size: 15pt;
            font-weight: 800;
            text-transform: uppercase;
            color: #0f172a;
        }
        .kop-detail {
            font-size: 8.5pt;
            color: #475569;
        }
        .surat-title {
            text-align: center;
            margin-bottom: 16px;
        }
        .surat-title h3 {
            margin: 0;
            font-size: 11pt;
            text-transform: uppercase;
            text-decoration: underline;
        }
        .surat-title div {
            font-size: 8.5pt;
            color: #64748b;
        }
        .meta-table {
            width: 100%;
            margin-bottom: 14px;
        }
        .meta-table td {
            padding: 2px 0;
            font-size: 9pt;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            margin-bottom: 14px;
        }
        .data-table th, .data-table td {
            border: 1px solid #cbd5e1;
            padding: 5px 8px;
            font-size: 8.5pt;
        }
        .data-table th {
            background-color: #f1f5f9;
            font-weight: 700;
            text-align: left;
        }
        .bank-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            padding: 8px 12px;
            margin: 12px 0;
            font-size: 8.5pt;
        }
        .ttd-table {
            width: 100%;
            margin-top: 24px;
        }
        .ttd-box {
            text-align: center;
            width: 200px;
            font-size: 9pt;
        }
    </style>
</head>
<body>
    <div class="header-kop">
        <div class="kop-sekolah">{{ $school->nama_sekolah ?? 'SMK WIKRAMA 1 GARUT' }}</div>
        <div class="kop-detail">{{ $school->alamat ?? 'Jl. Otto Iskandardinata, Tarogong Kidul, Garut' }} | Telp: {{ $school->telepon ?? '-' }} | Website: {{ $school->website ?? 'wikrama1garut.sch.id' }}</div>
    </div>

    <div class="surat-title">
        <h3>SURAT PEMBERITAHUAN TUNGGAKAN BIAYA PENDIDIKAN</h3>
        <div>Nomor: TAG/{{ date('Ym') }}/{{ str_pad($siswa->id, 4, '0', STR_PAD_LEFT) }}</div>
    </div>

    <table class="meta-table">
        <tr>
            <td style="width: 18%;">Kepada Yth.</td>
            <td style="width: 2%;">:</td>
            <td style="width: 80%; font-weight: bold;">Bapak / Ibu Orang Tua / Wali dari {{ $siswa->nama }}</td>
        </tr>
        <tr>
            <td>Kelas / Rayon</td>
            <td>:</td>
            <td>{{ $siswa->rombel?->nama ?? '-' }} / {{ $siswa->rayon?->nama ?? '-' }}</td>
        </tr>
        <tr>
            <td>NISN / NIPD</td>
            <td>:</td>
            <td>{{ $siswa->nisn ?? '-' }} / {{ $siswa->nipd ?? '-' }}</td>
        </tr>
    </table>

    <p style="font-size: 9pt; margin-bottom: 8px;">
        Assalamu'alaikum Warahmatullahi Wabarakatuh,<br>
        Sehubungan dengan kelancaran kegiatan belajar mengajar dan pemeliharaan fasilitas pendidikan di {{ $school->nama_sekolah ?? 'SMK Wikrama 1 Garut' }}, bersama ini kami informasikan rincian kewajiban administrasi keuangan yang masih belum terselesaikan sampai dengan bulan ini:
    </p>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%; text-align: center;">No</th>
                <th style="width: 50%;">Pos Pembayaran</th>
                <th style="width: 22%; text-align: right;">Total Tagihan</th>
                <th style="width: 23%; text-align: right;">Sisa Tunggakan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($tagihans as $idx => $tgh)
                <tr>
                    <td style="text-align: center;">{{ $idx + 1 }}</td>
                    <td>{{ $tgh->label_tagihan }}</td>
                    <td style="text-align: right;">Rp {{ number_format($tgh->nominal_tagihan, 0, ',', '.') }}</td>
                    <td style="text-align: right; font-weight: bold; color: #b91c1c;">Rp {{ number_format($tgh->sisa_tagihan, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" style="text-align: center; color: #15803d; font-weight: bold; padding: 12px;">Tidak ada catatan tunggakan (Seluruh biaya telah lunas).</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr style="background-color: #f1f5f9; font-weight: bold;">
                <td colspan="3" style="text-align: right; font-size: 9pt;">TOTAL TUNGGAKAN KEWAJIBAN:</td>
                <td style="text-align: right; font-size: 9.5pt; color: #b91c1c;">Rp {{ number_format($totalTunggakan, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    <div style="font-size: 8.5pt; font-style: italic; margin-bottom: 10px;">
        <strong>Terbilang:</strong> {{ $terbilang }}
    </div>

    <div class="bank-box">
        <strong>Tata Cara Pembayaran:</strong><br>
        1. Pembayaran langsung secara tunai dapat dilakukan di Loket Bendahara Sekolah pada jam kerja.<br>
        2. Pembayaran melalui transfer bank dapat ditujukan ke rekening resmi sekolah:<br>
        @foreach($rekenings as $rek)
            &bull; <strong>{{ $rek->nama_bank }}</strong>: {{ $rek->nomor_rekening }} a.n {{ $rek->atas_nama }}<br>
        @endforeach
        3. Setelah melakukan transfer, mohon unggah bukti pembayaran melalui Portal Orang Tua atau konfirmasi ke Bendahara Sekolah.
    </div>

    <p style="font-size: 9pt;">
        Demikian pemberitahuan ini kami sampaikan. Atas perhatian, kerjasamanya, dan komitmen Bapak/Ibu demi kelancaran pendidikan putra-putri kita, kami haturkan terima kasih.<br>
        Wassalamu'alaikum Warahmatullahi Wabarakatuh.
    </p>

    <table class="ttd-table">
        <tr>
            <td style="width: 50%;"></td>
            <td class="ttd-box">
                Garut, {{ now()->translatedFormat('d F Y') }}<br>
                Bendahara Sekolah,<br><br><br><br><br>
                <strong>( Bendahara Sekolah )</strong><br>
                <span style="font-size: 8pt; color: #64748b;">NIP. -</span>
            </td>
        </tr>
    </table>
</body>
</html>

