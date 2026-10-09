<!DOCTYPE html>
<html lang="id">
<head>
 <meta charset="UTF-8">
 <title>Identitas Siswa - {{ $siswa->nama }}</title>
 <style>
 body { font-family: 'Times New Roman', Times, serif; padding: 30px; }
 h2 { text-align: center; text-transform: uppercase; font-size: 16px; margin-bottom: 40px; }
 table { width: 100%; border-collapse: collapse; font-size: 14px; margin-left: 20px; }
 td { padding: 8px 4px; vertical-align: top; }
 .col-no { width: 5%; }
 .col-label { width: 35%; }
 .col-sep { width: 2%; text-align: center; }
 .col-value { width: 58%; }
 
 .photo-box { width: 3cm; height: 4cm; border: 1px solid #000; text-align: center; line-height: 4cm; margin-top: 50px; margin-left: 50px; float: left; }
 .kepsek-box { float: right; margin-top: 50px; margin-right: 50px; text-align: center; font-size: 14px; }
 </style>
</head>
<body>

 <h2>KETERANGAN TENTANG DIRI PESERTA DIDIK</h2>

 <table>
 <tr><td class="col-no">1.</td><td class="col-label">Nama Peserta Didik (Lengkap)</td><td class="col-sep">:</td><td class="col-value font-bold">{{ $siswa->nama }}</td></tr>
 <tr><td class="col-no">2.</td><td class="col-label">Nomor Induk Siswa Nasional</td><td class="col-sep">:</td><td class="col-value">{{ $siswa->nisn }}</td></tr>
 <tr><td class="col-no">3.</td><td class="col-label">Nomor Induk Siswa</td><td class="col-sep">:</td><td class="col-value">{{ $siswa->nipd }}</td></tr>
 <tr><td class="col-no">4.</td><td class="col-label">Tempat, Tanggal Lahir</td><td class="col-sep">:</td><td class="col-value">{{ $siswa->tempat_lahir }}, {{ \Carbon\Carbon::parse($siswa->tanggal_lahir)->translatedFormat('d F Y') }}</td></tr>
 <tr><td class="col-no">5.</td><td class="col-label">Jenis Kelamin</td><td class="col-sep">:</td><td class="col-value">{{ $siswa->jk === 'L' ? 'Laki-laki' : 'Perempuan' }}</td></tr>
 <tr><td class="col-no">6.</td><td class="col-label">Agama</td><td class="col-sep">:</td><td class="col-value">{{ $siswa->agama }}</td></tr>
 <tr><td class="col-no">7.</td><td class="col-label">Anak Ke</td><td class="col-sep">:</td><td class="col-value">{{ $siswa->anak_ke }}</td></tr>
 <tr><td class="col-no">8.</td><td class="col-label">Alamat Peserta Didik</td><td class="col-sep">:</td><td class="col-value">{{ $siswa->alamat }}<br>RT {{ $siswa->rt }} / RW {{ $siswa->rw }}, Kel/Desa: {{ $siswa->desa_kelurahan }}, Kec: {{ $siswa->kecamatan }}</td></tr>
 <tr><td class="col-no">9.</td><td class="col-label">Nama Ayah</td><td class="col-sep">:</td><td class="col-value">{{ $siswa->nama_ayah }}</td></tr>
 <tr><td class="col-no">10.</td><td class="col-label">Nama Ibu</td><td class="col-sep">:</td><td class="col-value">{{ $siswa->nama_ibu }}</td></tr>
 <tr><td class="col-no">11.</td><td class="col-label">Pekerjaan Ayah</td><td class="col-sep">:</td><td class="col-value">{{ $siswa->pekerjaan_ayah }}</td></tr>
 <tr><td class="col-no">12.</td><td class="col-label">Pekerjaan Ibu</td><td class="col-sep">:</td><td class="col-value">{{ $siswa->pekerjaan_ibu }}</td></tr>
 </table>

 <div style="clear: both;"></div>

 <div class="photo-box">
 Pas Foto<br>3 x 4
 </div>

 <div class="kepsek-box">
 Garut, ................................<br>
 Kepala Sekolah,<br><br><br><br><br>
 <strong>.........................................</strong><br>
 NIP. 
 </div>

</body>
</html>
