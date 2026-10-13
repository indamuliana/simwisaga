<!DOCTYPE html>
<html lang="id">
<head>
	<meta charset="UTF-8">
	<title>Transkrip Nilai - {{ $data['siswa']['nama'] }}</title>
	<style>
		body {
			font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
			font-size: 8.5pt;
			line-height: 1.3;
			color: #111827;
			margin: 0;
			padding: 0;
		}
		.header-kop {
			text-align: center;
			border-bottom: 2.5px double #111827;
			margin-bottom: 12px;
			padding-bottom: 8px;
		}
		.header-kop h1 {
			margin: 0;
			font-size: 15pt;
			text-transform: uppercase;
			letter-spacing: 0.5px;
		}
		.header-kop h2 {
			margin: 1px 0;
			font-size: 11pt;
			font-weight: 600;
		}
		.header-kop p {
			margin: 1px 0;
			font-size: 8pt;
			color: #374151;
		}
		.title {
			text-align: center;
			font-size: 12pt;
			font-weight: bold;
			margin-bottom: 12px;
			text-transform: uppercase;
			letter-spacing: 1px;
			text-decoration: underline;
		}
		.info-table {
			width: 100%;
			margin-bottom: 12px;
			font-size: 8.5pt;
			border-collapse: collapse;
		}
		.info-table td {
			padding: 1.5px 2px;
			vertical-align: top;
		}
		.data-table {
			width: 100%;
			border-collapse: collapse;
			margin-bottom: 12px;
		}
		.data-table th, .data-table td {
			border: 0.5pt solid #374151;
			padding: 4px 5px;
		}
		.data-table th {
			background-color: #f3f4f6;
			text-align: center;
			font-weight: bold;
			font-size: 8pt;
		}
		.group-header {
			background-color: #e5e7eb;
			font-weight: bold;
			text-transform: uppercase;
			font-size: 8pt;
			padding-left: 6px;
		}
		.summary-box {
			width: 100%;
			border: 0.5pt solid #374151;
			background-color: #f9fafb;
			padding: 8px;
			margin-bottom: 15px;
			font-size: 8.5pt;
		}
		.signature-table {
			width: 100%;
			margin-top: 15px;
			text-align: center;
			page-break-inside: avoid;
			font-size: 8.5pt;
		}
		.signature-table td {
			width: 50%;
			vertical-align: top;
		}
		.signature-space {
			height: 60px;
		}
	</style>
</head>
<body>

	<!-- Kop Surat Sekolah -->
	<div class="header-kop">
		@if(!empty($sekolah->kop_surat_path))
			<img src="{{ storage_path('app/public/' . $sekolah->kop_surat_path) }}" style="width: 100%; max-height: 110px; object-fit: contain;">
		@else
			<h2>{{ $sekolah->nama_yayasan ?? 'YAYASAN PRAWITAMA' }}</h2>
			<h1>{{ $sekolah->nama ?? 'SMK WIKRAMA 1 GARUT' }}</h1>
			<p>{{ $sekolah->alamat ?? 'Jl. Otto Iskandardinata, Kp. Tanjung Kidul, Tarogong Kaler, Garut' }}</p>
			<p>Telp: {{ $sekolah->telepon ?? '(0262) 234567' }} | Website: {{ $sekolah->website ?? 'wikramagarut.sch.id' }} | Email: {{ $sekolah->email ?? 'smkwikrama1garut@gmail.com' }}</p>
		@endif
	</div>

	<div class="title">TRANSKRIP NILAI AKADEMIK PESERTA DIDIK</div>

	<!-- Identitas Siswa -->
	<table class="info-table">
		<tr>
			<td width="20%">Nama Peserta Didik</td>
			<td width="2%">:</td>
			<td width="38%"><strong>{{ $data['siswa']['nama'] }}</strong></td>
			<td width="20%">Program Keahlian</td>
			<td width="2%">:</td>
			<td width="18%">{{ $data['siswa']['jurusan_nama'] }}</td>
		</tr>
		<tr>
			<td>Nomor Induk / NISN</td>
			<td>:</td>
			<td>{{ $data['siswa']['nipd'] }} / {{ $data['siswa']['nisn'] }}</td>
			<td>Konsentrasi Keahlian</td>
			<td>:</td>
			<td>{{ $data['siswa']['jurusan_kode'] }}</td>
		</tr>
		<tr>
			<td>Tempat, Tanggal Lahir</td>
			<td>:</td>
			<td>{{ $data['siswa']['tempat_lahir'] }}, {{ $data['siswa']['tanggal_lahir'] }}</td>
			<td>Rombel Terakhir</td>
			<td>:</td>
			<td>{{ $data['siswa']['rombel'] }}</td>
		</tr>
	</table>

	<!-- Tabel Nilai 6 Semester -->
	<table class="data-table">
		<thead>
			<tr>
				<th width="4%" rowspan="2">NO</th>
				<th width="36%" rowspan="2">MATA PELAJARAN</th>
				<th width="6%" rowspan="2">KKM</th>
				<th colspan="2" width="12%">KELAS X</th>
				<th colspan="2" width="12%">KELAS XI</th>
				<th colspan="2" width="12%">KELAS XII</th>
				<th width="10%" rowspan="2">NILAI AKHIR</th>
				<th width="8%" rowspan="2">HURUF</th>
			</tr>
			<tr>
				<th width="6%">S1</th>
				<th width="6%">S2</th>
				<th width="6%">S3</th>
				<th width="6%">S4</th>
				<th width="6%">S5</th>
				<th width="6%">S6</th>
			</tr>
		</thead>
		<tbody>
			@php $globalNo = 1; @endphp
			@foreach($data['grouped_mapels'] as $group)
				<tr>
					<td colspan="11" class="group-header">
						<strong>KELOMPOK {{ $group['kelompok_kode'] }}. {{ $group['kelompok_nama'] }}</strong>
					</td>
				</tr>
				@foreach($group['items'] as $item)
					<tr>
						<td style="text-align: center;">{{ $globalNo++ }}</td>
						<td>{{ $item['nama'] }}</td>
						<td style="text-align: center;">{{ number_format($item['kkm'], 0) }}</td>
						<td style="text-align: center;">{{ $item['semesters'][1] !== null ? number_format($item['semesters'][1], 0) : '-' }}</td>
						<td style="text-align: center;">{{ $item['semesters'][2] !== null ? number_format($item['semesters'][2], 0) : '-' }}</td>
						<td style="text-align: center;">{{ $item['semesters'][3] !== null ? number_format($item['semesters'][3], 0) : '-' }}</td>
						<td style="text-align: center;">{{ $item['semesters'][4] !== null ? number_format($item['semesters'][4], 0) : '-' }}</td>
						<td style="text-align: center;">{{ $item['semesters'][5] !== null ? number_format($item['semesters'][5], 0) : '-' }}</td>
						<td style="text-align: center;">{{ $item['semesters'][6] !== null ? number_format($item['semesters'][6], 0) : '-' }}</td>
						<td style="text-align: center; font-weight: bold; background-color: #f9fafb;">
							{{ $item['rata_mapel'] !== null ? number_format($item['rata_mapel'], 2) : '-' }}
						</td>
						<td style="text-align: center; font-weight: bold;">
							{{ $item['predikat'] }}
						</td>
					</tr>
				@endforeach
			@endforeach
		</tbody>
		<tfoot>
			<tr style="background-color: #f3f4f6; font-weight: bold;">
				<td colspan="3" style="text-align: center;">RATA-RATA TIAP SEMESTER</td>
				@for($s = 1; $s <= 6; $s++)
					<td style="text-align: center;">
						{{ $data['semester_averages'][$s] !== null ? number_format($data['semester_averages'][$s], 2) : '-' }}
					</td>
				@endfor
				<td colspan="2" style="text-align: center;">-</td>
			</tr>
		</tfoot>
	</table>

	<!-- Kotak Ringkasan Nilai Akhir -->
	<table style="width: 100%; border-collapse: collapse; margin-bottom: 15px;">
		<tr>
			<td width="60%" style="vertical-align: top; padding-right: 15px;">
				<table style="width: 100%; border: 0.5pt solid #374151; font-size: 8pt; border-collapse: collapse;">
					<tr style="background-color: #f3f4f6;">
						<th colspan="3" style="padding: 4px; border: 0.5pt solid #374151; text-align: left;">KETERANGAN PREDIKAT NILAI</th>
					</tr>
					<tr>
						<td width="30%" style="padding: 3px; border: 0.5pt solid #374151; text-align: center;">85.00 - 100.00</td>
						<td width="15%" style="padding: 3px; border: 0.5pt solid #374151; text-align: center;"><strong>A</strong></td>
						<td width="55%" style="padding: 3px; border: 0.5pt solid #374151;">Sangat Baik (Amat Baik)</td>
					</tr>
					<tr>
						<td style="padding: 3px; border: 0.5pt solid #374151; text-align: center;">75.00 - 84.99</td>
						<td style="padding: 3px; border: 0.5pt solid #374151; text-align: center;"><strong>B</strong></td>
						<td style="padding: 3px; border: 0.5pt solid #374151;">Baik</td>
					</tr>
					<tr>
						<td style="padding: 3px; border: 0.5pt solid #374151; text-align: center;">60.00 - 74.99</td>
						<td style="padding: 3px; border: 0.5pt solid #374151; text-align: center;"><strong>C</strong></td>
						<td style="padding: 3px; border: 0.5pt solid #374151;">Cukup</td>
					</tr>
					<tr>
						<td style="padding: 3px; border: 0.5pt solid #374151; text-align: center;">0.00 - 59.99</td>
						<td style="padding: 3px; border: 0.5pt solid #374151; text-align: center;"><strong>D</strong></td>
						<td style="padding: 3px; border: 0.5pt solid #374151;">Kurang</td>
					</tr>
				</table>
			</td>
			<td width="40%" style="vertical-align: top;">
				<table style="width: 100%; border: 1pt solid #111827; background-color: #f9fafb; font-size: 8.5pt; border-collapse: collapse;">
					<tr>
						<td style="padding: 5px; border-bottom: 0.5pt solid #e5e7eb;">Total Mata Pelajaran</td>
						<td style="padding: 5px; border-bottom: 0.5pt solid #e5e7eb; text-align: right; font-weight: bold;">{{ $data['summary']['total_mapel'] }} Mapel</td>
					</tr>
					<tr>
						<td style="padding: 5px; border-bottom: 0.5pt solid #e5e7eb;"><strong>RATA-RATA TRANSKRIP</strong></td>
						<td style="padding: 5px; border-bottom: 0.5pt solid #e5e7eb; text-align: right; font-weight: bold; font-size: 10pt; color: #1e40af;">
							{{ number_format($data['summary']['nilai_kumulatif'], 2) }}
						</td>
					</tr>
					<tr>
						<td style="padding: 5px; border-bottom: 0.5pt solid #e5e7eb;">Predikat Kelulusan</td>
						<td style="padding: 5px; border-bottom: 0.5pt solid #e5e7eb; text-align: right; font-weight: bold;">
							{{ $data['summary']['predikat'] }} ({{ $data['summary']['keterangan_predikat'] }})
						</td>
					</tr>
					<tr style="background-color: #f3f4f6;">
						<td style="padding: 5px;">Status Akhir</td>
						<td style="padding: 5px; text-align: right; font-weight: bold; color: {{ $data['summary']['is_lulus'] ? '#15803d' : '#b91c1c' }};">
							{{ $data['summary']['status_kelulusan'] }}
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>

	<!-- Tanda Tangan -->
	<table class="signature-table">
		<tr>
			<td>
				Mengetahui,<br>
				Wali Kelas / Ketua Program Keahlian
				<div class="signature-space"></div>
				<strong>{{ $data['siswa']['wali_kelas'] }}</strong>
			</td>
			<td>
				Garut, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}<br>
				Kepala SMK Wikrama 1 Garut
				<div class="signature-space"></div>
				<strong>{{ $sekolah->kepala_sekolah ?? 'Kepala Sekolah' }}</strong><br>
				<span style="font-size: 8pt;">NIP/NRK. {{ $sekolah->nip_kepala_sekolah ?? '-' }}</span>
			</td>
		</tr>
	</table>

</body>
</html>
