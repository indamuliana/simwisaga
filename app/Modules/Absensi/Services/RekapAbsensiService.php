<?php

namespace App\Modules\Absensi\Services;

use App\Enums\StatusAbsensi;
use App\Modules\Absensi\Models\AbsensiHarian;
use App\Modules\Akademik\Models\Rayon;
use App\Modules\Akademik\Models\Rombel;
use App\Modules\Kesiswaan\Models\Program;
use App\Modules\Kesiswaan\Models\Siswa;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class RekapAbsensiService
{
    /**
     * Menghitung hari efektif sekolah (Senin - Jumat / Weekday) di antara 2 tanggal
     */
    public function hitungHariEfektif(Carbon|string $startDate, Carbon|string $endDate): int
    {
        $start = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::parse($endDate)->startOfDay();

        if ($start->gt($end)) {
            return 0;
        }

        $effectiveDays = 0;
        $current = $start->copy();

        while ($current->lte($end)) {
            if (!$current->isWeekend()) {
                $effectiveDays++;
            }
            $current->addDay();
        }

        return $effectiveDays;
    }

    /**
     * Membangun dataset rekap absensi lengkap berdasarkan filter
     */
    public function getRekapData(array $filters): array
    {
        $tglMulai = $filters['tgl_mulai'] ?? now()->startOfMonth()->toDateString();
        $tglSelesai = $filters['tgl_selesai'] ?? now()->toDateString();

        $rombelId = $filters['rombel_id'] ?? null;
        $rayonId = $filters['rayon_id'] ?? null;
        $gender = $filters['gender'] ?? null;
        $programId = $filters['program_id'] ?? null;
        $search = trim((string) ($filters['search'] ?? ''));

        $hariEfektif = $this->hitungHariEfektif($tglMulai, $tglSelesai);

        // 1. Query Siswa sesuai filter
        $querySiswa = Siswa::with(['rombel', 'rayon', 'program'])
            ->where('status', 'aktif');

        if (!empty($rombelId)) {
            $querySiswa->inRombel((int) $rombelId);
        }

        if (!empty($rayonId)) {
            $querySiswa->where('rayon_id', $rayonId);
        }

        if (!empty($gender)) {
            $querySiswa->where('jk', $gender);
        }

        if (!empty($programId)) {
            $querySiswa->where('program_id', $programId);
        }

        if ($search !== '') {
            $querySiswa->where(function ($q) use ($search) {
                $q->where('nipd', 'like', "%{$search}%")
                    ->orWhere('nisn', 'like', "%{$search}%")
                    ->orWhere('nama', 'like', "%{$search}%");
            });
        }

        $siswas = $querySiswa->orderBy('nama')->get();
        $siswaIds = $siswas->pluck('id')->toArray();

        // 2. Query Absensi pada rentang tanggal
        $absensiRecords = collect();
        if (!empty($siswaIds)) {
            $absensiRecords = AbsensiHarian::whereBetween('tanggal', [$tglMulai, $tglSelesai])
                ->whereIn('siswa_id', $siswaIds)
                ->orderBy('tanggal')
                ->get()
                ->groupBy('siswa_id');
        }

        // 3. Proses baris data rekap
        $rekap = [];
        foreach ($siswas as $siswa) {
            $records = $absensiRecords->get($siswa->id, collect());

            // Hadir mencakup Hadir murni + seluruh jenis Dispen (isHadirStatistik)
            $hadirCount = $records->filter(fn($r) => $r->status instanceof StatusAbsensi ? $r->status->isHadirStatistik() : false)->count();

            // S, I, A
            $sRecords = $records->where('status', StatusAbsensi::SAKIT);
            $iRecords = $records->where('status', StatusAbsensi::IZIN);
            $aRecords = $records->where('status', StatusAbsensi::ALPA);

            $sCount = $sRecords->count();
            $iCount = $iRecords->count();
            $aCount = $aRecords->count();

            // Tanggal format j/n/y (contoh: 5/1/26) dipisahkan koma
            $tglS = $sRecords->map(fn($r) => Carbon::parse($r->tanggal)->format('j/n/y'))->implode(', ') ?: '-';
            $tglI = $iRecords->map(fn($r) => Carbon::parse($r->tanggal)->format('j/n/y'))->implode(', ') ?: '-';
            $tglA = $aRecords->map(fn($r) => Carbon::parse($r->tanggal)->format('j/n/y'))->implode(', ') ?: '-';

            // Presentasi Hadir %
            $persenHadir = $hariEfektif > 0 ? round(($hadirCount / $hariEfektif) * 100, 1) : 0;

            $rekap[] = [
                'siswa_id' => $siswa->id,
                'nis' => $siswa->nipd ?: $siswa->nisn,
                'nama' => $siswa->nama,
                'rombel' => $siswa->rombel?->nama ?? '-',
                'rayon' => $siswa->rayon?->nama ?? '-',
                'gender' => strtoupper($siswa->jk) === 'L' ? 'L' : 'P',
                'h' => $hadirCount,
                's' => $sCount,
                'i' => $iCount,
                'a' => $aCount,
                'tgl_s' => $tglS,
                'tgl_i' => $tglI,
                'tgl_a' => $tglA,
                'persentase' => $persenHadir . '%',
                'persentase_numeric' => $persenHadir,
            ];
        }

        // Label Filter Terpilih
        $rombelNama = $rombelId ? Rombel::find($rombelId)?->nama : 'Semua Rombel';
        $rayonNama = $rayonId ? Rayon::find($rayonId)?->nama : 'Semua Rayon';
        $programNama = $programId ? Program::find($programId)?->nama : 'Semua Program';
        $genderNama = $gender === 'L' ? 'Laki-laki' : ($gender === 'P' ? 'Perempuan' : 'Semua Gender');

        $periodeLabel = Carbon::parse($tglMulai)->translatedFormat('d M Y') . ' s/d ' . Carbon::parse($tglSelesai)->translatedFormat('d M Y');

        return [
            'rekap' => $rekap,
            'hari_efektif' => $hariEfektif,
            'total_siswa' => count($siswas),
            'tgl_mulai' => $tglMulai,
            'tgl_selesai' => $tglSelesai,
            'periode_label' => $periodeLabel,
            'rombel_nama' => $rombelNama,
            'rayon_nama' => $rayonNama,
            'program_nama' => $programNama,
            'gender_nama' => $genderNama,
        ];
    }

    /**
     * Membuat file Excel format standar (.xlsx)
     */
    public function generateExcel(array $filters): Spreadsheet
    {
        $data = $this->getRekapData($filters);
        $rekap = $data['rekap'];

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Rekap Absensi');

        // Header Dokumen
        $sheet->setCellValue('A1', 'REKAPITULASI PRESENSI SISWA');
        $sheet->setCellValue('A2', "Periode: {$data['periode_label']} (Total Hari Efektif: {$data['hari_efektif']} Hari)");
        $sheet->setCellValue('A3', "Filter: Rombel: {$data['rombel_nama']} | Rayon: {$data['rayon_nama']} | Gender: {$data['gender_nama']} | Program: {$data['program_nama']}");

        $sheet->mergeCells('A1:L1');
        $sheet->mergeCells('A2:L2');
        $sheet->mergeCells('A3:L3');

        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A2:A3')->getFont()->setSize(10)->setItalic(true);

        // Header Kolom Tabel
        $headers = [
            'A5' => 'No',
            'B5' => 'NIS',
            'C5' => 'Nama',
            'D5' => 'Rombel',
            'E5' => 'H',
            'F5' => 'S',
            'G5' => 'I',
            'H5' => 'A',
            'I5' => 'Tanggal S',
            'J5' => 'Tanggal I',
            'K5' => 'Tanggal A',
            'L5' => 'Presentasi Hadir %',
        ];

        foreach ($headers as $cell => $val) {
            $sheet->setCellValue($cell, $val);
        }

        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => '1F2937']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E5E7EB']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '9CA3AF']]],
        ];
        $sheet->getStyle('A5:L5')->applyFromArray($headerStyle);
        $sheet->getRowDimension(5)->setRowHeight(26);

        // Baris Data
        $row = 6;
        foreach ($rekap as $idx => $r) {
            $sheet->setCellValue('A' . $row, $idx + 1);
            $sheet->setCellValueExplicit('B' . $row, (string) $r['nis'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValue('C' . $row, $r['nama']);
            $sheet->setCellValue('D' . $row, $r['rombel']);
            $sheet->setCellValue('E' . $row, $r['h']);
            $sheet->setCellValue('F' . $row, $r['s']);
            $sheet->setCellValue('G' . $row, $r['i']);
            $sheet->setCellValue('H' . $row, $r['a']);
            $sheet->setCellValue('I' . $row, $r['tgl_s']);
            $sheet->setCellValue('J' . $row, $r['tgl_i']);
            $sheet->setCellValue('K' . $row, $r['tgl_a']);
            $sheet->setCellValue('L' . $row, $r['persentase']);

            $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("B{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("D{$row}:H{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("I{$row}:K{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            $sheet->getStyle("L{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            // Highlight baris
            if ($r['a'] >= 3) {
                $sheet->getStyle("H{$row}")->getFont()->getColor()->setRGB('DC2626');
                $sheet->getStyle("H{$row}")->getFont()->setBold(true);
            }

            $row++;
        }

        $lastRow = max(6, $row - 1);
        $sheet->getStyle("A5:L{$lastRow}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB('D1D5DB');

        // Auto size kolom
        foreach (range('A', 'L') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        return $spreadsheet;
    }
}
