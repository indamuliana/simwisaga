<?php

namespace App\Modules\Prestasi\Services;

use App\Modules\Akademik\Models\Rayon;
use App\Modules\Akademik\Models\Rombel;
use App\Modules\Akademik\Models\Semester;
use App\Modules\Akademik\Models\TahunAjaran;
use App\Modules\Kepegawaian\Models\Pegawai;
use App\Modules\Prestasi\Models\Prestasi;
use App\Modules\Prestasi\Models\PrestasiGuru;
use App\Modules\Prestasi\Models\PrestasiSekolah;
use App\Modules\Sistem\Models\PeriodeRapor;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class PrestasiService
{
    /**
     * Otomatis mencari PeriodeRapor berdasarkan tanggal perolehan
     */
    public function resolvePeriodeRapor(string $tanggalPerolehan, int $tahunAjaranId, int $semesterId): ?int
    {
        $date = Carbon::parse($tanggalPerolehan)->format('Y-m-d');
        $semester = Semester::find($semesterId);
        $semName = $semester ? (str_contains(strtolower($semester->semester), 'genap') ? 'genap' : 'ganjil') : 'ganjil';

        $periode = PeriodeRapor::where('tahun_ajaran_id', $tahunAjaranId)
            ->where('semester', $semName)
            ->where(function ($q) use ($date) {
                $q->where(function ($sq) use ($date) {
                    $sq->whereNotNull('tanggal_mulai')
                       ->whereNotNull('tanggal_selesai')
                       ->where('tanggal_mulai', '<=', $date)
                       ->where('tanggal_selesai', '>=', $date);
                })->orWhere(function ($sq) use ($date) {
                    $sq->whereNotNull('tanggal_bagi')
                       ->whereDate('tanggal_bagi', '>=', $date);
                });
            })
            ->orderBy('urutan')
            ->first();

        return $periode?->id;
    }

    /**
     * Mengambil daftar prestasi siswa yang approved untuk Rapor
     */
    public function getPrestasiForRapor(int $siswaId, int $semesterId, ?int $periodeRaporId = null): Collection
    {
        $query = Prestasi::with(['pembimbingPegawai', 'siswas'])
            ->whereHas('siswas', function ($q) use ($siswaId) {
                $q->where('siswas.id', $siswaId);
            })
            ->where('semester_id', $semesterId)
            ->approved();

        if ($periodeRaporId) {
            $query->where(function ($q) use ($periodeRaporId) {
                $q->where('periode_rapor_id', $periodeRaporId)
                  ->orWhereNull('periode_rapor_id');
            });
        }

        return $query->orderBy('tanggal_perolehan', 'desc')->get();
    }

    /**
     * Menghitung statistik dan leaderboard untuk Dashboard Prestasi
     */
    public function getDashboardStats(?int $tahunAjaranId = null, ?int $semesterId = null): array
    {
        $siswaQuery = Prestasi::query()->approved();
        $guruQuery = PrestasiGuru::query();
        $sekolahQuery = PrestasiSekolah::query();

        if ($tahunAjaranId) {
            $siswaQuery->where('tahun_ajaran_id', $tahunAjaranId);
            $guruQuery->where('tahun_ajaran_id', $tahunAjaranId);
            $sekolahQuery->where('tahun_ajaran_id', $tahunAjaranId);
        }

        if ($semesterId) {
            $siswaQuery->where('semester_id', $semesterId);
            $guruQuery->where('semester_id', $semesterId);
        }

        $totalSiswa = (clone $siswaQuery)->count();
        $totalGuru = (clone $guruQuery)->count();
        $totalSekolah = (clone $sekolahQuery)->count();
        $totalPending = Prestasi::pending()->count();

        // Kategori Siswa
        $kategoriSiswa = [
            'akademik' => (clone $siswaQuery)->where('kategori', 'akademik')->count(),
            'non_akademik' => (clone $siswaQuery)->where('kategori', 'non_akademik')->count(),
            'sertifikasi' => (clone $siswaQuery)->where('kategori', 'sertifikasi')->count(),
        ];

        // Tingkat Siswa
        $tingkatSiswa = [
            'sekolah' => (clone $siswaQuery)->where('tingkat', 'sekolah')->count(),
            'kecamatan' => (clone $siswaQuery)->where('tingkat', 'kecamatan')->count(),
            'kabupaten_kota' => (clone $siswaQuery)->where('tingkat', 'kabupaten_kota')->count(),
            'provinsi' => (clone $siswaQuery)->where('tingkat', 'provinsi')->count(),
            'nasional' => (clone $siswaQuery)->where('tingkat', 'nasional')->count(),
            'internasional' => (clone $siswaQuery)->where('tingkat', 'internasional')->count(),
        ];

        // Top 5 Rayon Penyumbang Prestasi (Dihitung dari seluruh siswa penerima prestasi)
        $topRayonRaw = DB::table('prestasi_siswa_anggotas')
            ->join('prestasis', 'prestasi_siswa_anggotas.prestasi_id', '=', 'prestasis.id')
            ->join('siswas', 'prestasi_siswa_anggotas.siswa_id', '=', 'siswas.id')
            ->where('prestasis.status_verifikasi', 'approved')
            ->whereNotNull('siswas.rayon_id')
            ->when($tahunAjaranId, fn($q) => $q->where('prestasis.tahun_ajaran_id', $tahunAjaranId))
            ->when($semesterId, fn($q) => $q->where('prestasis.semester_id', $semesterId))
            ->select('siswas.rayon_id', DB::raw('count(prestasi_siswa_anggotas.id) as total'))
            ->groupBy('siswas.rayon_id')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        $topRayon = $topRayonRaw->map(function ($item) {
            $rayon = Rayon::find($item->rayon_id);
            return [
                'id' => $item->rayon_id,
                'nama' => $rayon ? $rayon->nama : 'Rayon Tidak Diketahui',
                'pembimbing' => $rayon?->pembimbing?->nama_lengkap ?? '-',
                'total' => $item->total,
            ];
        });

        // Top 5 Rombel Penyumbang Prestasi
        $topRombelRaw = DB::table('prestasi_siswa_anggotas')
            ->join('prestasis', 'prestasi_siswa_anggotas.prestasi_id', '=', 'prestasis.id')
            ->join('siswas', 'prestasi_siswa_anggotas.siswa_id', '=', 'siswas.id')
            ->where('prestasis.status_verifikasi', 'approved')
            ->whereNotNull('siswas.rombel_id')
            ->when($tahunAjaranId, fn($q) => $q->where('prestasis.tahun_ajaran_id', $tahunAjaranId))
            ->when($semesterId, fn($q) => $q->where('prestasis.semester_id', $semesterId))
            ->select('siswas.rombel_id', DB::raw('count(prestasi_siswa_anggotas.id) as total'))
            ->groupBy('siswas.rombel_id')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        $topRombel = $topRombelRaw->map(function ($item) {
            $rombel = Rombel::find($item->rombel_id);
            return [
                'id' => $item->rombel_id,
                'nama' => $rombel ? $rombel->nama : 'Rombel Tidak Diketahui',
                'wali_kelas' => $rombel?->waliKelas?->nama_lengkap ?? '-',
                'total' => $item->total,
            ];
        });

        // Top 5 Guru Pembimbing/Pendamping Siswa Berprestasi
        $topGuruRaw = (clone $siswaQuery)
            ->whereNotNull('pembimbing_pegawai_id')
            ->select('pembimbing_pegawai_id', DB::raw('count(id) as total'))
            ->groupBy('pembimbing_pegawai_id')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        $topGuru = $topGuruRaw->map(function ($item) {
            $pegawai = Pegawai::find($item->pembimbing_pegawai_id);
            return [
                'id' => $item->pembimbing_pegawai_id,
                'nama' => $pegawai ? $pegawai->nama_gelar : 'Guru / GTK',
                'nip' => $pegawai?->nip ?? '-',
                'total' => $item->total,
            ];
        });

        return [
            'total_siswa' => $totalSiswa,
            'total_guru' => $totalGuru,
            'total_sekolah' => $totalSekolah,
            'total_pending' => $totalPending,
            'kategori_siswa' => $kategoriSiswa,
            'tingkat_siswa' => $tingkatSiswa,
            'top_rayon' => $topRayon,
            'top_rombel' => $topRombel,
            'top_guru' => $topGuru,
        ];
    }

    /**
     * Export Data Prestasi Siswa ke Excel Spreadsheet
     */
    public function exportSiswaExcel(array $filters = []): Spreadsheet
    {
        $query = Prestasi::with(['siswas.rombel', 'siswas.rayon', 'pembimbingPegawai', 'tahunAjaran', 'semester', 'periodeRapor'])
            ->orderBy('tanggal_perolehan', 'desc');

        if (!empty($filters['tahun_ajaran_id'])) {
            $query->where('tahun_ajaran_id', $filters['tahun_ajaran_id']);
        }
        if (!empty($filters['semester_id'])) {
            $query->where('semester_id', $filters['semester_id']);
        }
        if (!empty($filters['kategori'])) {
            $query->where('kategori', $filters['kategori']);
        }
        if (!empty($filters['tingkat'])) {
            $query->where('tingkat', $filters['tingkat']);
        }
        if (!empty($filters['status_verifikasi'])) {
            $query->where('status_verifikasi', $filters['status_verifikasi']);
        }

        $items = $query->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Prestasi Siswa');

        // Headers
        $headers = [
            'A1' => 'No',
            'B1' => 'Tipe Peserta',
            'C1' => 'Nama Tim / Siswa',
            'D1' => 'Daftar Anggota Siswa',
            'E1' => 'Kategori',
            'F1' => 'Nama Prestasi / Event',
            'G1' => 'Penyelenggara',
            'H1' => 'Lingkup',
            'I1' => 'Tingkat',
            'J1' => 'Peringkat / Capaian',
            'K1' => 'Guru Pembimbing',
            'L1' => 'Tanggal Perolehan',
            'M1' => 'Periode Rapor',
            'N1' => 'Status Verifikasi',
        ];

        foreach ($headers as $cell => $text) {
            $sheet->setCellValue($cell, $text);
        }

        $sheet->getStyle('A1:N1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '0F766E'], // Teal header
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(26);

        $row = 2;
        $no = 1;
        foreach ($items as $item) {
            $tipeLabel = $item->tipe_peserta === 'kelompok' ? 'Regu / Kelompok' : 'Individu';
            $namaTimOrSiswa = $item->tipe_peserta === 'kelompok' && $item->nama_tim 
                ? $item->nama_tim 
                : ($item->siswas->first()?->nama ?? '-');
            
            $daftarAnggota = $item->siswas->map(function ($s) {
                return $s->nama . ' (' . ($s->rombel?->nama ?? '-') . ')';
            })->implode("\n");

            $sheet->setCellValue("A{$row}", $no++);
            $sheet->setCellValue("B{$row}", $tipeLabel);
            $sheet->setCellValue("C{$row}", $namaTimOrSiswa);
            $sheet->setCellValue("D{$row}", $daftarAnggota);
            $sheet->setCellValue("E{$row}", $item->kategori_label);
            $sheet->setCellValue("F{$row}", $item->nama_prestasi);
            $sheet->setCellValue("G{$row}", $item->penyelenggara_nama);
            $sheet->setCellValue("H{$row}", $item->penyelenggara_lingkup_label);
            $sheet->setCellValue("I{$row}", $item->tingkat_label);
            $sheet->setCellValue("J{$row}", $item->peringkat);
            $sheet->setCellValue("K{$row}", $item->pembimbing_nama);
            $sheet->setCellValue("L{$row}", $item->tanggal_perolehan ? $item->tanggal_perolehan->format('d/m/Y') : '-');
            $sheet->setCellValue("M{$row}", $item->periodeRapor?->nama ?? 'Umum Semester');
            $sheet->setCellValue("N{$row}", ucfirst($item->status_verifikasi));
            $sheet->getStyle("D{$row}")->getAlignment()->setWrapText(true);
            $row++;
        }

        foreach (range('A', 'N') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        return $spreadsheet;
    }

    /**
     * Export Data Prestasi Guru ke Excel Spreadsheet
     */
    public function exportGuruExcel(array $filters = []): Spreadsheet
    {
        $query = PrestasiGuru::with(['pegawais', 'tahunAjaran', 'semester'])
            ->orderBy('tanggal_perolehan', 'desc');

        if (!empty($filters['tahun_ajaran_id'])) {
            $query->where('tahun_ajaran_id', $filters['tahun_ajaran_id']);
        }

        $items = $query->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Prestasi Guru & Tendik');

        $headers = [
            'A1' => 'No',
            'B1' => 'Tipe Peserta',
            'C1' => 'Nama Tim / GTK',
            'D1' => 'Daftar Anggota Guru/Tendik',
            'E1' => 'Kategori',
            'F1' => 'Nama Prestasi / Sertifikasi',
            'G1' => 'Penyelenggara',
            'H1' => 'Lingkup',
            'I1' => 'Tingkat',
            'J1' => 'Peringkat / Capaian',
            'K1' => 'Tanggal Perolehan',
            'L1' => 'Tahun Ajaran',
        ];

        foreach ($headers as $cell => $text) {
            $sheet->setCellValue($cell, $text);
        }

        $sheet->getStyle('A1:L1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1D4ED8'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(26);

        $row = 2;
        $no = 1;
        foreach ($items as $item) {
            $tipeLabel = $item->tipe_peserta === 'kelompok' ? 'Tim / Kelompok GTK' : 'Individu';
            $namaTimOrGuru = $item->tipe_peserta === 'kelompok' && $item->nama_tim 
                ? $item->nama_tim 
                : ($item->pegawais->first()?->nama_gelar ?? '-');

            $daftarAnggota = $item->pegawais->map(fn($p) => $p->nama_gelar)->implode("\n");

            $sheet->setCellValue("A{$row}", $no++);
            $sheet->setCellValue("B{$row}", $tipeLabel);
            $sheet->setCellValue("C{$row}", $namaTimOrGuru);
            $sheet->setCellValue("D{$row}", $daftarAnggota);
            $sheet->setCellValue("E{$row}", $item->kategori_label);
            $sheet->setCellValue("F{$row}", $item->nama_prestasi);
            $sheet->setCellValue("G{$row}", $item->penyelenggara_nama);
            $sheet->setCellValue("H{$row}", $item->penyelenggara_lingkup_label);
            $sheet->setCellValue("I{$row}", $item->tingkat_label);
            $sheet->setCellValue("J{$row}", $item->peringkat);
            $sheet->setCellValue("K{$row}", $item->tanggal_perolehan ? $item->tanggal_perolehan->format('d/m/Y') : '-');
            $sheet->setCellValue("L{$row}", $item->tahunAjaran?->nama ?? '-');
            $sheet->getStyle("D{$row}")->getAlignment()->setWrapText(true);
            $row++;
        }

        foreach (range('A', 'L') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        return $spreadsheet;
    }

    /**
     * Export Data Prestasi Sekolah ke Excel Spreadsheet
     */
    public function exportSekolahExcel(array $filters = []): Spreadsheet
    {
        $items = PrestasiSekolah::with('tahunAjaran')->orderBy('tanggal_perolehan', 'desc')->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Prestasi Sekolah');

        $headers = [
            'A1' => 'No',
            'B1' => 'Nama Prestasi / Akreditasi',
            'C1' => 'Kategori',
            'D1' => 'Penyelenggara / Lembaga',
            'E1' => 'Tingkat',
            'F1' => 'Capaian / Nilai',
            'G1' => 'Tanggal Perolehan',
            'H1' => 'Masa Berlaku Sampai',
            'I1' => 'Nomor SK / Sertifikat',
            'J1' => 'Status Berlaku',
        ];

        foreach ($headers as $cell => $text) {
            $sheet->setCellValue($cell, $text);
        }

        $sheet->getStyle('A1:J1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '9333EA'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(26);

        $row = 2;
        $no = 1;
        foreach ($items as $item) {
            $sheet->setCellValue("A{$row}", $no++);
            $sheet->setCellValue("B{$row}", $item->nama_prestasi);
            $sheet->setCellValue("C{$row}", $item->kategori_label);
            $sheet->setCellValue("D{$row}", $item->penyelenggara_nama);
            $sheet->setCellValue("E{$row}", $item->tingkat_label);
            $sheet->setCellValue("F{$row}", $item->capaian);
            $sheet->setCellValue("G{$row}", $item->tanggal_perolehan ? $item->tanggal_perolehan->format('d/m/Y') : '-');
            $sheet->setCellValue("H{$row}", $item->masa_berlaku_sampai ? $item->masa_berlaku_sampai->format('d/m/Y') : 'Permanen');
            $sheet->setCellValue("I{$row}", $item->nomor_sk ?: '-');
            $sheet->setCellValue("J{$row}", $item->status_berlaku);
            $row++;
        }

        foreach (range('A', 'J') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        return $spreadsheet;
    }
}
