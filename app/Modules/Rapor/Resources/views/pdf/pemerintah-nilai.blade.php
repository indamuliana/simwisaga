<!DOCTYPE html>
<html lang="id">
<head>
	<meta charset="UTF-8">
	<title>Rapor Pemerintah - {{ count($studentsData) === 1 ? ($studentsData[0]['siswa']->nama ?? 'Siswa') : 'Kumpulan Rapor' }}</title>
	<style>
		body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 9.5pt; line-height: 1.4; color: #000; margin: 0; padding: 0; }
		.header-kop { text-align: center; border-bottom: 3px double #000; margin-bottom: 16px; padding-bottom: 8px; }
		.header-kop h1 { margin: 0; font-size: 17pt; text-transform: uppercase; font-weight: 800; }
		.header-kop h2 { margin: 2px 0; font-size: 13pt; font-weight: 700; }
		.header-kop p { margin: 0; font-size: 9pt; }
		
		.title { text-align: center; font-size: 13.5pt; font-weight: bold; margin-bottom: 16px; text-transform: uppercase; }
		
		.info-table { width: 100%; margin-bottom: 16px; font-weight: bold; font-size: 9pt; }
		.info-table td { padding: 2px; }
		
		.data-table { width: 100%; border-collapse: collapse; margin-bottom: 16px; font-size: 9pt; }
		.data-table th, .data-table td { border: 1px solid #000; padding: 5px 6px; }
		.data-table th { background-color: #f1f5f9; text-align: center; font-weight: bold; }
		
		.catatan-box { border: 1px solid #000; padding: 10px; min-height: 45px; margin-bottom: 16px; font-size: 9pt; line-height: 1.4; }
		
		.signature-table { width: 100%; margin-top: 25px; text-align: center; page-break-inside: avoid; font-size: 9pt; }
		.signature-table td { width: 33.33%; vertical-align: bottom; height: 95px; }

		.page-break { page-break-after: always; }
	</style>
</head>
<body>

@foreach($studentsData as $idx => $data)
	@php
		$siswa = $data['siswa'];
		$semester = $data['semester'];
		$sekolah = $data['sekolah'];
		$tanggal = $data['tanggal'];
		$groupedAcademic = $data['groupedAcademic'];
		$ekskuls = $data['ekskuls'];
		$rekapAbsen = $data['rekapAbsen'];
		$catatan = $data['catatan'];

		$tingkat = $siswa->rombel?->tingkat ?? 'X';
		$fase = in_array($tingkat, ['XI', 'XII']) ? 'F' : 'E';
		$semNama = ucfirst($semester->semester ?? '');
	@endphp

	<div class="{{ !$loop->last ? 'page-break' : '' }}">
		<!-- Kop Surat -->
		<div class="header-kop">
			@if($sekolah->kop_surat_path)
				<img src="{{ storage_path('app/public/' . $sekolah->kop_surat_path) }}" style="width: 100%; max-height: 140px; object-fit: contain;">
			@else
				@if($sekolah->logo_sekolah_path)
					<img src="{{ storage_path('app/public/' . $sekolah->logo_sekolah_path) }}" style="height:75px; position:absolute; left:20px; top:0;">
				@endif
				<h2>{{ $sekolah->nama_yayasan ?? 'YAYASAN PRAWITAMA' }}</h2>
				<h1>{{ $sekolah->nama ?? 'SMK WIKRAMA BOGOR' }}</h1>
				<p>{{ $sekolah->alamat }} Telp. {{ $sekolah->telepon }}</p>
				<p>Website: {{ $sekolah->website }} | Email: {{ $sekolah->email }}</p>
			@endif
		</div>

		<!-- Judul Resmi Kurikulum Merdeka -->
		<div class="title">LAPORAN HASIL BELAJAR PESERTA DIDIK</div>

		<!-- Identitas Siswa -->
		<table class="info-table">
			<tr>
				<td width="18%">Nama Peserta Didik</td><td width="2%">:</td><td width="38%">{{ $siswa->nama }}</td>
				<td width="16%">Kelas</td><td width="2%">:</td><td width="24%">{{ $siswa->rombel->nama ?? '-' }}</td>
			</tr>
			<tr>
				<td>NISN / NIS</td><td>:</td><td>{{ $siswa->nisn ?? '-' }} / {{ $siswa->nipd ?? '-' }}</td>
				<td>Fase / Semester</td><td>:</td><td>{{ $fase }} / {{ $semNama }}</td>
			</tr>
			<tr>
				<td>Kompetensi Keahlian</td><td>:</td><td>{{ $siswa->rombel->jurusan->nama ?? '-' }}</td>
				<td>Tahun Pelajaran</td><td>:</td><td>{{ $semester->tahun_ajaran->nama ?? '-' }}</td>
			</tr>
		</table>

		<!-- A. Nilai Akademik Standar Pemerintah -->
		<div style="font-weight: bold; margin-bottom: 5px; font-size: 9.5pt;">A. Nilai Akademik</div>
		<table class="data-table">
			<thead>
				<tr>
					<th width="4%">No</th>
					<th width="28%">Mata Pelajaran</th>
					<th width="10%">Nilai Akhir</th>
					<th width="12%">Predikat</th>
					<th width="46%">Capaian Kompetensi</th>
				</tr>
			</thead>
			<tbody>
				@if($groupedAcademic->isNotEmpty())
					@foreach($groupedAcademic as $namaKelompok => $items)
						<tr style="background-color: #f8fafc;">
							<td colspan="5" style="font-weight: bold; padding: 4px 6px; font-size: 9pt;">
								{{ $namaKelompok }}
							</td>
						</tr>
						@foreach($items as $idxItem => $row)
							<tr>
								<td style="text-align: center;">{{ $idxItem + 1 }}</td>
								<td style="font-weight: 500;">{{ $row->mapel_nama }}</td>
								<td style="text-align: center; font-family: monospace; font-weight: bold; font-size: 10pt;">
									{{ $row->nilai_akhir !== null ? number_format($row->nilai_akhir, 0) : '-' }}
								</td>
								<td style="text-align: center; font-size: 8.5pt; font-weight: bold;">
									{{ $row->predikat ?? '-' }}
								</td>
								<td style="font-size: 8.5pt; line-height: 1.4; color: #1e293b;">
									@if($row->deskripsi_manual)
										{!! nl2br(e($row->deskripsi_manual)) !!}
									@else
										@if(!empty($row->deskripsi_tertinggi))
											<div>{{ $row->deskripsi_tertinggi }}</div>
										@endif
										@if(!empty($row->deskripsi_terendah))
											<div style="margin-top: 4px;">{{ $row->deskripsi_terendah }}</div>
										@endif
										@if(empty($row->deskripsi_tertinggi) && empty($row->deskripsi_terendah))
											<span style="color: #94a3b8; font-style: italic;">Belum ada deskripsi</span>
										@endif
									@endif
								</td>
							</tr>
						@endforeach
					@endforeach
				@else
					<tr>
						<td colspan="5" style="text-align: center; color: #64748b; padding: 14px; font-style: italic;">
							Belum ada data nilai akademik untuk semester ini.
						</td>
					</tr>
				@endif
			</tbody>
		</table>

		<!-- B. Ekstrakurikuler -->
		<div style="font-weight: bold; margin-bottom: 5px; font-size: 9.5pt;">B. Ekstrakurikuler</div>
		<table class="data-table">
			<thead>
				<tr>
					<th width="6%">No</th>
					<th width="44%">Kegiatan Ekstrakurikuler</th>
					<th width="50%">Keterangan / Predikat</th>
				</tr>
			</thead>
			<tbody>
				@forelse($ekskuls as $idxEks => $ekskul)
					<tr>
						<td style="text-align: center;">{{ $idxEks + 1 }}</td>
						<td>{{ $ekskul->ekstrakurikuler->nama ?? '-' }}</td>
						<td>{{ $ekskul->nilai_huruf ? ($ekskul->deskripsi ? $ekskul->nilai_huruf . ' - ' . $ekskul->deskripsi : $ekskul->nilai_huruf) : ($ekskul->deskripsi ?? ($ekskul->predikat ?? ($ekskul->keterangan ?? 'Aktif Mengikuti'))) }}</td>
					</tr>
				@empty
					<tr>
						<td colspan="3" style="text-align: center; color: #94a3b8; font-style: italic; padding: 5px;">
							-
						</td>
					</tr>
				@endforelse
			</tbody>
		</table>

		<!-- C. Ketidakhadiran -->
		<div style="font-weight: bold; margin-bottom: 5px; font-size: 9.5pt;">C. Ketidakhadiran</div>
		<table class="data-table" style="width: 50%;">
			<tr>
				<td width="60%">Sakit (S)</td>
				<td width="40%" style="text-align: center; font-weight: bold;">{{ $rekapAbsen['sakit'] }} hari</td>
			</tr>
			<tr>
				<td>Izin (I)</td>
				<td style="text-align: center; font-weight: bold;">{{ $rekapAbsen['izin'] }} hari</td>
			</tr>
			<tr>
				<td>Tanpa Keterangan (Alpa)</td>
				<td style="text-align: center; font-weight: bold;">{{ $rekapAbsen['alpa'] }} hari</td>
			</tr>
		</table>

		<!-- D. Catatan Wali Kelas -->
		<div style="font-weight: bold; margin-bottom: 5px; font-size: 9.5pt;">D. Catatan Wali Kelas</div>
		<div class="catatan-box">
			{{ $catatan ?: 'Tingkatkan terus semangat belajar dan pertahankan prestasi yang telah diraih.' }}
		</div>

		<!-- E. Keputusan Akhir Tahun (Khusus Semester Genap) -->
		@if(isset($semester) && strtolower($semester->semester) === 'genap')
		<div style="font-weight: bold; margin-bottom: 5px; font-size: 9.5pt;">E. Keputusan Akhir Tahun Ajaran</div>
		<div class="catatan-box" style="margin-bottom: 16px;">
			@php
				$riwayat = $siswa->riwayatAkademiks?->first();
				$statusKeputusan = $riwayat?->status_keputusan;
			@endphp

			@if($tingkat === 'XII' || $statusKeputusan === 'lulus' || $siswa->status === 'lulus')
				<p style="margin: 0; font-size: 9.5pt;">
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
				<p style="margin: 0; font-size: 9.5pt;">
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

		<!-- Signatures: Orang Tua, Wali Kelas, Kepala Sekolah -->
		<table class="signature-table">
			<tr>
				<td>
					Mengetahui,<br>
					Orang Tua / Wali<br><br><br><br>
					<strong>............................................</strong>
				</td>
				<td>
					{{ $sekolah->kabupaten ?: 'Bogor' }}, {{ \Carbon\Carbon::parse($tanggal)->translatedFormat('d F Y') }}<br>
					Wali Kelas,<br><br><br><br>
					<strong>{{ $siswa->rombel?->waliKelas?->nama_lengkap ?? ($siswa->rombel?->waliKelas?->name ?? '............................................') }}</strong>
				</td>
				<td>
					Mengetahui,<br>
					Kepala Sekolah,<br><br><br><br>
					<strong>{{ $sekolah->kepala_sekolah_nama ?: ($sekolah->kepala_sekolah ?: '............................................') }}</strong><br>
					@if($sekolah->kepala_sekolah_nip || $sekolah->nip_kepala_sekolah)
						<span style="font-size: 8.5pt; color: #475569;">NIP. {{ $sekolah->kepala_sekolah_nip ?: $sekolah->nip_kepala_sekolah }}</span>
					@endif
				</td>
			</tr>
		</table>
	</div>
@endforeach

</body>
</html>
