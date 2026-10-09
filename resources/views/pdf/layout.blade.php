<!DOCTYPE html>
<html lang="id">
<head>
 <meta charset="UTF-8">
 <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
 <title>{{ $title ?? 'Dokumen SIRAGA' }}</title>
 <style>
 @page {
 margin: 1.5cm 1.5cm 2cm 1.5cm;
 @bottom-right {
 content: "Halaman " counter(page) " dari " counter(pages);
 font-size: 9pt;
 color: #64748b;
 }
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
 margin-bottom: 20px;
 border-bottom: 3px double #0f172a;
 padding-bottom: 10px;
 text-align: center;
 }
 .kop-image {
 width: 100%;
 max-height: 120px;
 object-fit: contain;
 }
 .kop-text-yayasan {
 font-size: 11pt;
 font-weight: 600;
 text-transform: uppercase;
 letter-spacing: 1px;
 margin: 0;
 color: #334155;
 }
 .kop-text-sekolah {
 font-size: 16pt;
 font-weight: 800;
 text-transform: uppercase;
 letter-spacing: 1.5px;
 margin: 2px 0 4px 0;
 color: #0f172a;
 }
 .kop-text-kontak {
 font-size: 8.5pt;
 color: #475569;
 margin: 0;
 }
 .document-title-block {
 text-align: center;
 margin: 15px 0 20px 0;
 }
 .document-title {
 font-size: 13pt;
 font-weight: bold;
 text-transform: uppercase;
 letter-spacing: 0.5px;
 margin: 0;
 text-decoration: underline;
 }
 .document-subtitle {
 font-size: 9.5pt;
 color: #475569;
 margin-top: 4px;
 }
 .document-nomor {
 font-size: 9pt;
 font-family: 'Courier New', Courier, monospace;
 color: #334155;
 margin-top: 2px;
 }
 .content-table {
 width: 100%;
 border-collapse: collapse;
 margin: 15px 0;
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
 .signatures-container {
 margin-top: 30px;
 width: 100%;
 page-break-inside: avoid;
 }
 .titimangsa-block {
 text-align: right;
 font-size: 9.5pt;
 margin-bottom: 10px;
 }
 .signatures-table {
 width: 100%;
 border-collapse: collapse;
 text-align: center;
 }
 .signatures-table td {
 vertical-align: top;
 padding: 0 10px;
 font-size: 9.5pt;
 }
 .sign-space {
 height: 70px;
 }
 .signer-name {
 font-weight: bold;
 text-decoration: underline;
 margin: 0;
 }
 .signer-nip {
 font-size: 8.5pt;
 color: #475569;
 margin-top: 2px;
 }
 .footer-note {
 position: fixed;
 bottom: -30px;
 left: 0;
 right: 0;
 font-size: 8pt;
 color: #94a3b8;
 border-top: 1px solid #e2e8f0;
 padding-top: 4px;
 display: flex;
 justify-content: space-between;
 }
 </style>
</head>
<body>
 {{-- Header Kop Surat --}}
 @if($tampilkanKop ?? true)
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
 @endif

 {{-- Judul Dokumen --}}
 <div class="document-title-block">
 <h2 class="document-title">{{ $title }}</h2>
 @if(!empty($subtitle))
 <div class="document-subtitle">{{ $subtitle }}</div>
 @endif
 @if(!empty($nomorSurat))
 <div class="document-nomor">Nomor: {{ $nomorSurat }}</div>
 @endif
 </div>

 {{-- Isi Dokumen Utama --}}
 <div class="document-body">
 {!! $content !!}
 </div>

 {{-- Blok Tanda Tangan Dinamis --}}
 @if(!empty($signers) && count($signers) > 0)
 <div class="signatures-container">
 <div class="titimangsa-block">
 {{ $titimangsa ?? 'Garut, ' . now()->translatedFormat('d F Y') }}
 </div>
 <table class="signatures-table">
 <tr>
 @foreach($signers as $s)
 <td style="width: {{ 100 / count($signers) }}%;">
 <div>{{ $s['judul_jabatan'] ?? 'Pejabat' }}</div>
 <div class="sign-space"></div>
 <div class="signer-name">{{ $s['nama_pejabat'] ?? '....................................' }}</div>
 <div class="signer-nip">
 @if(!empty($s['nip_pejabat']))
 NIP/NUPTK: {{ $s['nip_pejabat'] }}
 @else
 &nbsp;
 @endif
 </div>
 </td>
 @endforeach
 </tr>
 </table>
 </div>
 @endif

 {{-- Catatan Kaki --}}
 <div class="footer-note">
 <span>Dicetak dari Sistem Informasi Pengelolaan Sekolah (SIRAGA) SMK Wikrama 1 Garut</span>
 <span style="float: right;">Tanggal Cetak: {{ now()->translatedFormat('d/m/Y H:i') }}</span>
 </div>
</body>
</html>
