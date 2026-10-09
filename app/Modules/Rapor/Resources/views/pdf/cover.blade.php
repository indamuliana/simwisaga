<!DOCTYPE html>
<html lang="id">
<head>
 <meta charset="UTF-8">
 <title>Cover Rapor - {{ $siswa->nama }}</title>
 <style>
 body { font-family: 'Times New Roman', Times, serif; text-align: center; margin-top: 50px; }
 .logo { width: 150px; margin: 50px auto; }
 h1 { font-size: 24px; font-weight: bold; margin-bottom: 5px; }
 h2 { font-size: 20px; font-weight: normal; margin-bottom: 80px; }
 .student-box { border: 2px solid #000; width: 60%; margin: 0 auto; padding: 30px; margin-bottom: 80px; font-size: 18px; }
 .school-info { font-size: 16px; font-weight: bold; line-height: 1.5; }
 </style>
</head>
<body>

 <h1>RAPOR PESERTA DIDIK</h1>
 <h2>SEKOLAH MENENGAH KEJURUAN (SMK)</h2>

 @if($sekolah->logo_sekolah_path)
 <img src="{{ storage_path('app/public/' . $sekolah->logo_sekolah_path) }}" class="logo" alt="Logo">
 @else
 <!-- Fallback if no logo -->
 <div style="height: 150px; width: 150px; border: 1px solid #ccc; margin: 50px auto; line-height: 150px;">LOGO</div>
 @endif

 <div class="student-box">
 <p style="margin-bottom: 5px;">Nama Peserta Didik:</p>
 <p style="font-size: 22px; font-weight: bold; margin-top: 0; text-transform: uppercase;">{{ $siswa->nama }}</p>
 <br>
 <p style="margin-bottom: 5px;">NISN / NIS:</p>
 <p style="font-size: 18px; font-weight: bold; margin-top: 0;">{{ $siswa->nisn }} / {{ $siswa->nipd }}</p>
 </div>

 <div class="school-info">
 <p>KEMENTERIAN PENDIDIKAN, KEBUDAYAAN,<br>RISET, DAN TEKNOLOGI<br>REPUBLIK INDONESIA</p>
 </div>

</body>
</html>
