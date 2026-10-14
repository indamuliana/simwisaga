<!DOCTYPE html>
<html lang="id">
<head>
	<meta charset="UTF-8">
	<title>Rapor Wikrama - {{ count($studentsData) === 1 ? ($studentsData[0]['siswa']->nama ?? 'Siswa') : 'Kumpulan Rapor' }}</title>
	<style>
		body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 9pt; line-height: 1.35; color: #000; margin: 0; padding: 0; }
		.header-kop { text-align: center; border-bottom: 3px double #000; margin-bottom: 12px; padding-bottom: 6px; }
		.header-kop h1 { margin: 0; font-size: 16pt; text-transform: uppercase; font-weight: 800; }
		.header-kop h2 { margin: 2px 0; font-size: 13pt; font-weight: 700; }
		.header-kop p { margin: 0; font-size: 8.5pt; color: #333; }
		
		.title { text-align: center; font-size: 12.5pt; font-weight: bold; margin-bottom: 12px; text-transform: uppercase; letter-spacing: 0.5px; }
		
		.info-table { width: 100%; margin-bottom: 12px; font-weight: bold; font-size: 8.5pt; }
		.info-table td { padding: 1.5px 2px; }
		
		.data-table { width: 100%; border-collapse: collapse; margin-bottom: 12px; font-size: 8.5pt; }
		.data-table th, .data-table td { border: 1px solid #000; padding: 4.5px 5px; }
		.data-table th { background-color: #f1f5f9; text-align: center; font-weight: bold; }
		
		.catatan-box { border: 1px solid #000; padding: 8px 10px; min-height: 40px; margin-bottom: 12px; font-size: 8.5pt; line-height: 1.4; }
		
		.signature-table { width: 100%; margin-top: 15px; text-align: center; page-break-inside: avoid; font-size: 8.5pt; }
		.signature-table td { width: 33.33%; vertical-align: bottom; height: 85px; }

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
		$periode = $data['periode'];
		$periodMeta = $data['periodMeta'];
		$groupedAcademic = $data['groupedAcademic'];
		$ekskuls = $data['ekskuls'];
		$rekapAbsen = $data['rekapAbsen'];
		$catatan = $data['catatan'];
		$ahKeys = $periodMeta['ah_keys'];
		$sumatifLabel = $periodMeta['sumatif_label'];

		$tingkat = $siswa->rombel?->tingkat ?? 'X';
		$fase = in_array($tingkat, ['XI', 'XII']) ? 'F' : 'E';
		$semNama = ucfirst($semester->semester ?? '');
	@endphp

	<div class="{{ !$loop->last ? 'page-break' : '' }}">
		<!-- Kop Surat -->
		<div class="header-kop">
			@if($sekolah->kop_surat_path)
				<img src="{{ storage_path('app/public/' . $sekolah->kop_surat_path) }}" style="width: 100%; max-height: 120px; object-fit: contain;">
			@else
				@if($sekolah->logo_sekolah_path)
					<img src="{{ storage_path('app/public/' . $sekolah->logo_sekolah_path) }}" style="height:70px; position:absolute; left:20px; top:0;">
				@endif
				<h2>{{ $sekolah->nama_yayasan ?? 'YAYASAN PRAWITAMA' }}</h2>
				<h1>{{ $sekolah->nama ?? 'SMK WIKRAMA BOGOR' }}</h1>
				<p>{{ $sekolah->alamat }} Telp. {{ $sekolah->telepon }}</p>
				<p>Website: {{ $sekolah->website }} | Email: {{ $sekolah->email }}</p>
			@endif
		</div>

		<!-- Judul Rapor -->
		<div class="title">
			LAPORAN HASIL BELAJAR PESERTA DIDIK<br>
			<span style="font-size: 10.5pt; font-weight: normal; text-transform: uppercase;">
				(RAPOR {{ $periodMeta['sebutan'] ?? '9 MINGGU' }} - SEMESTER {{ strtoupper($semester->semester ?? 'GANJIL') }})
			</span>
		</div>

		<!-- Identitas Siswa -->
		<table class="info-table">
			<tr>
				<td width="16%">Nama Peserta Didik</td><td width="2%">:</td><td width="38%">{{ $siswa->nama }}</td>
				<td width="16%">Kelas / Rombel</td><td width="2%">:</td><td width="26%">{{ $siswa->rombel->nama ?? '-' }}</td>
			</tr>
			<tr>
				<td>NISN / NIS</td><td>:</td><td>{{ $siswa->nisn ?? '-' }} / {{ $siswa->nipd ?? '-' }}</td>
				<td>Rayon / Pembimbing</td><td>:</td>
				<td>
					{{ $siswa->rayon->nama ?? '-' }}
					@if($siswa->rayon?->pembimbing)
						/ {{ $siswa->rayon->pembimbing->nama_lengkap ?? $siswa->rayon->pembimbing->name }}
					@endif
				</td>
			</tr>
			<tr>
				<td>Kompetensi Keahlian</td><td>:</td><td>{{ $siswa->rombel->jurusan->nama ?? '-' }}</td>
				<td>Fase / Semester</td><td>:</td><td>{{ $fase }} / {{ $semNama }} ({{ $periodMeta['sebutan'] }})</td>
			</tr>
			<tr>
				<td>Tahun Pelajaran</td><td>:</td><td>{{ $semester->tahun_ajaran->nama ?? '-' }}</td>
				<td>Tempat, Tanggal Cetak</td><td>:</td><td>{{ $sekolah->kabupaten ?: 'Bogor' }}, {{ \Carbon\Carbon::parse($tanggal)->translatedFormat('d F Y') }}</td>
			</tr>
		</table>

		<!-- A. Nilai Akademik -->
		<div style="font-weight: bold; margin-bottom: 4px; font-size: 9pt;">A. Nilai Akademik (Rincian Hasil Belajar)</div>
		<table class="data-table">
			<thead>
				<tr>
					<th width="4%">No</th>
					<th width="23%">Mata Pelajaran</th>
					<th width="5%">KKM</th>
					@foreach($ahKeys as $ah)
						<th width="6%">{{ $ah }}</th>
					@endforeach
					<th width="7%">{{ $sumatifLabel }}</th>
					<th width="7%">Nilai Akhir</th>
					<th width="42%">Capaian Kompetensi</th>
				</tr>
			</thead>
			<tbody>
				@if($groupedAcademic->isNotEmpty())
					@foreach($groupedAcademic as $namaKelompok => $items)
						<tr style="background-color: #f8fafc;">
							<td colspan="{{ 5 + count($ahKeys) }}" style="font-weight: bold; padding: 3px 6px; font-size: 8.5pt;">
								{{ $namaKelompok }}
							</td>
						</tr>
						@foreach($items as $idxItem => $row)
							<tr>
								<td style="text-align: center;">{{ $idxItem + 1 }}</td>
								<td style="font-weight: 500;">{{ $row->mapel_nama }}</td>
								<td style="text-align: center; font-family: monospace; font-size: 8pt; color: #475569;">{{ (int) ($row->kkm ?? 75) }}</td>
								@foreach($ahKeys as $ah)
									<td style="text-align: center; font-family: monospace;">
										{{ isset($row->ah_scores[$ah]) && $row->ah_scores[$ah] !== null ? number_format($row->ah_scores[$ah], 0) : '-' }}
									</td>
								@endforeach
								<td style="text-align: center; font-family: monospace;">
									{{ $row->sumatif !== null ? number_format($row->sumatif, 0) : '-' }}
								</td>
								<td style="text-align: center; font-family: monospace; font-weight: bold;">
									{{ $row->nilai_akhir !== null ? number_format($row->nilai_akhir, 0) : '-' }}
								</td>
								<td style="font-size: 8pt; line-height: 1.35; color: #1e293b;">
									@if($row->status === 'belum_lengkap')
										<span style="color: #b45309; font-weight: 600;">{{ $row->deskripsi_tertinggi }}</span>
									@elseif($row->status === 'belum_kompeten')
										<span style="color: #b91c1c; font-weight: 600;">{{ $row->deskripsi_tertinggi }}</span>
									@elseif($row->deskripsi_manual)
										{!! nl2br(e($row->deskripsi_manual)) !!}
									@else
										@if(!empty($row->deskripsi_tertinggi))
											<div>{{ $row->deskripsi_tertinggi }}</div>
										@endif
										@if(!empty($row->deskripsi_terendah))
											<div style="margin-top: 3px;">{{ $row->deskripsi_terendah }}</div>
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
						<td colspan="{{ 5 + count($ahKeys) }}" style="text-align: center; color: #64748b; padding: 12px; font-style: italic;">
							Belum ada data nilai akademik untuk periode ini.
						</td>
					</tr>
				@endif
			</tbody>
		</table>

		<!-- Baris Ekstrakurikuler & Ketidakhadiran Berdampingan -->
		<table style="width: 100%; border-collapse: collapse; margin-bottom: 8px;">
			<tr>
				<!-- B. Ekstrakurikuler -->
				<td style="width: 58%; vertical-align: top; padding-right: 8px;">
					<div style="font-weight: bold; margin-bottom: 4px; font-size: 9pt;">B. Ekstrakurikuler</div>
					<table class="data-table" style="margin-bottom: 0;">
						<thead>
							<tr>
								<th width="8%">No</th>
								<th width="48%">Kegiatan Ekstrakurikuler</th>
								<th width="44%">Keterangan / Predikat</th>
							</tr>
						</thead>
						<tbody>
							@forelse($ekskuls as $idxEks => $ekskul)
								<tr>
									<td style="text-align: center;">{{ $idxEks + 1 }}</td>
									<td>{{ $ekskul->ekstrakurikuler->nama ?? '-' }}</td>
									<td>{{ $ekskul->predikat ?? ($ekskul->keterangan ?? 'Aktif Mengikuti') }}</td>
								</tr>
							@empty
								<tr>
									<td colspan="3" style="text-align: center; color: #94a3b8; font-style: italic; padding: 4px;">
										-
									</td>
								</tr>
							@endforelse
						</tbody>
					</table>
				</td>

				<!-- C. Ketidakhadiran -->
				<td style="width: 42%; vertical-align: top;">
					<div style="font-weight: bold; margin-bottom: 4px; font-size: 9pt;">C. Ketidakhadiran</div>
					<table class="data-table" style="margin-bottom: 0;">
						<tr>
							<td width="60%">Sakit (S)</td>
							<td width="40%" style="text-align: center; font-weight: bold;">{{ $rekapAbsen['sakit'] }} hari</td>
						</tr>
						<tr>
							<td>Izin (I)</td>
							<td style="text-align: center; font-weight: bold;">{{ $rekapAbsen['izin'] }} hari</td>
						</tr>
						<tr>
							<td>Tanpa Keterangan (A)</td>
							<td style="text-align: center; font-weight: bold;">{{ $rekapAbsen['alpa'] }} hari</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>

		<!-- D. Catatan Pembimbing Rayon -->
		<div style="font-weight: bold; margin-bottom: 4px; font-size: 9pt;">D. Catatan Perkembangan Karakter & Akademik (Pembimbing Rayon)</div>
		<div class="catatan-box">
			{{ $catatan ?: 'Peserta didik terus menunjukkan kemajuan belajar dan kedisiplinan yang baik. Pertahankan dan tingkatkan prestasi pada periode selanjutnya.' }}
		</div>

		<!-- Tanda Tangan: Orang Tua, Pembimbing Rayon, Kepala Sekolah -->
		<table class="signature-table">
			<tr>
				<td>
					Mengetahui,<br>
					Orang Tua / Wali<br><br><br><br>
					<strong>............................................</strong>
				</td>
				<td>
					{{ $sekolah->kabupaten ?: 'Bogor' }}, {{ \Carbon\Carbon::parse($tanggal)->translatedFormat('d F Y') }}<br>
					Pembimbing Rayon,<br><br><br><br>
					<strong>{{ $siswa->rayon?->pembimbing?->nama_lengkap ?? ($siswa->rayon?->pembimbing?->name ?? '............................................') }}</strong>
				</td>
				<td>
					Mengetahui,<br>
					Kepala SMK Wikrama Bogor<br><br><br><br>
					<strong>{{ $sekolah->kepala_sekolah_nama ?: ($sekolah->kepala_sekolah ?: '............................................') }}</strong><br>
					@if($sekolah->kepala_sekolah_nip || $sekolah->nip_kepala_sekolah)
						<span style="font-size: 8pt; color: #475569;">NIP. {{ $sekolah->kepala_sekolah_nip ?: $sekolah->nip_kepala_sekolah }}</span>
					@endif
				</td>
			</tr>
		</table>
	</div>
@endforeach

</body>
</html>
