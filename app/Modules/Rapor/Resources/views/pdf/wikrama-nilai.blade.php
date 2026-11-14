<!DOCTYPE html>
<html lang="id">
<head>
	<meta charset="UTF-8">
	<title>Rapor Wikrama - {{ count($studentsData) === 1 ? ($studentsData[0]['siswa']->nama ?? 'Siswa') : 'Kumpulan Rapor' }}</title>
	<style>
		@page { margin: 20px 25px 35px 25px; }
		body { font-family: 'Times New Roman', Times, serif; font-size: 8.5pt; line-height: 1.3; color: #000; margin: 0; padding: 0; }
		.header-kop { text-align: center; border-bottom: 3px double #000; margin-bottom: 10px; padding-bottom: 4px; }
		.header-kop h1 { margin: 0; font-size: 15pt; text-transform: uppercase; font-weight: 800; }
		.header-kop h2 { margin: 2px 0; font-size: 12pt; font-weight: 700; }
		.header-kop p { margin: 0; font-size: 8pt; color: #333; }
		
		.title { text-align: center; font-size: 12pt; font-weight: bold; margin-bottom: 10px; text-transform: uppercase; letter-spacing: 0.5px; }
		
		.info-table { width: 100%; margin-bottom: 10px; font-weight: bold; font-size: 10pt; }
		.info-table td { padding: 1px 2px; }
		
		.data-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; font-size: 8pt; }
		.data-table thead { display: table-header-group; }
		.data-table tr { page-break-inside: avoid; }
		.data-table th, .data-table td { border: 1px solid #000; padding: 3px 4px; vertical-align: top; }
		.data-table th { background-color: #f1f5f9; text-align: center; vertical-align: middle; font-weight: bold; font-size: 10pt; }
		
		.avoid-break { page-break-inside: avoid; }
		.catatan-box { border: 1px solid #000; padding: 6px 8px; min-height: 35px; margin-bottom: 10px; font-size: 9pt; line-height: 1.35; }
		
		.signature-table { width: 100%; margin-top: 15px; font-size: 9pt; border-collapse: collapse; page-break-inside: avoid; }
		.signature-table td { vertical-align: top; }

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
		$prestasis = $data['prestasis'] ?? collect();
		$ahKeys = $periodMeta['ah_keys'];
		$sumatifLabel = $periodMeta['sumatif_label'];

		$tingkat = $siswa->rombel?->tingkat ?? 'X';
		$fase = in_array($tingkat, ['XI', 'XII']) ? 'F' : 'E';
		$semNama = ucfirst($semester->semester ?? '');

		$kopPath = null;
		if (!empty($sekolah->kop_surat_path)) {
			$p1 = public_path('storage/' . $sekolah->kop_surat_path);
			$p2 = storage_path('app/public/' . $sekolah->kop_surat_path);
			if (file_exists($p1)) {
				$kopPath = $p1;
			} elseif (file_exists($p2)) {
				$kopPath = $p2;
			}
		}

		$logoPath = null;
		if (!empty($sekolah->logo_sekolah_path)) {
			$lp1 = public_path('storage/' . $sekolah->logo_sekolah_path);
			$lp2 = storage_path('app/public/' . $sekolah->logo_sekolah_path);
			if (file_exists($lp1)) {
				$logoPath = $lp1;
			} elseif (file_exists($lp2)) {
				$logoPath = $lp2;
			}
		}
	@endphp

	<div class="{{ !$loop->last ? 'page-break' : '' }}">
		<!-- Kop Surat -->
		@if($kopPath)
			<div class="header-kop-banner" style="text-align: center; margin-bottom: 12px;">
				<img src="{{ $kopPath }}" style="width: 100%; height: auto;" alt="Kop Surat">
			</div>
		@else
			<div class="header-kop-fallback" style="text-align: center; border-bottom: 3px double #000; margin-bottom: 12px; padding-bottom: 6px; position: relative;">
				@if($logoPath)
					<img src="{{ $logoPath }}" style="height: 65px; position: absolute; left: 15px; top: 0;" alt="Logo">
				@endif
				<h2 style="margin: 0; font-size: 11pt; font-weight: 700; text-transform: uppercase;">{{ $sekolah->nama_yayasan ?? 'YAYASAN PRAWITAMA' }}</h2>
				<h1 style="margin: 2px 0; font-size: 15pt; font-weight: 800; text-transform: uppercase;">{{ $sekolah->nama ?? 'SMK WIKRAMA 1 GARUT' }}</h1>
				<p style="margin: 0; font-size: 8.5pt; color: #333;">{{ $sekolah->alamat ?? 'Jl. Otto Iskandardinata, Tarogong Kaler, Garut' }}</p>
				<p style="margin: 0; font-size: 8.5pt; color: #333;">
					@if(!empty($sekolah->telepon)) Telp. {{ $sekolah->telepon }} | @endif
					@if(!empty($sekolah->website)) Website: {{ $sekolah->website }} | @endif
					@if(!empty($sekolah->email)) Email: {{ $sekolah->email }} @endif
				</p>
			</div>
		@endif

		<!-- Judul Rapor -->
		<div class="title">
			LAPORAN HASIL BELAJAR PESERTA DIDIK<br>
			<span style="font-size: 12pt; font-weight: normal; text-transform: uppercase;">
				(RAPOR {{ $periodMeta['sebutan'] ?? '9 MINGGU' }} - SEMESTER {{ strtoupper($semester->semester ?? 'GANJIL') }})
			</span>
		</div>

		<!-- Identitas Siswa -->
		<table class="info-table">
			<tr>
				<td width="16%">Nama</td><td width="2%">:</td><td width="36%">{{ $siswa->nama }}</td>
				<td width="16%">Rombel / Rayon</td><td width="2%">:</td><td width="28%">{{ $siswa->rombel->nama ?? '-' }} / {{ $siswa->rayon?->wilayah_cakupan ?: ($siswa->rayon?->nama ?? '-') }}</td>
			</tr>
			<tr>
				<td>NISN / NIS</td><td>:</td><td>{{ $siswa->nisn ?? '-' }} / {{ $siswa->nipd ?? '-' }}</td>
				<td>Fase / Semester</td><td>:</td><td>{{ $fase }} / {{ $semNama }} ({{ $periodMeta['sebutan'] }})</td>
			</tr>
			<tr>
				<td>Keahlian</td><td>:</td><td>{{ $siswa->rombel->jurusan->nama ?? '-' }}</td>
				<td>Tahun Pelajaran</td><td>:</td><td>{{ $semester->tahunAjaran?->nama ?? ($semester->tahun_ajaran->nama ?? '-') }}</td>
			</tr>
		</table>

		<br>

		<!-- A. Nilai Akademik -->
		<div style="font-weight: bold; margin-bottom: 4px; font-size: 10pt;">A. Nilai Akademik (Rincian Hasil Belajar)</div>
		<table class="data-table">
			<thead>
				<tr>
					<th width="4%" style="text-align: center; vertical-align: middle;">No</th>
					<th width="32%" style="text-align: center; vertical-align: middle;">Mata Pelajaran & Capaian TP</th>
					<th width="5%" style="text-align: center; vertical-align: middle;">KKM</th>
					<th width="8%" style="text-align: center; vertical-align: middle;">Nilai Harian</th>
					<th width="8%" style="text-align: center; vertical-align: middle;">{{ $sumatifLabel }}</th>
					<th width="7%" style="text-align: center; vertical-align: middle;">Nilai Akhir</th>
					<th width="10%" style="text-align: center; vertical-align: middle;">Predikat</th>
					<th width="26%" style="text-align: center; vertical-align: middle;">Capaian Kompetensi</th>
				</tr>
			</thead>
			<tbody>
				@if($groupedAcademic->isNotEmpty())
					@foreach($groupedAcademic as $namaKelompok => $items)
						<tr style="background-color: #f8fafc; page-break-after: avoid;">
							<td colspan="8" style="font-weight: bold; padding: 3px 6px; font-size: 9pt;">
								{{ $namaKelompok }}
							</td>
						</tr>
						@foreach($items as $idxItem => $row)
							@php
								$tpCount = !empty($row->tps) ? count($row->tps) : 0;
								$rowspan = 1 + $tpCount;
							@endphp
							<tr style="background-color: #ffffff; {{ $tpCount > 0 ? 'page-break-after: avoid;' : '' }}">
								<td rowspan="{{ $rowspan }}" style="text-align: center; font-weight: bold; vertical-align: top; font-size: 9pt;">{{ $idxItem + 1 }}</td>
								<td style="font-weight: bold; font-size: 9pt; vertical-align: top;">{{ $row->mapel_nama }}</td>
								<td rowspan="{{ $rowspan }}" style="text-align: center; font-size: 9pt; font-weight: bold; color: #000; vertical-align: top;">{{ (int) ($row->kkm ?? 75) }}</td>
								<td style="text-align: center; font-size: 9pt; font-weight: bold; vertical-align: top; {{ ($row->nh !== null && $row->nh < ($row->kkm ?? 75)) ? 'color: #b91c1c;' : 'color: #000;' }}">
									{{ $row->nh !== null ? number_format($row->nh, 0) : '-' }}
								</td>
								<td rowspan="{{ $rowspan }}" style="text-align: center; font-size: 9pt; font-weight: bold; vertical-align: top; {{ ($row->sumatif !== null && $row->sumatif < ($row->kkm ?? 75)) ? 'color: #b91c1c;' : 'color: #000;' }}">
									{{ $row->sumatif !== null ? number_format($row->sumatif, 0) : '-' }}
								</td>
								<td rowspan="{{ $rowspan }}" style="text-align: center; font-size: 9pt; font-weight: bold; color: #000; vertical-align: top;">
									{{ $row->nilai_akhir !== null ? number_format($row->nilai_akhir, 0) : '-' }}
								</td>
								<td style="text-align: center; font-size: 9pt; font-weight: bold; vertical-align: top;">
									{{ $row->predikat ?? '-' }}
								</td>
								<td rowspan="{{ $rowspan }}" style="font-size: 8pt; line-height: 1.35; color: #000; vertical-align: top;">
									@if($row->status === 'belum_lengkap')
										<span style="color: #b45309; font-weight: 600;">{{ $row->deskripsi_tertinggi ?: 'Nilai belum lengkap, silakan hubungi guru yang bersangkutan.' }}</span>
									@elseif($row->status === 'belum_kompeten')
										<span style="color: #b91c1c; font-weight: 600;">{{ $row->deskripsi_tertinggi ?: 'Nilai belum kompeten, silakan hubungi guru yang bersangkutan.' }}</span>
									@elseif($row->deskripsi_manual)
										{!! nl2br(e($row->deskripsi_manual)) !!}
									@else
										@if(!empty($row->deskripsi_tertinggi))
											<div>{!! nl2br(e($row->deskripsi_tertinggi)) !!}</div>
										@endif
										@if(!empty($row->deskripsi_terendah))
											<div style="{{ !empty($row->deskripsi_tertinggi) ? 'margin-top: 4px;' : '' }}">{!! nl2br(e($row->deskripsi_terendah)) !!}</div>
										@endif
										@if(empty($row->deskripsi_tertinggi) && empty($row->deskripsi_terendah))
											<span style="color: #94a3b8; font-style: italic;">Belum ada deskripsi</span>
										@endif
									@endif
								</td>
							</tr>

							<!-- Baris Bertingkat: Rincian TP Mata Pelajaran -->
							@if(!empty($row->tps) && count($row->tps) > 0)
								@foreach($row->tps as $tp)
									<tr style="background-color: #ffffff; font-size: 7.5pt; page-break-before: avoid; {{ !$loop->last ? 'page-break-after: avoid;' : '' }}">
										<td style="padding-left: 15px; color: #475569; vertical-align: top;">
											<strong>&bull;</strong> {{ $tp->deskripsi }}
										</td>
										<td style="text-align: center; font-size: 9pt; font-weight: bold; vertical-align: top; {{ ($tp->score !== null && $tp->score < ($row->kkm ?? 75)) ? 'color: #b91c1c;' : 'color: #000;' }}">
											{{ $tp->score !== null ? number_format($tp->score, 0) : '-' }}
										</td>
										<td style="text-align: center; font-size: 9pt; font-weight: bold; vertical-align: top;">
											@if($tp->score !== null)
												<span style="color: {{ $tp->score >= ($row->kkm ?? 75) ? '#000' : '#b91c1c' }};">
													{{ $tp->score >= ($row->kkm ?? 75) ? 'Tuntas' : 'Remedial' }}
												</span>
											@else
												-
											@endif
										</td>
									</tr>
								@endforeach
							@endif
						@endforeach
					@endforeach
				@else
					<tr>
						<td colspan="8" style="text-align: center; color: #64748b; padding: 12px; font-style: italic;">
							Belum ada data nilai akademik untuk periode ini.
						</td>
					</tr>
				@endif
			</tbody>
		</table>

		<div class="avoid-break">
			<!-- B. Ekstrakurikuler -->
			<div style="font-weight: bold; margin-bottom: 4px; font-size: 10pt;">B. Ekstrakurikuler</div>
			<table class="data-table" style="width: 100%; margin-bottom: 8px;">
				<thead>
					<tr>
						<th width="5%">No</th>
						<th width="45%">Kegiatan Ekstrakurikuler</th>
						<th width="50%">Keterangan / Predikat</th>
					</tr>
				</thead>
				<tbody style="font-size: 10pt;">
					@forelse($ekskuls as $idxEks => $ekskul)
						<tr style="font-size: 10pt;">
							<td style="text-align: center; font-size: 10pt;">{{ $idxEks + 1 }}</td>
							<td style="font-size: 10pt;">{{ $ekskul->ekstrakurikuler->nama ?? '-' }}</td>
							<td style="font-size: 10pt;">{{ $ekskul->nilai_huruf ? ($ekskul->deskripsi ? $ekskul->nilai_huruf . ' - ' . $ekskul->deskripsi : $ekskul->nilai_huruf) : ($ekskul->deskripsi ?? ($ekskul->predikat ?? ($ekskul->keterangan ?? 'Aktif Mengikuti'))) }}</td>
						</tr>
					@empty
						<tr style="font-size: 10pt;">
							<td colspan="3" style="text-align: center; color: #94a3b8; font-style: italic; padding: 4px; font-size: 10pt;">
								-
							</td>
						</tr>
					@endforelse
				</tbody>
			</table>

			<!-- C. Ketidakhadiran -->
			<div style="font-weight: bold; margin-bottom: 4px; font-size: 10pt;">C. Ketidakhadiran</div>
			<table class="data-table" style="width: 100%; margin-bottom: 8px;">
				<thead>
					<tr>
						<th width="22%">Jenis Ketidakhadiran</th>
						<th width="11%">Jumlah</th>
						<th width="67%" style="text-align: center;">Tanggal</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>Sakit (S)</td>
						<td style="text-align: center; font-weight: bold;">{{ $rekapAbsen['sakit'] }} hari</td>
						<td style="text-align: left; padding-left: 6px;">{{ $rekapAbsen['sakit_dates'] ?? '-' }}</td>
					</tr>
					<tr>
						<td>Izin (I)</td>
						<td style="text-align: center; font-weight: bold;">{{ $rekapAbsen['izin'] }} hari</td>
						<td style="text-align: left; padding-left: 6px;">{{ $rekapAbsen['izin_dates'] ?? '-' }}</td>
					</tr>
					<tr>
						<td>Tanpa Keterangan (A)</td>
						<td style="text-align: center; font-weight: bold;">{{ $rekapAbsen['alpa'] }} hari</td>
						<td style="text-align: left; padding-left: 6px;">{{ $rekapAbsen['alpa_dates'] ?? '-' }}</td>
					</tr>
				</tbody>
			</table>

			<!-- D. Catatan Pembimbing Rayon -->
			<div style="font-weight: bold; margin-bottom: 4px; font-size: 10pt;">D. Catatan Perkembangan Karakter & Akademik (Pembimbing Rayon)</div>
			<div class="catatan-box">
				<div style="font-size: 9pt;">{{ $catatan ?: 'Peserta didik terus menunjukkan kemajuan belajar dan kedisiplinan yang baik. Pertahankan dan tingkatkan prestasi pada periode selanjutnya.' }}</div>
				@if(!empty($prestasis) && $prestasis->isNotEmpty())
					<div style="margin-top: 5px; border-top: 1px dashed #94a3b8; padding-top: 3px; font-size: 9pt;">
						<strong style="font-size: 9pt;">Prestasi / Penghargaan yang Diraih pada Periode Ini:</strong>
						<ul style="margin: 2px 0 0 12px; padding: 0; font-size: 9pt;">
							@foreach($prestasis as $p)
								<li>
									{{ $p->nama_prestasi }} &mdash; <strong>{{ $p->peringkat }}</strong> (Tingkat {{ $p->tingkat_label }} &bull; Penyelenggara: {{ $p->penyelenggara_nama }})
								</li>
							@endforeach
						</ul>
					</div>
				@endif
			</div>

			<!-- Tanda Tangan: 2 Baris -->
			<table class="signature-table" style="width: 100%; margin-top: 20px; font-size: 9pt; border-collapse: collapse; page-break-inside: avoid;">
				<!-- Baris 1: Orang Tua/Wali & Pembimbing Siswa -->
				<tr>
					<td style="width: 50%; vertical-align: top; text-align: center; height: auto;">
						<br>
						Orang Tua/Wali<br><br><br><br><br><br>
						............................................
					</td>
					<td style="width: 50%; vertical-align: top; text-align: center; height: auto;">
						{{ $sekolah->kabupaten ?: 'Garut' }}, {{ \Carbon\Carbon::parse($tanggal)->translatedFormat('d F Y') }}<br>
						Pembimbing siswa<br><br><br><br><br><br>
						<strong>{{ $siswa->rayon?->pembimbing?->nama_lengkap ?? ($siswa->rayon?->pembimbing?->name ?? '............................................') }}</strong>
					</td>
				</tr>
				<!-- Baris 2: Kepala Sekolah di Tengah -->
				<tr>
					<td colspan="2" style="text-align: center; vertical-align: top; padding-top: 22px; height: auto;">
						Mengetahui,<br>
						Kepala {{ $sekolah->nama ?? 'Sekolah' }}<br><br><br><br><br><br>
						<strong>{{ $sekolah->kepala_sekolah_nama ?: ($sekolah->kepala_sekolah ?: '............................................') }}</strong><br>
						@if($sekolah->kepala_sekolah_nip || $sekolah->nip_kepala_sekolah)
							<span style="font-size: 9pt; color: #475569;">NIP. {{ $sekolah->kepala_sekolah_nip ?: $sekolah->nip_kepala_sekolah }}</span>
						@endif
					</td>
				</tr>
			</table>
		</div>
	</div>
@endforeach

<script type="text/php">
if (isset($pdf)) {
	$font = $fontMetrics->get_font("Times-Roman", "normal");
	$color = [0.35, 0.35, 0.35];
	$w = $pdf->get_width();
	$h = $pdf->get_height();
	$y = $h - 22;

	$pdf->page_script(function ($pageNumber, $pageCount, $canvas, $fontMetrics) use ($w, $h, $y, $font, $color) {
		// Garis tipis pembatas footer
		$canvas->line(25, $y - 4, $w - 25, $y - 4, [0.8, 0.8, 0.8], 0.5);

		@if(count($studentsData) === 1)
			$textLeft = "{{ $studentsData[0]['siswa']->nama }} - NIS: {{ $studentsData[0]['siswa']->nipd ?? $studentsData[0]['siswa']->nisn ?? '-' }} - {{ $studentsData[0]['siswa']->rombel->nama ?? '-' }}";
			$canvas->text(25, $y, $textLeft, $font, 7.5, $color);

			$textRight = "Rapor {{ $studentsData[0]['periodMeta']['sebutan'] ?? '9 Minggu' }} - Halaman " . $pageNumber . "/" . $pageCount;
			$tw = $fontMetrics->get_text_width($textRight, $font, 7.5);
			$canvas->text($w - 25 - $tw, $y, $textRight, $font, 7.5, $color);
		@else
			$textRight = "Halaman " . $pageNumber . "/" . $pageCount;
			$tw = $fontMetrics->get_text_width($textRight, $font, 7.5);
			$canvas->text($w - 25 - $tw, $y, $textRight, $font, 7.5, $color);
		@endif
	});
}
</script>

</body>
</html>
