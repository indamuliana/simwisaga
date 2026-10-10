<?php

namespace App\Modules\Kesiswaan\Services;

use App\Core\Support\DataMasker;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class SiswaExporter
{
    /**
     * Export data ringkasan siswa (rekap pokok/administrasi)
     */
    public function exportRingkasan(Collection $siswas): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Ringkasan Siswa');

        $headers = [
            'No',
            'NIS / NIPD',
            'NISN',
            'Nama Lengkap',
            'JK',
            'Tempat Lahir',
            'Tanggal Lahir',
            'Agama',
            'Rombel (Kelas)',
            'Rayon',
            'Program',
            'Status',
            'No. HP Siswa',
            'Email Siswa',
            'Nama Ayah',
            'No. HP Ayah',
            'Nama Ibu',
            'No. HP Ibu',
            'Alamat Lengkap',
        ];

        // Tulis header
        foreach ($headers as $colIdx => $header) {
            $sheet->setCellValueByColumnAndRow($colIdx + 1, 1, $header);
        }

        $canViewSensitive = DataMasker::canViewSensitive();
        $row = 2;

        foreach ($siswas as $idx => $s) {
            $ayah = $s->ayah;
            $ibu = $s->ibu;

            $alamatParts = array_filter([
                $s->alamat,
                ($s->rt || $s->rw) ? "RT {$s->rt}/RW {$s->rw}" : null,
                $s->kelurahan ? "Kel. {$s->kelurahan}" : null,
                $s->kecamatan ? "Kec. {$s->kecamatan}" : null,
            ]);
            $alamatFull = implode(', ', $alamatParts) ?: '-';

            $nisn = $canViewSensitive ? $s->nisn : DataMasker::maskNisn($s->nisn);
            $nipd = $s->nipd ?: '-';
            $tglLahir = $s->tanggal_lahir ? $s->tanggal_lahir->format('d/m/Y') : '-';

            $sheet->setCellValue("A{$row}", $idx + 1);
            $sheet->setCellValueExplicit("B{$row}", $nipd, DataType::TYPE_STRING);
            $sheet->setCellValueExplicit("C{$row}", $nisn, DataType::TYPE_STRING);
            $sheet->setCellValue("D{$row}", $s->nama);
            $sheet->setCellValue("E{$row}", $s->jk === 'L' ? 'L' : 'P');
            $sheet->setCellValue("F{$row}", $s->tempat_lahir ?: '-');
            $sheet->setCellValue("G{$row}", $tglLahir);
            $sheet->setCellValue("H{$row}", $s->agama ?: '-');
            $sheet->setCellValue("I{$row}", $s->rombel?->nama ?? '-');
            $sheet->setCellValue("J{$row}", $s->rayon?->nama ?? '-');
            $sheet->setCellValue("K{$row}", $s->program?->nama ?? 'Reguler');
            $sheet->setCellValue("L{$row}", ucfirst($s->status ?: 'aktif'));
            $sheet->setCellValueExplicit("M{$row}", $s->hp ?: '-', DataType::TYPE_STRING);
            $sheet->setCellValue("N{$row}", $s->email ?: '-');
            $sheet->setCellValue("O{$row}", $ayah?->nama ?? '-');
            $sheet->setCellValueExplicit("P{$row}", $ayah?->no_hp ?? '-', DataType::TYPE_STRING);
            $sheet->setCellValue("Q{$row}", $ibu?->nama ?? '-');
            $sheet->setCellValueExplicit("R{$row}", $ibu?->no_hp ?? '-', DataType::TYPE_STRING);
            $sheet->setCellValue("S{$row}", $alamatFull);

            $row++;
        }

        $this->applyStyling($sheet, count($headers), max($row - 1, 1), '059669'); // Emerald / Green Theme for Summary

        return $spreadsheet;
    }

    /**
     * Export data lengkap format standar profil 66 kolom DAPODIK (Round-trip compatible)
     */
    public function exportLengkap(Collection $siswas): Spreadsheet
    {
        return $this->exportDapodik($siswas);
    }

    /**
     * Export data siswa sesuai format resmi 2-baris header DAPODIK (66-67 kolom)
     */
    public function exportDapodik(Collection $siswas): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Siswa Dapodik');

        $this->buildDapodikHeader($sheet);

        $canViewSensitive = DataMasker::canViewSensitive();
        $row = 3;

        foreach ($siswas as $idx => $s) {
            $ayah = $s->ayah;
            $ibu = $s->ibu;
            $wali = $s->wali;

            $nik = $canViewSensitive ? $s->nik : DataMasker::maskNik($s->nik);
            $nisn = $canViewSensitive ? $s->nisn : DataMasker::maskNisn($s->nisn);
            $noKk = $canViewSensitive ? $s->no_kk : DataMasker::maskKk($s->no_kk);
            $nikAyah = $canViewSensitive ? ($ayah?->nik ?? '') : DataMasker::maskNik($ayah?->nik);
            $nikIbu = $canViewSensitive ? ($ibu?->nik ?? '') : DataMasker::maskNik($ibu?->nik);
            $nikWali = $canViewSensitive ? ($wali?->nik ?? '') : DataMasker::maskNik($wali?->nik);
            $tglLahir = $s->tanggal_lahir ? $s->tanggal_lahir->format('Y-m-d') : '';

            $sheet->setCellValue("A{$row}", $idx + 1);
            $sheet->setCellValue("B{$row}", $s->nama);
            $sheet->setCellValueExplicit("C{$row}", (string) ($s->nipd ?: ''), DataType::TYPE_STRING);
            $sheet->setCellValue("D{$row}", $s->jk === 'P' ? 'P' : 'L');
            $sheet->setCellValueExplicit("E{$row}", (string) ($nisn ?: ''), DataType::TYPE_STRING);
            $sheet->setCellValue("F{$row}", $s->tempat_lahir ?: '');
            $sheet->setCellValue("G{$row}", $tglLahir);
            $sheet->setCellValueExplicit("H{$row}", (string) ($nik ?: ''), DataType::TYPE_STRING);
            $sheet->setCellValue("I{$row}", $s->agama ?: '');
            $sheet->setCellValue("J{$row}", $s->alamat ?: '');
            $sheet->setCellValueExplicit("K{$row}", (string) ($s->rt ?: ''), DataType::TYPE_STRING);
            $sheet->setCellValueExplicit("L{$row}", (string) ($s->rw ?: ''), DataType::TYPE_STRING);
            $sheet->setCellValue("M{$row}", $s->dusun ?: '');
            $sheet->setCellValue("N{$row}", $s->kelurahan ?: '');
            $sheet->setCellValue("O{$row}", $s->kecamatan ?: '');
            $sheet->setCellValueExplicit("P{$row}", (string) ($s->kode_pos ?: ''), DataType::TYPE_STRING);
            $sheet->setCellValue("Q{$row}", $s->jenis_tinggal ?: '');
            $sheet->setCellValue("R{$row}", $s->alat_transportasi ?: '');
            $sheet->setCellValueExplicit("S{$row}", (string) ($s->telepon ?: ''), DataType::TYPE_STRING);
            $sheet->setCellValueExplicit("T{$row}", (string) ($s->hp ?: ''), DataType::TYPE_STRING);
            $sheet->setCellValue("U{$row}", $s->email ?: '');
            $sheet->setCellValue("V{$row}", $s->skhun ?: '');
            $sheet->setCellValue("W{$row}", $s->penerima_kps ? 'Ya' : 'Tidak');
            $sheet->setCellValueExplicit("X{$row}", (string) ($s->no_kps ?: ''), DataType::TYPE_STRING);

            // Data Ayah
            $sheet->setCellValue("Y{$row}", $ayah?->nama ?? '');
            $sheet->setCellValue("Z{$row}", $ayah?->tahun_lahir ?? '');
            $sheet->setCellValue("AA{$row}", $ayah?->jenjang_pendidikan ?? '');
            $sheet->setCellValue("AB{$row}", $ayah?->pekerjaan ?? '');
            $sheet->setCellValue("AC{$row}", $ayah?->penghasilan ?? '');
            $sheet->setCellValueExplicit("AD{$row}", (string) ($nikAyah ?: ''), DataType::TYPE_STRING);

            // Data Ibu
            $sheet->setCellValue("AE{$row}", $ibu?->nama ?? '');
            $sheet->setCellValue("AF{$row}", $ibu?->tahun_lahir ?? '');
            $sheet->setCellValue("AG{$row}", $ibu?->jenjang_pendidikan ?? '');
            $sheet->setCellValue("AH{$row}", $ibu?->pekerjaan ?? '');
            $sheet->setCellValue("AI{$row}", $ibu?->penghasilan ?? '');
            $sheet->setCellValueExplicit("AJ{$row}", (string) ($nikIbu ?: ''), DataType::TYPE_STRING);

            // Data Wali
            $sheet->setCellValue("AK{$row}", $wali?->nama ?? '');
            $sheet->setCellValue("AL{$row}", $wali?->tahun_lahir ?? '');
            $sheet->setCellValue("AM{$row}", $wali?->jenjang_pendidikan ?? '');
            $sheet->setCellValue("AN{$row}", $wali?->pekerjaan ?? '');
            $sheet->setCellValue("AO{$row}", $wali?->penghasilan ?? '');
            $sheet->setCellValueExplicit("AP{$row}", (string) ($nikWali ?: ''), DataType::TYPE_STRING);

            // Rombel Saat Ini & Dokumen
            $sheet->setCellValue("AQ{$row}", $s->rombel?->nama ?? '');
            $sheet->setCellValueExplicit("AR{$row}", (string) ($s->no_peserta_un ?: ''), DataType::TYPE_STRING);
            $sheet->setCellValueExplicit("AS{$row}", (string) ($s->no_seri_ijazah ?: ''), DataType::TYPE_STRING);
            $sheet->setCellValue("AT{$row}", $s->penerima_kip ? 'Ya' : 'Tidak');
            $sheet->setCellValueExplicit("AU{$row}", (string) ($s->nomor_kip ?: ''), DataType::TYPE_STRING);
            $sheet->setCellValue("AV{$row}", $s->nama_di_kip ?: '');
            $sheet->setCellValueExplicit("AW{$row}", (string) ($s->nomor_kks ?: ''), DataType::TYPE_STRING);
            $sheet->setCellValueExplicit("AX{$row}", (string) ($s->no_akta_lahir ?: ''), DataType::TYPE_STRING);
            $sheet->setCellValue("AY{$row}", $s->bank ?: '');
            $sheet->setCellValueExplicit("AZ{$row}", (string) ($s->nomor_rekening_bank ?: ''), DataType::TYPE_STRING);
            $sheet->setCellValue("BA{$row}", $s->rekening_atas_nama ?: '');
            $sheet->setCellValue("BB{$row}", $s->layak_pip ? 'Ya' : 'Tidak');
            $sheet->setCellValue("BC{$row}", $s->alasan_layak_pip ?: '');
            $sheet->setCellValue("BD{$row}", $s->kebutuhan_khusus ?: '');
            $sheet->setCellValue("BE{$row}", $s->sekolah_asal ?: '');
            $sheet->setCellValue("BF{$row}", $s->anak_ke_berapa ?: '');
            $sheet->setCellValue("BG{$row}", $s->lintang ?: '');
            $sheet->setCellValue("BH{$row}", $s->bujur ?: '');
            $sheet->setCellValueExplicit("BI{$row}", (string) ($noKk ?: ''), DataType::TYPE_STRING);
            $sheet->setCellValue("BJ{$row}", $s->berat_badan ?: '');
            $sheet->setCellValue("BK{$row}", $s->tinggi_badan ?: '');
            $sheet->setCellValue("BL{$row}", $s->lingkar_kepala ?: '');
            $sheet->setCellValue("BM{$row}", $s->jml_saudara_kandung ?: '');
            $sheet->setCellValue("BN{$row}", $s->jarak_ke_sekolah_km ? (string) $s->jarak_ke_sekolah_km : '');
            $sheet->setCellValue("BO{$row}", $s->rayon?->nama ?? '');

            $row++;
        }

        $this->applyDapodikStyling($sheet, max($row - 1, 2));

        return $spreadsheet;
    }

    /**
     * Membuat template kosong dengan 2 baris header resmi DAPODIK + 1 baris contoh pengisian
     */
    public function downloadTemplate(): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Import Dapodik');

        $this->buildDapodikHeader($sheet);

        // Baris 3: Contoh data panduan
        $sheet->setCellValue('A3', 1);
        $sheet->setCellValue('B3', 'CONTOH NAMA SISWA');
        $sheet->setCellValueExplicit('C3', '12200760', DataType::TYPE_STRING);
        $sheet->setCellValue('D3', 'L');
        $sheet->setCellValueExplicit('E3', '0063473065', DataType::TYPE_STRING);
        $sheet->setCellValue('F3', 'GARUT');
        $sheet->setCellValue('G3', '2006-07-08');
        $sheet->setCellValueExplicit('H3', '3205020807060003', DataType::TYPE_STRING);
        $sheet->setCellValue('I3', 'Islam');
        $sheet->setCellValue('J3', 'Jl. Garut Permai No. 123');
        $sheet->setCellValueExplicit('K3', '01', DataType::TYPE_STRING);
        $sheet->setCellValueExplicit('L3', '01', DataType::TYPE_STRING);
        $sheet->setCellValue('M3', 'Dusun Contoh');
        $sheet->setCellValue('N3', 'Kelurahan Contoh');
        $sheet->setCellValue('O3', 'Kecamatan Contoh');
        $sheet->setCellValueExplicit('P3', '44100', DataType::TYPE_STRING);
        $sheet->setCellValue('Q3', 'Bersama orang tua');
        $sheet->setCellValue('R3', 'Sepeda Motor');
        $sheet->setCellValueExplicit('S3', '', DataType::TYPE_STRING);
        $sheet->setCellValueExplicit('T3', '082320877605', DataType::TYPE_STRING);
        $sheet->setCellValue('U3', 'contoh@siswa.sch.id');
        $sheet->setCellValue('V3', '');
        $sheet->setCellValue('W3', 'Tidak');
        $sheet->setCellValueExplicit('X3', '', DataType::TYPE_STRING);

        // Ayah
        $sheet->setCellValue('Y3', 'Nama Ayah Contoh');
        $sheet->setCellValue('Z3', 1977);
        $sheet->setCellValue('AA3', 'SMA / sederajat');
        $sheet->setCellValue('AB3', 'Wiraswasta');
        $sheet->setCellValue('AC3', 'Rp. 2,000,000 - Rp. 4,999,999');
        $sheet->setCellValueExplicit('AD3', '3205020802770001', DataType::TYPE_STRING);

        // Ibu
        $sheet->setCellValue('AE3', 'Nama Ibu Contoh');
        $sheet->setCellValue('AF3', 1979);
        $sheet->setCellValue('AG3', 'SMA / sederajat');
        $sheet->setCellValue('AH3', 'Ibu Rumah Tangga');
        $sheet->setCellValue('AI3', 'Tidak Berpenghasilan');
        $sheet->setCellValueExplicit('AJ3', '3205025309770002', DataType::TYPE_STRING);

        // Wali
        $sheet->setCellValue('AK3', '');
        $sheet->setCellValue('AL3', '');
        $sheet->setCellValue('AM3', '');
        $sheet->setCellValue('AN3', '');
        $sheet->setCellValue('AO3', '');
        $sheet->setCellValueExplicit('AP3', '', DataType::TYPE_STRING);

        // Rombel Saat Ini: Sengaja dikosongkan agar pengguna tahu kolom ini boleh kosong
        $sheet->setCellValue('AQ3', '');
        $sheet->setCellValueExplicit('AR3', '', DataType::TYPE_STRING);
        $sheet->setCellValueExplicit('AS3', '', DataType::TYPE_STRING);
        $sheet->setCellValue('AT3', 'Tidak');
        $sheet->setCellValueExplicit('AU3', '', DataType::TYPE_STRING);
        $sheet->setCellValue('AV3', '');
        $sheet->setCellValueExplicit('AW3', '', DataType::TYPE_STRING);
        $sheet->setCellValueExplicit('AX3', '2715/K./2006', DataType::TYPE_STRING);
        $sheet->setCellValue('AY3', 'BRI');
        $sheet->setCellValueExplicit('AZ3', '', DataType::TYPE_STRING);
        $sheet->setCellValue('BA3', '');
        $sheet->setCellValue('BB3', 'Tidak');
        $sheet->setCellValue('BC3', '');
        $sheet->setCellValue('BD3', 'Tidak ada');
        $sheet->setCellValue('BE3', 'SMP NEGERI 1 CONTOH');
        $sheet->setCellValue('BF3', 1);
        $sheet->setCellValue('BG3', '-7.209649');
        $sheet->setCellValue('BH3', '107.943336');
        $sheet->setCellValueExplicit('BI3', '3205021008070335', DataType::TYPE_STRING);
        $sheet->setCellValue('BJ3', 48);
        $sheet->setCellValue('BK3', 160);
        $sheet->setCellValue('BL3', 55);
        $sheet->setCellValue('BM3', 2);
        $sheet->setCellValue('BN3', 2.5);
        $sheet->setCellValue('BO3', 'Cisarua');

        // Berikan format baris contoh abu-abu miring (italic)
        $sheet->getStyle('A3:BO3')->applyFromArray([
            'font' => [
                'italic' => true,
                'color' => ['rgb' => '64748B'],
            ],
        ]);

        $this->applyDapodikStyling($sheet, 3);

        return $spreadsheet;
    }

    /**
     * Membangun 2 baris header resmi DAPODIK
     */
    protected function buildDapodikHeader($sheet): void
    {
        $headersRow1 = [
            'A' => 'No',
            'B' => 'Nama',
            'C' => 'NIPD',
            'D' => 'JK',
            'E' => 'NISN',
            'F' => 'Tempat Lahir',
            'G' => 'Tanggal Lahir',
            'H' => 'NIK',
            'I' => 'Agama',
            'J' => 'Alamat',
            'K' => 'RT',
            'L' => 'RW',
            'M' => 'Dusun',
            'N' => 'Kelurahan',
            'O' => 'Kecamatan',
            'P' => 'Kode Pos',
            'Q' => 'Jenis Tinggal',
            'R' => 'Alat Transportasi',
            'S' => 'Telepon',
            'T' => 'HP',
            'U' => 'E-Mail',
            'V' => 'SKHUN',
            'W' => 'Penerima KPS',
            'X' => 'No. KPS',
            'Y' => 'Data Ayah',
            'AE' => 'Data Ibu',
            'AK' => 'Data Wali',
            'AQ' => 'Rombel Saat Ini',
            'AR' => 'No Peserta Ujian Nasional',
            'AS' => 'No Seri Ijazah',
            'AT' => 'Penerima KIP',
            'AU' => 'Nomor KIP',
            'AV' => 'Nama di KIP',
            'AW' => 'Nomor KKS',
            'AX' => 'No Registrasi Akta Lahir',
            'AY' => 'Bank',
            'AZ' => 'Nomor Rekening Bank',
            'BA' => 'Rekening Atas Nama',
            'BB' => 'Layak PIP (usulan dari sekolah)',
            'BC' => 'Alasan Layak PIP',
            'BD' => 'Kebutuhan Khusus',
            'BE' => 'Sekolah Asal',
            'BF' => 'Anak ke-berapa',
            'BG' => 'Lintang',
            'BH' => 'Bujur',
            'BI' => 'No KK',
            'BJ' => 'Berat Badan',
            'BK' => 'Tinggi Badan',
            'BL' => 'Lingkar Kepala',
            'BM' => "Jml. Saudara\nKandung",
            'BN' => "Jarak Rumah\nke Sekolah (KM)",
            'BO' => 'Rayon',
        ];

        foreach ($headersRow1 as $col => $val) {
            $sheet->setCellValue($col . '1', $val);
        }

        // Merge Row 1 untuk kelompok Data Ayah, Ibu, Wali
        $sheet->mergeCells('Y1:AD1');
        $sheet->mergeCells('AE1:AJ1');
        $sheet->mergeCells('AK1:AP1');

        // Row 2 sub-headers untuk Ayah, Ibu, Wali
        $subHeaders = ['Nama', 'Tahun Lahir', 'Jenjang Pendidikan', 'Pekerjaan', 'Penghasilan', 'NIK'];
        $colsAyah = ['Y', 'Z', 'AA', 'AB', 'AC', 'AD'];
        $colsIbu  = ['AE', 'AF', 'AG', 'AH', 'AI', 'AJ'];
        $colsWali = ['AK', 'AL', 'AM', 'AN', 'AO', 'AP'];

        foreach ($subHeaders as $i => $title) {
            $sheet->setCellValue($colsAyah[$i] . '2', $title);
            $sheet->setCellValue($colsIbu[$i] . '2', $title);
            $sheet->setCellValue($colsWali[$i] . '2', $title);
        }

        // Vertically merge kolom individual (A1:A2 s/d X1:X2, dan AQ1:AQ2 s/d BO1:BO2)
        $singleCols = [
            'A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X',
            'AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ','BA','BB','BC','BD','BE','BF','BG','BH','BI','BJ','BK','BL','BM','BN','BO'
        ];
        foreach ($singleCols as $sc) {
            $sheet->mergeCells("{$sc}1:{$sc}2");
        }
    }

    /**
     * Terapkan styling tabel DAPODIK resmi (2 baris header)
     */
    protected function applyDapodikStyling($sheet, int $rowCount): void
    {
        $lastCol = 'BO';

        // Styling Row 1 dan Row 2 Header
        $sheet->getStyle("A1:{$lastCol}2")->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 9,
                'name' => 'Calibri',
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1E40AF'], // Blue Theme DAPODIK
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
        ]);

        // Bedakan sedikit kontras sub-header Ayah, Ibu, Wali row 2
        $sheet->getStyle('Y2:AP2')->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1D4ED8'],
            ],
        ]);

        $sheet->getRowDimension(1)->setRowHeight(24);
        $sheet->getRowDimension(2)->setRowHeight(22);
        $sheet->freezePane('A3');

        if ($rowCount >= 3) {
            $sheet->getStyle("A1:{$lastCol}{$rowCount}")->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'CBD5E1'],
                    ],
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ]);

            for ($r = 3; $r <= $rowCount; $r++) {
                $sheet->getRowDimension($r)->setRowHeight(20);
            }
        }

        // Auto width untuk setiap kolom
        $highestColIndex = Coordinate::columnIndexFromString($lastCol);
        for ($c = 1; $c <= $highestColIndex; $c++) {
            $sheet->getColumnDimensionByColumn($c)->setAutoSize(true);
        }
    }

    /**
     * Terapkan styling profesional pada worksheet
     */
    protected function applyStyling($sheet, int $colCount, int $rowCount, string $headerColorHex): void
    {
        $lastColLetter = Coordinate::stringFromColumnIndex($colCount);

        // Header style
        $sheet->getStyle("A1:{$lastColLetter}1")->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 10,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => $headerColorHex],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
        ]);

        $sheet->getRowDimension(1)->setRowHeight(28);
        $sheet->freezePane('A2');

        // Border tipis untuk seluruh tabel
        if ($rowCount >= 2) {
            $sheet->getStyle("A1:{$lastColLetter}{$rowCount}")->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'E2E8F0'],
                    ],
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ]);

            // Set default row height untuk baris data
            for ($r = 2; $r <= $rowCount; $r++) {
                $sheet->getRowDimension($r)->setRowHeight(20);
            }
        }

        // Auto width untuk setiap kolom
        for ($c = 1; $c <= $colCount; $c++) {
            $sheet->getColumnDimensionByColumn($c)->setAutoSize(true);
        }
    }
}
