@php
 $canViewSensitive ??= \App\Core\Support\DataMasker::canViewSensitive();
 $school ??= \App\Modules\Sistem\Models\School::current();
 $ayah = $siswa->ayah;
 $ibu = $siswa->ibu;
 $wali = $siswa->wali;
 $nik = $canViewSensitive ? $siswa->nik : \App\Core\Support\DataMasker::maskNik($siswa->nik);
 $nisn = $canViewSensitive ? $siswa->nisn : \App\Core\Support\DataMasker::maskNisn($siswa->nisn);
 $noKk = $canViewSensitive ? $siswa->no_kk : \App\Core\Support\DataMasker::maskKk($siswa->no_kk);
 $nikAyah = $canViewSensitive ? ($ayah?->nik ?? '-') : \App\Core\Support\DataMasker::maskNik($ayah?->nik);
 $nikIbu = $canViewSensitive ? ($ibu?->nik ?? '-') : \App\Core\Support\DataMasker::maskNik($ibu?->nik);
 $nikWali = $canViewSensitive ? ($wali?->nik ?? '-') : \App\Core\Support\DataMasker::maskNik($wali?->nik);

 // Foto base64
 $fotoBase64 = null;
 if ($siswa->foto_path && file_exists(public_path('storage/' . $siswa->foto_path))) {
 $imgData = file_get_contents(public_path('storage/' . $siswa->foto_path));
 $ext = pathinfo($siswa->foto_path, PATHINFO_EXTENSION);
 $fotoBase64 = 'data:image/' . $ext . ';base64,' . base64_encode($imgData);
 }
@endphp
<html xmlns:o='urn:schemas-microsoft-com:office:office'
 xmlns:w='urn:schemas-microsoft-com:office:word'
 xmlns='http://www.w3.org/TR/REC-html40'>
<head>
 <meta charset="utf-8">
 <title>Biodata Siswa - {{ $siswa->nama }}</title>
 <!--[if gte mso 9]>
 <xml>
 <w:WordDocument>
 <w:View>Print</w:View>
 <w:Zoom>100</w:Zoom>
 <w:DoNotOptimizeForBrowser/>
 </w:WordDocument>
 </xml>
 <![endif]-->
 <style>
 @page Section1 {
 size: 595.3pt 841.9pt; /* A4 */
 margin: 2.0cm 2.0cm 2.0cm 2.0cm;
 mso-header-margin: 36.0pt;
 mso-footer-margin: 36.0pt;
 mso-paper-source: 0;
 }
 div.Section1 { page: Section1; }
 body {
 font-family: Arial, Helvetica, sans-serif;
 font-size: 10pt;
 line-height: 1.35;
 color: #111827;
 }
 .header-kop {
 text-align: center;
 border-bottom: 3px double #000000;
 padding-bottom: 8px;
 margin-bottom: 16px;
 }
 .kop-yayasan {
 font-size: 11pt;
 font-weight: bold;
 margin: 0;
 text-transform: uppercase;
 }
 .kop-sekolah {
 font-size: 16pt;
 font-weight: bold;
 margin: 2px 0;
 color: #0f172a;
 text-transform: uppercase;
 }
 .kop-alamat {
 font-size: 8.5pt;
 color: #475569;
 margin: 0;
 }
 .doc-title {
 text-align: center;
 font-size: 13pt;
 font-weight: bold;
 text-decoration: underline;
 text-transform: uppercase;
 margin: 14px 0 2px 0;
 }
 .doc-subtitle {
 text-align: center;
 font-size: 9.5pt;
 color: #475569;
 margin-bottom: 14px;
 }
 .sec-title {
 background-color: #1e40af;
 color: #ffffff;
 font-weight: bold;
 font-size: 9.5pt;
 padding: 4px 8px;
 margin-top: 12px;
 margin-bottom: 4px;
 text-transform: uppercase;
 }
 table.bio-table {
 width: 100%;
 border-collapse: collapse;
 margin-bottom: 10px;
 font-size: 9pt;
 }
 table.bio-table td {
 border: 1px solid #cbd5e1;
 padding: 4px 6px;
 vertical-align: top;
 }
 table.bio-table td.label-col {
 width: 28%;
 background-color: #f8fafc;
 font-weight: bold;
 color: #334155;
 }
 table.bio-table td.colon-col {
 width: 2%;
 text-align: center;
 background-color: #f8fafc;
 }
 table.bio-table td.val-col {
 width: 70%;
 }
 .foto-box {
 width: 90px;
 height: 120px;
 border: 1px dashed #64748b;
 text-align: center;
 background-color: #f8fafc;
 margin: 0 auto;
 }
 .sign-table {
 width: 100%;
 border-collapse: collapse;
 margin-top: 24px;
 text-align: center;
 font-size: 9.5pt;
 }
 .sign-table td {
 vertical-align: top;
 padding: 4px 10px;
 }
 </style>
</head>
<body>
<div class="Section1">

 {{-- KOP SURAT --}}
 <div class="header-kop">
 <div class="kop-yayasan">YAYASAN PRAWITAMA</div>
 <div class="kop-sekolah">{{ $school->nama ?? 'SMK WIKRAMA 1 GARUT' }}</div>
 <div class="kop-alamat">
 {{ $school->alamat ?? 'Jl. Otto Iskandardinata, Kp. Tanjung Kidul, Tarogong Kidul, Garut' }} |
 Telp: {{ $school->telepon ?? '(0262) 232145' }} | Email: {{ $school->email ?? 'smkwikrama1garut@gmail.com' }}
 </div>
 </div>

 {{-- JUDUL DOKUMEN --}}
 <div class="doc-title">LEMBAR BUKU INDUK SISWA</div>
 <div class="doc-subtitle">NIS: {{ $siswa->nipd ?: $siswa->nisn }} &mdash; NISN: {{ $nisn ?: '-' }}</div>

 {{-- KOTAK DATA POKOK & FOTO --}}
 <table style="width: 100%; border-collapse: collapse; margin-bottom: 10px;">
 <tr>
 <td style="width: 78%; vertical-align: top;">
 <table class="bio-table" style="margin-bottom: 0;">
 <tr>
 <td class="label-col">Nama Lengkap Siswa</td>
 <td class="colon-col">:</td>
 <td class="val-col"><b style="font-size: 11pt; color: #1e40af;">{{ $siswa->nama }}</b></td>
 </tr>
 <tr>
 <td class="label-col">Nomor Induk Siswa (NIS)</td>
 <td class="colon-col">:</td>
 <td class="val-col"><b>{{ $siswa->nipd ?: '-' }}</b></td>
 </tr>
 <tr>
 <td class="label-col">NISN</td>
 <td class="colon-col">:</td>
 <td class="val-col"><b>{{ $nisn ?: '-' }}</b></td>
 </tr>
 <tr>
 <td class="label-col">Rombongan Belajar (Rombel)</td>
 <td class="colon-col">:</td>
 <td class="val-col"><b>{{ $siswa->rombel?->nama ?? '-' }}</b> (Tingkat {{ $siswa->rombel?->tingkat ?? '-' }})</td>
 </tr>
 <tr>
 <td class="label-col">Rayon Wilayah / Program</td>
 <td class="colon-col">:</td>
 <td class="val-col">{{ $siswa->rayon?->nama ?? '-' }} / {{ $siswa->program?->nama ?? 'Reguler' }}</td>
 </tr>
 </table>
 </td>
 <td style="width: 22%; text-align: center; vertical-align: middle;">
 <div class="foto-box">
 @if($fotoBase64)
 <img src="{{ $fotoBase64 }}" width="90" height="120" style="object-fit: cover;" alt="Foto">
 @else
 <div style="padding-top: 45px; font-size: 8pt; color: #64748b;">
 Pas Foto<br>3 x 4
 </div>
 @endif
 </div>
 </td>
 </tr>
 </table>

 {{-- I. IDENTITAS PRIBADI --}}
 <div class="sec-title">I. Identitas Pribadi Peserta Didik</div>
 <table class="bio-table">
 <tr>
 <td class="label-col">Jenis Kelamin</td>
 <td class="colon-col">:</td>
 <td class="val-col">{{ $siswa->jk === 'L' ? 'Laki-laki (L)' : 'Perempuan (P)' }}</td>
 </tr>
 <tr>
 <td class="label-col">Tempat, Tanggal Lahir</td>
 <td class="colon-col">:</td>
 <td class="val-col">{{ $siswa->tempat_lahir ?: '-' }}, {{ $siswa->tanggal_lahir ? $siswa->tanggal_lahir->translatedFormat('d F Y') : '-' }}</td>
 </tr>
 <tr>
 <td class="label-col">Nomor Induk Kependudukan (NIK)</td>
 <td class="colon-col">:</td>
 <td class="val-col">{{ $nik ?: '-' }}</td>
 </tr>
 <tr>
 <td class="label-col">Agama</td>
 <td class="colon-col">:</td>
 <td class="val-col">{{ $siswa->agama ?: '-' }}</td>
 </tr>
 <tr>
 <td class="label-col">Nomor HP / WhatsApp</td>
 <td class="colon-col">:</td>
 <td class="val-col">{{ $siswa->hp ?: '-' }}</td>
 </tr>
 <tr>
 <td class="label-col">Alamat Email</td>
 <td class="colon-col">:</td>
 <td class="val-col">{{ $siswa->email ?: '-' }}</td>
 </tr>
 <tr>
 <td class="label-col">Status Siswa</td>
 <td class="colon-col">:</td>
 <td class="val-col"><b style="text-transform: uppercase;">{{ $siswa->status ?: 'aktif' }}</b> ({{ $siswa->is_active ? 'Aktif Terdaftar' : 'Non-Aktif' }})</td>
 </tr>
 </table>

 {{-- II. ALAMAT & TEMPAT TINGGAL --}}
 <div class="sec-title">II. Alamat dan Tempat Tinggal</div>
 <table class="bio-table">
 <tr>
 <td class="label-col">Alamat Jalan / Tempat Tinggal</td>
 <td class="colon-col">:</td>
 <td class="val-col">{{ $siswa->alamat ?: '-' }}</td>
 </tr>
 <tr>
 <td class="label-col">RT / RW / Dusun</td>
 <td class="colon-col">:</td>
 <td class="val-col">RT {{ $siswa->rt ?: '-' }} / RW {{ $siswa->rw ?: '-' }} &mdash; Dusun {{ $siswa->dusun ?: '-' }}</td>
 </tr>
 <tr>
 <td class="label-col">Kelurahan / Desa & Kecamatan</td>
 <td class="colon-col">:</td>
 <td class="val-col">Kel. {{ $siswa->kelurahan ?: '-' }}, Kec. {{ $siswa->kecamatan ?: '-' }}</td>
 </tr>
 <tr>
 <td class="label-col">Kode Pos</td>
 <td class="colon-col">:</td>
 <td class="val-col">{{ $siswa->kode_pos ?: '-' }}</td>
 </tr>
 <tr>
 <td class="label-col">Jenis Tinggal / Alat Transportasi</td>
 <td class="colon-col">:</td>
 <td class="val-col">{{ $siswa->jenis_tinggal ?: '-' }} / {{ $siswa->alat_transportasi ?: '-' }}</td>
 </tr>
 <tr>
 <td class="label-col">Jarak Rumah ke Sekolah</td>
 <td class="colon-col">:</td>
 <td class="val-col">{{ $siswa->jarak_ke_sekolah_km ? $siswa->jarak_ke_sekolah_km . ' km' : '-' }}</td>
 </tr>
 </table>

 {{-- III. DATA ORANG TUA / WALI --}}
 <div class="sec-title">III. Data Orang Tua dan Wali</div>
 <table class="bio-table">
 <tr style="background-color: #f1f5f9; font-weight: bold;">
 <td style="width: 25%;">Keterangan</td>
 <td style="width: 25%;">Ayah Kandung</td>
 <td style="width: 25%;">Ibu Kandung</td>
 <td style="width: 25%;">Wali</td>
 </tr>
 <tr>
 <td class="label-col">Nama Lengkap</td>
 <td>{{ $ayah?->nama ?? '-' }}</td>
 <td>{{ $ibu?->nama ?? '-' }}</td>
 <td>{{ $wali?->nama ?? '-' }}</td>
 </tr>
 <tr>
 <td class="label-col">NIK</td>
 <td>{{ $nikAyah }}</td>
 <td>{{ $nikIbu }}</td>
 <td>{{ $nikWali }}</td>
 </tr>
 <tr>
 <td class="label-col">Tahun Lahir</td>
 <td>{{ $ayah?->tahun_lahir ?? '-' }}</td>
 <td>{{ $ibu?->tahun_lahir ?? '-' }}</td>
 <td>{{ $wali?->tahun_lahir ?? '-' }}</td>
 </tr>
 <tr>
 <td class="label-col">Pendidikan Terakhir</td>
 <td>{{ $ayah?->jenjang_pendidikan ?? '-' }}</td>
 <td>{{ $ibu?->jenjang_pendidikan ?? '-' }}</td>
 <td>{{ $wali?->jenjang_pendidikan ?? '-' }}</td>
 </tr>
 <tr>
 <td class="label-col">Pekerjaan</td>
 <td>{{ $ayah?->pekerjaan ?? '-' }}</td>
 <td>{{ $ibu?->pekerjaan ?? '-' }}</td>
 <td>{{ $wali?->pekerjaan ?? '-' }}</td>
 </tr>
 <tr>
 <td class="label-col">Penghasilan Bulanan</td>
 <td>{{ $ayah?->penghasilan ?? '-' }}</td>
 <td>{{ $ibu?->penghasilan ?? '-' }}</td>
 <td>{{ $wali?->penghasilan ?? '-' }}</td>
 </tr>
 <tr>
 <td class="label-col">Nomor HP / Kontak</td>
 <td>{{ $ayah?->no_hp ?? '-' }}</td>
 <td>{{ $ibu?->no_hp ?? '-' }}</td>
 <td>{{ $wali?->no_hp ?? '-' }}</td>
 </tr>
 </table>

 {{-- IV. BANTUAN & DOKUMEN PENDUKUNG --}}
 <div class="sec-title">IV. Bantuan Sosial, Rekening & Dokumen</div>
 <table class="bio-table">
 <tr>
 <td class="label-col">Nomor Kartu Keluarga (KK)</td>
 <td class="colon-col">:</td>
 <td class="val-col">{{ $noKk ?: '-' }}</td>
 </tr>
 <tr>
 <td class="label-col">No. Registrasi Akta Lahir</td>
 <td class="colon-col">:</td>
 <td class="val-col">{{ $siswa->no_akta_lahir ?: '-' }}</td>
 </tr>
 <tr>
 <td class="label-col">Sekolah Asal (SMP/MTs)</td>
 <td class="colon-col">:</td>
 <td class="val-col">{{ $siswa->sekolah_asal ?: '-' }}</td>
 </tr>
 <tr>
 <td class="label-col">No Peserta UN / No Seri Ijazah</td>
 <td class="colon-col">:</td>
 <td class="val-col">{{ $siswa->no_peserta_un ?: '-' }} / {{ $siswa->no_seri_ijazah ?: '-' }} (SKHUN: {{ $siswa->skhun ?: '-' }})</td>
 </tr>
 <tr>
 <td class="label-col">Penerima Bantuan KPS / KKS</td>
 <td class="colon-col">:</td>
 <td class="val-col">{{ $siswa->penerima_kps ? 'Ya (No. KPS: ' . ($siswa->no_kps ?: '-') . ')' : 'Tidak' }} &mdash; No. KKS: {{ $siswa->nomor_kks ?: '-' }}</td>
 </tr>
 <tr>
 <td class="label-col">Program Indonesia Pintar (PIP / KIP)</td>
 <td class="colon-col">:</td>
 <td class="val-col">
 KIP: {{ $siswa->penerima_kip ? 'Ya (Nomor: ' . ($siswa->nomor_kip ?: '-') . ' a.n ' . ($siswa->nama_di_kip ?: '-') . ')' : 'Tidak' }} <br>
 Layak PIP: {{ $siswa->layak_pip ? 'Ya (Alasan: ' . ($siswa->alasan_layak_pip ?: '-') . ')' : 'Tidak' }}
 </td>
 </tr>
 <tr>
 <td class="label-col">Rekening Bank Siswa</td>
 <td class="colon-col">:</td>
 <td class="val-col">Bank {{ $siswa->bank ?: '-' }} &mdash; No. Rek: <b>{{ $siswa->nomor_rekening_bank ?: '-' }}</b> (a.n {{ $siswa->rekening_atas_nama ?: '-' }})</td>
 </tr>
 </table>

 {{-- V. DATA PERIODIK & FISIK --}}
 <div class="sec-title">V. Data Periodik dan Fisik</div>
 <table class="bio-table">
 <tr>
 <td class="label-col">Tinggi / Berat Badan</td>
 <td class="colon-col">:</td>
 <td class="val-col">{{ $siswa->tinggi_badan ? $siswa->tinggi_badan . ' cm' : '-' }} / {{ $siswa->berat_badan ? $siswa->berat_badan . ' kg' : '-' }} (Lingkar Kepala: {{ $siswa->lingkar_kepala ? $siswa->lingkar_kepala . ' cm' : '-' }})</td>
 </tr>
 <tr>
 <td class="label-col">Status Anak & Saudara</td>
 <td class="colon-col">:</td>
 <td class="val-col">Anak ke-<b>{{ $siswa->anak_ke_berapa ?: '-' }}</b> dari <b>{{ ($siswa->jml_saudara_kandung !== null) ? ($siswa->jml_saudara_kandung + 1) : '-' }}</b> bersaudara</td>
 </tr>
 <tr>
 <td class="label-col">Kebutuhan Khusus</td>
 <td class="colon-col">:</td>
 <td class="val-col">{{ $siswa->kebutuhan_khusus ?: 'Tidak Ada' }}</td>
 </tr>
 </table>

 {{-- TANDA TANGAN --}}
 <div style="text-align: right; margin-top: 20px; font-size: 9.5pt;">
 Garut, {{ now()->translatedFormat('d F Y') }}
 </div>
 <table class="sign-table">
 <tr>
 <td style="width: 33%;">
 <div>Mengetahui,<br>Orang Tua / Wali Siswa</div>
 <div style="height: 60px;"></div>
 <div><b>( {{ $ayah?->nama ?: ($ibu?->nama ?: ($wali?->nama ?: '........................................')) }} )</b></div>
 </td>
 <td style="width: 34%;">
 <div>Pembimbing Rayon / Wali Kelas</div>
 <div style="height: 60px;"></div>
 <div><b><u>{{ $siswa->rayon?->pembimbing?->nama ?? ($siswa->rombel?->waliKelas?->nama ?? '........................................') }}</u></b></div>
 <div style="font-size: 8.5pt; color: #475569;">NIP: {{ $siswa->rayon?->pembimbing?->nip ?? ($siswa->rombel?->waliKelas?->nip ?? '-') }}</div>
 </td>
 <td style="width: 33%;">
 <div>Mengetahui,<br>Kepala Sekolah</div>
 <div style="height: 60px;"></div>
 <div><b><u>{{ $school->kepala_sekolah ?? 'H. Dedik, M.Pd.' }}</u></b></div>
 <div style="font-size: 8.5pt; color: #475569;">NIP: {{ $school->nip_kepsek ?? '-' }}</div>
 </td>
 </tr>
 </table>

</div>
</body>
</html>
