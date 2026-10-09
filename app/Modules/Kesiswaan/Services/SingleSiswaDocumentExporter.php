<?php

namespace App\Modules\Kesiswaan\Services;

use App\Core\Services\DocumentEngine;
use App\Core\Support\DataMasker;
use App\Modules\Kesiswaan\Models\Siswa;
use App\Modules\Sistem\Models\School;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SingleSiswaDocumentExporter
{
    /**
     * Unduh berkas PDF Biodata Lengkap Siswa
     */
    public function downloadPdf(Siswa $siswa): StreamedResponse
    {
        $siswa->loadMissing(['rombel.waliKelas', 'rayon.pembimbing', 'program', 'orangTuas']);

        $canViewSensitive = DataMasker::canViewSensitive();
        $htmlContent = view('kesiswaan::documents.biodata-pdf-content', [
            'siswa' => $siswa,
            'canViewSensitive' => $canViewSensitive,
        ])->render();

        $nisDisplay = $siswa->nipd ?: $siswa->nisn;
        $nisnDisplay = $canViewSensitive ? $siswa->nisn : DataMasker::maskNisn($siswa->nisn);

        $pdf = DocumentEngine::createPdf(
            title: 'LEMBAR BUKU INDUK SISWA',
            content: $htmlContent,
            options: [
                'subtitle' => "NIS: {$nisDisplay} | NISN: {$nisnDisplay}",
                'template_kode' => 'biodata_siswa',
                'context' => [
                    'wali_kelas' => $siswa->rombel?->waliKelas,
                    'pembimbing_rayon' => $siswa->rayon?->pembimbing,
                ],
            ]
        );

        $filename = $this->buildFilename($siswa, 'pdf');

        return response()->streamDownload(
            fn () => print($pdf->output()),
            $filename,
            ['Content-Type' => 'application/pdf']
        );
    }

    /**
     * Unduh berkas Word (.doc) Biodata Lengkap Siswa
     */
    public function downloadWord(Siswa $siswa): StreamedResponse
    {
        $siswa->loadMissing(['rombel.waliKelas', 'rayon.pembimbing', 'program', 'orangTuas']);
        $school = School::current();
        $canViewSensitive = DataMasker::canViewSensitive();

        $wordHtml = view('kesiswaan::documents.biodata-word', [
            'siswa' => $siswa,
            'school' => $school,
            'canViewSensitive' => $canViewSensitive,
        ])->render();

        $filename = $this->buildFilename($siswa, 'doc');

        return response()->streamDownload(
            fn () => print($wordHtml),
            $filename,
            [
                'Content-Type' => 'application/msword; charset=utf-8',
                'Cache-Control' => 'max-age=0',
            ]
        );
    }

    /**
     * Unduh berkas Excel (.xlsx) Biodata Lengkap Siswa
     */
    public function downloadExcel(Siswa $siswa): StreamedResponse
    {
        $siswa->loadMissing(['rombel', 'rayon', 'program', 'orangTuas']);
        $school = School::current();
        $canViewSensitive = DataMasker::canViewSensitive();

        $ayah = $siswa->ayah;
        $ibu = $siswa->ibu;
        $wali = $siswa->wali;

        $nik = $canViewSensitive ? $siswa->nik : DataMasker::maskNik($siswa->nik);
        $nisn = $canViewSensitive ? $siswa->nisn : DataMasker::maskNisn($siswa->nisn);
        $noKk = $canViewSensitive ? $siswa->no_kk : DataMasker::maskKk($siswa->no_kk);
        $nikAyah = $canViewSensitive ? ($ayah?->nik ?? '-') : DataMasker::maskNik($ayah?->nik);
        $nikIbu = $canViewSensitive ? ($ibu?->nik ?? '-') : DataMasker::maskNik($ibu?->nik);
        $nikWali = $canViewSensitive ? ($wali?->nik ?? '-') : DataMasker::maskNik($wali?->nik);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Biodata Siswa');

        // Header Title
        $sheet->mergeCells('A1:D1');
        $sheet->setCellValue('A1', strtoupper($school->nama ?? 'SMK WIKRAMA 1 GARUT'));
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => '1E40AF']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        $sheet->mergeCells('A2:D2');
        $sheet->setCellValue('A2', 'LEMBAR BUKU INDUK SISWA (BIODATA DETAIL)');
        $sheet->getStyle('A2')->applyFromArray([
            'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => '334155']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        $sheet->mergeCells('A3:D3');
        $sheet->setCellValue('A3', 'Dicetak pada: ' . now()->translatedFormat('d F Y H:i'));
        $sheet->getStyle('A3')->applyFromArray([
            'font' => ['italic' => true, 'size' => 9, 'color' => ['rgb' => '64748B']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        $rows = [
            // I. IDENTITAS
            ['type' => 'section', 'title' => 'I. IDENTITAS PRIBADI PESERTA DIDIK'],
            ['label' => 'Nama Lengkap Siswa', 'value' => $siswa->nama],
            ['label' => 'Nomor Induk Siswa (NIS / NIPD)', 'value' => $siswa->nipd ?: '-', 'is_string' => true],
            ['label' => 'NISN', 'value' => $nisn, 'is_string' => true],
            ['label' => 'Nomor Induk Kependudukan (NIK)', 'value' => $nik, 'is_string' => true],
            ['label' => 'Jenis Kelamin', 'value' => $siswa->jk === 'L' ? 'Laki-laki (L)' : 'Perempuan (P)'],
            ['label' => 'Tempat Lahir', 'value' => $siswa->tempat_lahir ?: '-'],
            ['label' => 'Tanggal Lahir', 'value' => $siswa->tanggal_lahir ? $siswa->tanggal_lahir->format('d/m/Y') : '-'],
            ['label' => 'Agama', 'value' => $siswa->agama ?: '-'],
            ['label' => 'Rombongan Belajar (Rombel)', 'value' => $siswa->rombel?->nama ?? '-'],
            ['label' => 'Rayon Wilayah', 'value' => $siswa->rayon?->nama ?? '-'],
            ['label' => 'Program Siswa', 'value' => $siswa->program?->nama ?? 'Reguler'],
            ['label' => 'Nomor HP / WhatsApp', 'value' => $siswa->hp ?: '-', 'is_string' => true],
            ['label' => 'Alamat Email', 'value' => $siswa->email ?: '-'],
            ['label' => 'Status Keaktifan', 'value' => ucfirst($siswa->status ?: 'aktif')],

            // II. ALAMAT
            ['type' => 'section', 'title' => 'II. ALAMAT DAN TEMPAT TINGGAL'],
            ['label' => 'Alamat Jalan', 'value' => $siswa->alamat ?: '-'],
            ['label' => 'RT / RW', 'value' => 'RT ' . ($siswa->rt ?: '-') . ' / RW ' . ($siswa->rw ?: '-'), 'is_string' => true],
            ['label' => 'Dusun', 'value' => $siswa->dusun ?: '-'],
            ['label' => 'Kelurahan / Desa', 'value' => $siswa->kelurahan ?: '-'],
            ['label' => 'Kecamatan', 'value' => $siswa->kecamatan ?: '-'],
            ['label' => 'Kode Pos', 'value' => $siswa->kode_pos ?: '-', 'is_string' => true],
            ['label' => 'Jenis Tempat Tinggal', 'value' => $siswa->jenis_tinggal ?: '-'],
            ['label' => 'Alat Transportasi ke Sekolah', 'value' => $siswa->alat_transportasi ?: '-'],
            ['label' => 'Jarak Rumah ke Sekolah', 'value' => $siswa->jarak_ke_sekolah_km ? $siswa->jarak_ke_sekolah_km . ' km' : '-'],
            ['label' => 'Koordinat Lintang / Bujur', 'value' => ($siswa->lintang ?: '-') . ' / ' . ($siswa->bujur ?: '-')],

            // III. ORANG TUA / WALI
            ['type' => 'section', 'title' => 'III. DATA ORANG TUA DAN WALI'],
            ['label' => 'Nama Ayah Kandung', 'value' => $ayah?->nama ?? '-'],
            ['label' => 'NIK Ayah', 'value' => $nikAyah, 'is_string' => true],
            ['label' => 'Tahun Lahir Ayah', 'value' => $ayah?->tahun_lahir ?? '-', 'is_string' => true],
            ['label' => 'Pendidikan Terakhir Ayah', 'value' => $ayah?->jenjang_pendidikan ?? '-'],
            ['label' => 'Pekerjaan Ayah', 'value' => $ayah?->pekerjaan ?? '-'],
            ['label' => 'Penghasilan Bulanan Ayah', 'value' => $ayah?->penghasilan ?? '-'],
            ['label' => 'No. HP Ayah', 'value' => $ayah?->no_hp ?? '-', 'is_string' => true],

            ['label' => 'Nama Ibu Kandung', 'value' => $ibu?->nama ?? '-'],
            ['label' => 'NIK Ibu', 'value' => $nikIbu, 'is_string' => true],
            ['label' => 'Tahun Lahir Ibu', 'value' => $ibu?->tahun_lahir ?? '-', 'is_string' => true],
            ['label' => 'Pendidikan Terakhir Ibu', 'value' => $ibu?->jenjang_pendidikan ?? '-'],
            ['label' => 'Pekerjaan Ibu', 'value' => $ibu?->pekerjaan ?? '-'],
            ['label' => 'Penghasilan Bulanan Ibu', 'value' => $ibu?->penghasilan ?? '-'],
            ['label' => 'No. HP Ibu', 'value' => $ibu?->no_hp ?? '-', 'is_string' => true],

            ['label' => 'Nama Wali Siswa', 'value' => $wali?->nama ?? '-'],
            ['label' => 'NIK Wali', 'value' => $nikWali, 'is_string' => true],
            ['label' => 'Pekerjaan & Kontak Wali', 'value' => ($wali?->pekerjaan ?? '-') . ' (' . ($wali?->no_hp ?? '-') . ')'],

            // IV. BANTUAN & DOKUMEN
            ['type' => 'section', 'title' => 'IV. BANTUAN SOSIAL, REKENING & DOKUMEN PENDUKUNG'],
            ['label' => 'Nomor Kartu Keluarga (KK)', 'value' => $noKk ?: '-', 'is_string' => true],
            ['label' => 'No. Registrasi Akta Lahir', 'value' => $siswa->no_akta_lahir ?: '-', 'is_string' => true],
            ['label' => 'Sekolah Asal (SMP/MTs)', 'value' => $siswa->sekolah_asal ?: '-'],
            ['label' => 'No. Peserta UN / No. Seri Ijazah', 'value' => ($siswa->no_peserta_un ?: '-') . ' / ' . ($siswa->no_seri_ijazah ?: '-'), 'is_string' => true],
            ['label' => 'SKHUN', 'value' => $siswa->skhun ?: '-', 'is_string' => true],
            ['label' => 'Penerima KPS (No. KPS)', 'value' => $siswa->penerima_kps ? 'Ya (' . ($siswa->no_kps ?: '-') . ')' : 'Tidak'],
            ['label' => 'Penerima KIP (Nomor KIP)', 'value' => $siswa->penerima_kip ? 'Ya (' . ($siswa->nomor_kip ?: '-') . ' a.n ' . ($siswa->nama_di_kip ?: '-') . ')' : 'Tidak'],
            ['label' => 'Nomor KKS', 'value' => $siswa->nomor_kks ?: '-', 'is_string' => true],
            ['label' => 'Layak PIP', 'value' => $siswa->layak_pip ? 'Ya (Alasan: ' . ($siswa->alasan_layak_pip ?: '-') . ')' : 'Tidak'],
            ['label' => 'Rekening Bank Siswa', 'value' => ($siswa->bank ?: '-') . ' - No. ' . ($siswa->nomor_rekening_bank ?: '-') . ' a.n ' . ($siswa->rekening_atas_nama ?: '-'), 'is_string' => true],

            // V. DATA PERIODIK & FISIK
            ['type' => 'section', 'title' => 'V. DATA PERIODIK DAN FISIK'],
            ['label' => 'Tinggi / Berat Badan', 'value' => ($siswa->tinggi_badan ? $siswa->tinggi_badan . ' cm' : '-') . ' / ' . ($siswa->berat_badan ? $siswa->berat_badan . ' kg' : '-')],
            ['label' => 'Lingkar Kepala', 'value' => $siswa->lingkar_kepala ? $siswa->lingkar_kepala . ' cm' : '-'],
            ['label' => 'Status Anak & Jumlah Saudara', 'value' => 'Anak ke-' . ($siswa->anak_ke_berapa ?: '-') . ' dari ' . (($siswa->jml_saudara_kandung !== null) ? ($siswa->jml_saudara_kandung + 1) : '-') . ' bersaudara'],
            ['label' => 'Kebutuhan Khusus', 'value' => $siswa->kebutuhan_khusus ?: 'Tidak Ada'],
        ];

        $currentRow = 5;
        $numIndex = 1;

        foreach ($rows as $item) {
            if (($item['type'] ?? '') === 'section') {
                $sheet->mergeCells("A{$currentRow}:D{$currentRow}");
                $sheet->setCellValue("A{$currentRow}", '  ' . $item['title']);
                $sheet->getStyle("A{$currentRow}:D{$currentRow}")->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 10],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1E40AF']],
                    'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $sheet->getRowDimension($currentRow)->setRowHeight(24);
                $numIndex = 1;
            } else {
                $sheet->setCellValue("A{$currentRow}", $numIndex++);
                $sheet->setCellValue("B{$currentRow}", $item['label']);
                $sheet->setCellValue("C{$currentRow}", ':');

                if (!empty($item['is_string'])) {
                    $sheet->setCellValueExplicit("D{$currentRow}", (string) $item['value'], DataType::TYPE_STRING);
                } else {
                    $sheet->setCellValue("D{$currentRow}", $item['value']);
                }

                $sheet->getStyle("A{$currentRow}:D{$currentRow}")->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E2E8F0']]],
                    'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                ]);

                $sheet->getStyle("A{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("B{$currentRow}")->getFont()->setBold(true);
                $sheet->getStyle("B{$currentRow}")->getFont()->getColor()->setRGB('334155');
                $sheet->getStyle("C{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getRowDimension($currentRow)->setRowHeight(20);
            }
            $currentRow++;
        }

        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(6);
        $sheet->getColumnDimension('B')->setWidth(34);
        $sheet->getColumnDimension('C')->setWidth(4);
        $sheet->getColumnDimension('D')->setWidth(50);

        $filename = $this->buildFilename($siswa, 'xlsx');

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    protected function buildFilename(Siswa $siswa, string $ext): string
    {
        $namaClean = preg_replace('/[^A-Za-z0-9_\-]/', '_', $siswa->nama);
        $nis = $siswa->nipd ?: $siswa->nisn;
        return "Biodata_Siswa_{$namaClean}_{$nis}.{$ext}";
    }
}
