<!DOCTYPE html>
<html>
<head>
 <meta charset="utf-8">
 <title>Rekap Absensi</title>
 <style>
 body { font-family: sans-serif; font-size: 12px; }
 .text-center { text-align: center; }
 .font-bold { font-weight: bold; }
 .mb-4 { margin-bottom: 1rem; }
 .w-full { width: 100%; }
 table { width: 100%; border-collapse: collapse; margin-top: 20px; }
 th, td { border: 1px solid #000; padding: 6px; text-align: center; }
 th { background-color: #f3f4f6; }
 .text-left { text-align: left; }
 </style>
</head>
<body>
 <div class="text-center mb-4">
 <h2 class="font-bold">REKAPITULASI ABSENSI SISWA</h2>
 <p>Bulan: {{ $bulan }} | Rombongan Belajar: {{ $rombel->nama }}</p>
 </div>

 <table>
 <thead>
 <tr>
 <th width="5%">No</th>
 <th width="20%">NIS</th>
 <th width="45%">Nama Siswa</th>
 <th width="7%">Hadir</th>
 <th width="7%">Sakit</th>
 <th width="7%">Izin</th>
 <th width="9%">Alpa</th>
 </tr>
 </thead>
 <tbody>
 @foreach($rekap as $index => $row)
 <tr>
 <td>{{ $index + 1 }}</td>
 <td>{{ $row['nis'] }}</td>
 <td class="text-left" style="padding-left: 10px;">{{ $row['nama'] }}</td>
 <td>{{ $row['hadir'] }}</td>
 <td>{{ $row['sakit'] }}</td>
 <td>{{ $row['izin'] }}</td>
 <td>{{ $row['alpa'] }}</td>
 </tr>
 @endforeach
 @if(empty($rekap))
 <tr>
 <td colspan="7">Belum ada data absensi.</td>
 </tr>
 @endif
 </tbody>
 </table>
</body>
</html>
