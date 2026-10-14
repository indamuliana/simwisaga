<!DOCTYPE html>
<html lang="id">
<head>
	<meta charset="UTF-8">
	<title>Capaian Hasil Belajar - {{ $siswa->nama }}</title>
	<style>
		body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 10pt; line-height: 1.4; color: #000; margin: 0; padding: 0; }
		.header-kop { text-align: center; border-bottom: 3px double #000; margin-bottom: 20px; padding-bottom: 10px; }
		.header-kop h1 { margin: 0; font-size: 18pt; text-transform: uppercase; }
		.header-kop h2 { margin: 2px 0; font-size: 14pt; }
		.header-kop p { margin: 0; font-size: 10pt; }
		
		.info-table { width: 100%; margin-bottom: 20px; font-weight: bold; }
		.info-table td { padding: 2px; }
		
		.title { text-align: center; font-size: 14pt; font-weight: bold; margin-bottom: 20px; text-transform: uppercase; }
		
		.data-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
		.data-table th, .data-table td { border: 1px solid #000; padding: 6px; }
		.data-table th { background-color: #f0f0f0; text-align: center; }
		
		.catatan-box { border: 1px solid #000; padding: 10px; min-height: 50px; margin-bottom: 20px; }
		
		.signature-table { width: 100%; margin-top: 30px; text-align: center; page-break-inside: avoid; }
		.signature-table td { width: 33.33%; vertical-align: bottom; height: 110px; }
	</style>
</head>
<body>

	<!-- Kop Surat -->
	<div class="header-kop">
		@if($sekolah->kop_surat_path)
			<img src="{{ storage_path('app/public/' . $sekolah->kop_surat_path) }}" style="width: 100%; max-height: 150px; object-fit: contain;">
		@else
			<!-- Fallback text kop -->
			@if($sekolah->logo_sekolah_path)
				<img src="{{ storage_path('app/public/' . $sekolah->logo_sekolah_path) }}" style="height:80px; position:absolute; left:20px; top:0;">
			@endif
			<h2>{{ $sekolah->nama_yayasan ?? 'YAYASAN' }}</h2>
			<h1>{{ $sekolah->nama }}</h1>
			<p>{{ $sekolah->alamat }} Telp. {{ $sekolah->telepon }}</p>
			<p>Website: {{ $sekolah->website }} | Email: {{ $sekolah->email }}</p>
		@endif
	</div>

	<div class="title">CAPAIAN HASIL BELAJAR</div>

	<table class="info-table">
		<tr>
			<td width="15%">Nama Peserta Didik</td><td width="2%">:</td><td width="40%">{{ $siswa->nama }}</td>
			<td width="15%">Kelas</td><td width="2%">:</td><td width="26%">{{ $siswa->rombel->nama }}</td>
		</tr>
		<tr>
			<td>NISN / NIS</td><td>:</td><td>{{ $siswa->nisn }} / {{ $siswa->nipd }}</td>
			<td>Fase / Semester</td><td>:</td>
			<td>
				@php
					$tingkat = $siswa->rombel?->tingkat ?? 'X';
					$fase = in_array($tingkat, ['XI', 'XII']) ? 'F' : 'E';
					$semNama = ucfirst($semester->semester ?? '');
					$periodeLabel = $periode ? " / {$periode->sebutan}" : '';
				@endphp
				{{ $fase }} / {{ $semNama }}{{ $periodeLabel }}
			</td>
		</tr>
		<tr>
			<td>Kompetensi Keahlian</td><td>:</td><td>{{ $siswa->rombel->jurusan->nama ?? '-' }}</td>
			<td>Tahun Pelajaran</td><td>:</td><td>{{ $semester->tahun_ajaran->nama ?? '-' }}</td>
		</tr>
	</table>

	<!-- Nilai Akademik -->
	<h3 style="margin-bottom: 5px;">A. Nilai Akademik</h3>
	<table class="data-table">
		<thead>
			<tr>
				<th width="5%">No</th>
				<th width="25%">Mata Pelajaran</th>
				<th width="10%">Nilai Akhir</th>
				<th width="60%">Capaian Kompetensi</th>
			</tr>
		</thead>
		<tbody>
			@if(isset($groupedNilaiAkademiks) && $groupedNilaiAkademiks->isNotEmpty())
				@foreach($groupedNilaiAkademiks as $namaKelompok => $items)
				<tr style="background-color: #f2f2f2;">
					<td colspan="4" style="font-weight: bold; padding: 4px 6px; font-size: 9.5pt;">
						{{ $namaKelompok }}
					</td>
				</tr>
				@foreach($items as $index => $na)
				<tr>
					<td style="text-align: center;">{{ $index + 1 }}</td>
					<td>{{ $na->penugasanMengajar->mapel->nama }}</td>
					<td style="text-align: center; font-weight: bold;">{{ $na->nilai_akhir !== null ? number_format($na->nilai_akhir, 0) : '-' }}</td>
					<td style="font-size: 8.5pt; line-height: 1.4; color: #1f2937;">
						@if($na->deskripsi_manual)
							{!! nl2br(e($na->deskripsi_manual)) !!}
						@else
							@if(!empty($na->deskripsi_tertinggi))
								<div>{{ $na->deskripsi_tertinggi }}</div>
							@endif
							@if(!empty($na->deskripsi_terendah))
								<div style="margin-top: 4px;">{{ $na->deskripsi_terendah }}</div>
							@endif
							@if(empty($na->deskripsi_tertinggi) && empty($na->deskripsi_terendah))
								<span style="color: #9ca3af; font-style: italic;">Belum ada deskripsi</span>
							@endif
						@endif
					</td>
				</tr>
				@endforeach
				@endforeach
			@elseif($nilaiAkademiks->isNotEmpty())
				@foreach($nilaiAkademiks as $index => $na)
				<tr>
					<td style="text-align: center;">{{ $index + 1 }}</td>
					<td>{{ $na->penugasanMengajar->mapel->nama }}</td>
					<td style="text-align: center; font-weight: bold;">{{ $na->nilai_akhir !== null ? number_format($na->nilai_akhir, 0) : '-' }}</td>
					<td style="font-size: 8.5pt; line-height: 1.4; color: #1f2937;">
						@if($na->deskripsi_manual)
							{!! nl2br(e($na->deskripsi_manual)) !!}
						@else
							@if(!empty($na->deskripsi_tertinggi))
								<div>{{ $na->deskripsi_tertinggi }}</div>
							@endif
							@if(!empty($na->deskripsi_terendah))
								<div style="margin-top: 4px;">{{ $na->deskripsi_terendah }}</div>
							@endif
							@if(empty($na->deskripsi_tertinggi) && empty($na->deskripsi_terendah))
								<span style="color: #9ca3af; font-style: italic;">Belum ada deskripsi</span>
							@endif
						@endif
					</td>
				</tr>
				@endforeach
			@else
				<tr><td colspan="4" style="text-align: center;">Belum ada nilai yang diinput.</td></tr>
			@endif
		</tbody>
	</table>

	<!-- Unit Pengembangan Diri (UPD) / Ekstrakurikuler -->
	<h3 style="margin-bottom: 5px;">B. Unit Pengembangan Diri (UPD)</h3>
	<table class="data-table">
		<thead>
			<tr>
				<th width="5%">No</th>
				<th width="30%">Kegiatan Unit Pengembangan Diri (UPD)</th>
				<th width="15%">Predikat</th>
				<th width="50%">Keterangan</th>
			</tr>
		</thead>
		<tbody>
			@foreach($ekskuls as $index => $ekskul)
			<tr>
				<td style="text-align: center;">{{ $index + 1 }}</td>
				<td>{{ $ekskul->ekstrakurikuler->nama }}</td>
				<td style="text-align: center; font-weight: bold;">{{ $ekskul->nilai_huruf ?? '-' }}</td>
				<td style="font-size: 9pt;">{{ $ekskul->deskripsi }}</td>
			</tr>
			@endforeach
			@if($ekskuls->isEmpty())
				<tr><td colspan="4" style="text-align: center;">Tidak mengikuti kegiatan Unit Pengembangan Diri (UPD).</td></tr>
			@endif
		</tbody>
	</table>

	<!-- Ketidakhadiran -->
	<h3 style="margin-bottom: 5px;">C. Ketidakhadiran</h3>
	<table class="data-table" style="width: 50%;">
		<tr>
			<td width="60%">Sakit</td>
			<td width="40%" style="text-align: center;">{{ $rekapAbsen['sakit'] }} hari</td>
		</tr>
		<tr>
			<td>Izin</td>
			<td style="text-align: center;">{{ $rekapAbsen['izin'] }} hari</td>
		</tr>
		<tr>
			<td>Tanpa Keterangan (Alpa)</td>
			<td style="text-align: center;">{{ $rekapAbsen['alpa'] }} hari</td>
		</tr>
	</table>

	<!-- Catatan Wali Kelas -->
	<h3 style="margin-bottom: 5px;">D. Catatan Wali Kelas</h3>
	<div class="catatan-box">
		{{ $catatan ?: '.........................................................................................................' }}
	</div>

	<!-- Keputusan Akhir Tahun (Khusus Semester Genap) -->
	@if(isset($semester) && strtolower($semester->semester) === 'genap')
	<h3 style="margin-bottom: 5px;">E. Keputusan Akhir Tahun Ajaran</h3>
	<div class="catatan-box" style="margin-bottom: 15px;">
		@php
			$tingkat = $siswa->rombel?->tingkat ?? 'X';
			$riwayat = $siswa->riwayatAkademiks?->first();
			$statusKeputusan = $riwayat?->status_keputusan;
		@endphp

		@if($tingkat === 'XII' || $statusKeputusan === 'lulus' || $siswa->status === 'lulus')
			<p style="margin: 0; font-size: 10pt;">
				Berdasarkan hasil pencapaian kompetensi dan kriteria kelulusan satuan pendidikan, peserta didik dinyatakan:
			</p>
			<p style="margin: 8px 0 0 0; font-size: 11pt; font-weight: bold; text-align: center; border: 1px solid #000; padding: 6px; background-color: #f8fafc;">
				{{ ($statusKeputusan === 'tidak_lulus') ? 'TIDAK LULUS' : 'LULUS' }} DARI SATUAN PENDIDIKAN
			</p>
		@else
			@php
				$nextTingkat = ($tingkat === 'X') ? 'XI' : 'XII';
				$isTinggal = ($statusKeputusan === 'tinggal_kelas');
			@endphp
			<p style="margin: 0; font-size: 10pt;">
				Berdasarkan kriteria kenaikan kelas tahun ajaran {{ $semester->tahunAjaran?->nama ?? '' }}, peserta didik dinyatakan:
			</p>
			<p style="margin: 8px 0 0 0; font-size: 11pt; font-weight: bold; text-align: center; border: 1px solid #000; padding: 6px; background-color: #f8fafc;">
				@if($isTinggal)
					TINGGAL DI KELAS {{ $tingkat }} ({{ $siswa->rombel?->jurusan?->kode ?? '' }})
				@else
					NAIK KE KELAS {{ $nextTingkat }} ({{ $siswa->rombel?->jurusan?->kode ?? '' }})
				@endif
			</p>
		@endif
	</div>
	@endif

	<!-- Signatures -->
	<table class="signature-table">
		<tr>
			<td>
				Mengetahui,<br>
				Orang Tua/Wali<br><br><br><br><br>
				<strong>...................................</strong>
			</td>
			<td>
				<br>
				Wali Kelas<br><br><br><br><br>
				<strong>{{ $siswa->rombel->waliKelas->name ?? ($siswa->rombel->waliKelas->nama_lengkap ?? '...................................') }}</strong>
			</td>
			<td>
				{{ $periode?->tempat ?: ($sekolah->kabupaten ?: 'Garut') }}, {{ \Carbon\Carbon::parse($tanggal)->translatedFormat('d F Y') }}<br>
				Kepala Sekolah,<br><br><br><br><br>
				<strong>{{ $sekolah->kepala_sekolah_nama ?: ($sekolah->kepala_sekolah ?: '...................................') }}</strong><br>
				NIP. {{ $sekolah->kepala_sekolah_nip ?: ($sekolah->nip_kepala_sekolah ?? '-') }}
			</td>
		</tr>
	</table>

</body>
</html>
