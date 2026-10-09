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
     * Export data lengkap format standar profil 66 kolom DAPODIK
     */
    public function exportLengkap(Collection $siswas): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Dapodik Lengkap');

        $headers = [
            'No',
            'Nama Siswa',
            'NIPD (NIS)',
            'JK',
            'NISN',
            'Tempat Lahir',
            'Tanggal Lahir',
            'NIK',
            'Agama',
            'Alamat Jalan',
            'RT',
            'RW',
            'Dusun',
            'Kelurahan / Desa',
            'Kecamatan',
            'Kode Pos',
            'Jenis Tinggal',
            'Alat Transportasi',
            'Telepon',
            'HP',
            'E-mail',
            'SKHUN',
            'Penerima KPS',
            'No KPS',
            'Nama Ayah',
            'Tahun Lahir Ayah',
            'Jenjang Pendidikan Ayah',
            'Pekerjaan Ayah',
            'Penghasilan Ayah',
            'NIK Ayah',
            'Nama Ibu',
            'Tahun Lahir Ibu',
            'Jenjang Pendidikan Ibu',
            'Pekerjaan Ibu',
            'Penghasilan Ibu',
            'NIK Ibu',
            'Nama Wali',
            'Tahun Lahir Wali',
            'Jenjang Pendidikan Wali',
            'Pekerjaan Wali',
            'Penghasilan Wali',
            'NIK Wali',
            'Rombel Saat Ini',
            'Rayon',
            'Program',
            'No Peserta UN',
            'No Seri Ijazah',
            'Penerima KIP',
            'Nomor KIP',
            'Nama di KIP',
            'Nomor KKS',
            'No Registrasi Akta Lahir',
            'Bank',
            'Nomor Rekening Bank',
            'Rekening Atas Nama',
            'Layak PIP',
            'Alasan Layak PIP',
            'Kebutuhan Khusus',
            'Sekolah Asal',
            'Anak ke-berapa',
            'Lintang',
            'Bujur',
            'No KK',
            'Berat Badan (kg)',
            'Tinggi Badan (cm)',
            'Lingkar Kepala (cm)',
            'Jml Saudara Kandung',
            'Jarak Rumah ke Sekolah (km)',
            'Status Siswa',
        ];

        foreach ($headers as $colIdx => $header) {
            $sheet->setCellValueByColumnAndRow($colIdx + 1, 1, $header);
        }

        $canViewSensitive = DataMasker::canViewSensitive();
        $row = 2;

        foreach ($siswas as $idx => $s) {
            $ayah = $s->ayah;
            $ibu = $s->ibu;
            $wali = $s->wali;

            $nik = $canViewSensitive ? $s->nik : DataMasker::maskNik($s->nik);
            $nisn = $canViewSensitive ? $s->nisn : DataMasker::maskNisn($s->nisn);
            $noKk = $canViewSensitive ? $s->no_kk : DataMasker::maskKk($s->no_kk);
            $nikAyah = $canViewSensitive ? ($ayah?->nik ?? '-') : DataMasker::maskNik($ayah?->nik);
            $nikIbu = $canViewSensitive ? ($ibu?->nik ?? '-') : DataMasker::maskNik($ibu?->nik);
            $nikWali = $canViewSensitive ? ($wali?->nik ?? '-') : DataMasker::maskNik($wali?->nik);
            $tglLahir = $s->tanggal_lahir ? $s->tanggal_lahir->format('d/m/Y') : '-';

            $col = 1;
            $sheet->setCellValueByColumnAndRow($col++, $row, $idx + 1);
            $sheet->setCellValueByColumnAndRow($col++, $row, $s->nama);
            $sheet->setCellValueExplicitByColumnAndRow($col++, $row, (string) ($s->nipd ?: '-'), DataType::TYPE_STRING);
            $sheet->setCellValueByColumnAndRow($col++, $row, $s->jk === 'L' ? 'L' : 'P');
            $sheet->setCellValueExplicitByColumnAndRow($col++, $row, (string) $nisn, DataType::TYPE_STRING);
            $sheet->setCellValueByColumnAndRow($col++, $row, $s->tempat_lahir ?: '-');
            $sheet->setCellValueByColumnAndRow($col++, $row, $tglLahir);
            $sheet->setCellValueExplicitByColumnAndRow($col++, $row, (string) ($nik ?: '-'), DataType::TYPE_STRING);
            $sheet->setCellValueByColumnAndRow($col++, $row, $s->agama ?: '-');
            $sheet->setCellValueByColumnAndRow($col++, $row, $s->alamat ?: '-');
            $sheet->setCellValueExplicitByColumnAndRow($col++, $row, (string) ($s->rt ?: '-'), DataType::TYPE_STRING);
            $sheet->setCellValueExplicitByColumnAndRow($col++, $row, (string) ($s->rw ?: '-'), DataType::TYPE_STRING);
            $sheet->setCellValueByColumnAndRow($col++, $row, $s->dusun ?: '-');
            $sheet->setCellValueByColumnAndRow($col++, $row, $s->kelurahan ?: '-');
            $sheet->setCellValueByColumnAndRow($col++, $row, $s->kecamatan ?: '-');
            $sheet->setCellValueExplicitByColumnAndRow($col++, $row, (string) ($s->kode_pos ?: '-'), DataType::TYPE_STRING);
            $sheet->setCellValueByColumnAndRow($col++, $row, $s->jenis_tinggal ?: '-');
            $sheet->setCellValueByColumnAndRow($col++, $row, $s->alat_transportasi ?: '-');
            $sheet->setCellValueExplicitByColumnAndRow($col++, $row, (string) ($s->telepon ?: '-'), DataType::TYPE_STRING);
            $sheet->setCellValueExplicitByColumnAndRow($col++, $row, (string) ($s->hp ?: '-'), DataType::TYPE_STRING);
            $sheet->setCellValueByColumnAndRow($col++, $row, $s->email ?: '-');
            $sheet->setCellValueByColumnAndRow($col++, $row, $s->skhun ?: '-');
            $sheet->setCellValueByColumnAndRow($col++, $row, $s->penerima_kps ? 'Ya' : 'Tidak');
            $sheet->setCellValueExplicitByColumnAndRow($col++, $row, (string) ($s->no_kps ?: '-'), DataType::TYPE_STRING);

            // Ayah
            $sheet->setCellValueByColumnAndRow($col++, $row, $ayah?->nama ?? '-');
            $sheet->setCellValueByColumnAndRow($col++, $row, $ayah?->tahun_lahir ?? '-');
            $sheet->setCellValueByColumnAndRow($col++, $row, $ayah?->jenjang_pendidikan ?? '-');
            $sheet->setCellValueByColumnAndRow($col++, $row, $ayah?->pekerjaan ?? '-');
            $sheet->setCellValueByColumnAndRow($col++, $row, $ayah?->penghasilan ?? '-');
            $sheet->setCellValueExplicitByColumnAndRow($col++, $row, (string) $nikAyah, DataType::TYPE_STRING);

            // Ibu
            $sheet->setCellValueByColumnAndRow($col++, $row, $ibu?->nama ?? '-');
            $sheet->setCellValueByColumnAndRow($col++, $row, $ibu?->tahun_lahir ?? '-');
            $sheet->setCellValueByColumnAndRow($col++, $row, $ibu?->jenjang_pendidikan ?? '-');
            $sheet->setCellValueByColumnAndRow($col++, $row, $ibu?->pekerjaan ?? '-');
            $sheet->setCellValueByColumnAndRow($col++, $row, $ibu?->penghasilan ?? '-');
            $sheet->setCellValueExplicitByColumnAndRow($col++, $row, (string) $nikIbu, DataType::TYPE_STRING);

            // Wali
            $sheet->setCellValueByColumnAndRow($col++, $row, $wali?->nama ?? '-');
            $sheet->setCellValueByColumnAndRow($col++, $row, $wali?->tahun_lahir ?? '-');
            $sheet->setCellValueByColumnAndRow($col++, $row, $wali?->jenjang_pendidikan ?? '-');
            $sheet->setCellValueByColumnAndRow($col++, $row, $wali?->pekerjaan ?? '-');
            $sheet->setCellValueByColumnAndRow($col++, $row, $wali?->penghasilan ?? '-');
            $sheet->setCellValueExplicitByColumnAndRow($col++, $row, (string) $nikWali, DataType::TYPE_STRING);

            // Akademik & Dokumen
            $sheet->setCellValueByColumnAndRow($col++, $row, $s->rombel?->nama ?? '-');
            $sheet->setCellValueByColumnAndRow($col++, $row, $s->rayon?->nama ?? '-');
            $sheet->setCellValueByColumnAndRow($col++, $row, $s->program?->nama ?? 'Reguler');
            $sheet->setCellValueExplicitByColumnAndRow($col++, $row, (string) ($s->no_peserta_un ?: '-'), DataType::TYPE_STRING);
            $sheet->setCellValueExplicitByColumnAndRow($col++, $row, (string) ($s->no_seri_ijazah ?: '-'), DataType::TYPE_STRING);
            $sheet->setCellValueByColumnAndRow($col++, $row, $s->penerima_kip ? 'Ya' : 'Tidak');
            $sheet->setCellValueExplicitByColumnAndRow($col++, $row, (string) ($s->nomor_kip ?: '-'), DataType::TYPE_STRING);
            $sheet->setCellValueByColumnAndRow($col++, $row, $s->nama_di_kip ?: '-');
            $sheet->setCellValueExplicitByColumnAndRow($col++, $row, (string) ($s->nomor_kks ?: '-'), DataType::TYPE_STRING);
            $sheet->setCellValueExplicitByColumnAndRow($col++, $row, (string) ($s->no_akta_lahir ?: '-'), DataType::TYPE_STRING);
            $sheet->setCellValueByColumnAndRow($col++, $row, $s->bank ?: '-');
            $sheet->setCellValueExplicitByColumnAndRow($col++, $row, (string) ($s->nomor_rekening_bank ?: '-'), DataType::TYPE_STRING);
            $sheet->setCellValueByColumnAndRow($col++, $row, $s->rekening_atas_nama ?: '-');
            $sheet->setCellValueByColumnAndRow($col++, $row, $s->layak_pip ? 'Ya' : 'Tidak');
            $sheet->setCellValueByColumnAndRow($col++, $row, $s->alasan_layak_pip ?: '-');
            $sheet->setCellValueByColumnAndRow($col++, $row, $s->kebutuhan_khusus ?: '-');
            $sheet->setCellValueByColumnAndRow($col++, $row, $s->sekolah_asal ?: '-');
            $sheet->setCellValueByColumnAndRow($col++, $row, $s->anak_ke_berapa ?: '-');
            $sheet->setCellValueByColumnAndRow($col++, $row, $s->lintang ?: '-');
            $sheet->setCellValueByColumnAndRow($col++, $row, $s->bujur ?: '-');
            $sheet->setCellValueExplicitByColumnAndRow($col++, $row, (string) ($noKk ?: '-'), DataType::TYPE_STRING);
            $sheet->setCellValueByColumnAndRow($col++, $row, $s->berat_badan ?: '-');
            $sheet->setCellValueByColumnAndRow($col++, $row, $s->tinggi_badan ?: '-');
            $sheet->setCellValueByColumnAndRow($col++, $row, $s->lingkar_kepala ?: '-');
            $sheet->setCellValueByColumnAndRow($col++, $row, $s->jml_saudara_kandung ?: '-');
            $sheet->setCellValueByColumnAndRow($col++, $row, $s->jarak_ke_sekolah_km ? (string) $s->jarak_ke_sekolah_km : '-');
            $sheet->setCellValueByColumnAndRow($col++, $row, ucfirst($s->status ?: 'aktif'));

            $row++;
        }

        $this->applyStyling($sheet, count($headers), max($row - 1, 1), '1D4ED8'); // Blue Theme for Full DAPODIK

        return $spreadsheet;
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
