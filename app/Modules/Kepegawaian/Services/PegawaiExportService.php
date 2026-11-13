<?php

namespace App\Modules\Kepegawaian\Services;

use App\Modules\Kepegawaian\Models\Pegawai;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class PegawaiExportService
{
    public const HEADERS = [
        'No. Pegawai',
        'NIY',
        'Nama',
        'Jenis Kelamin',
        'NUPTK',
        'NIP (u/ PNS)',
        'No. Peserta UKG (SIMPKB-ID)',
        'No. Registrasi Guru',
        'No. Peserta Sertifikasi (Sertifikat Pendidik)',
        'NIK (No. KTP)',
        'No. Sertifikat Pendidik',
        'No. Kartu BPJS Kesehatan',
        'No. BPJS Ketenagakerjaan',
        'L/P',
        'Status Kepegawaian',
        'Jenis Kepegawaian',
        'Tempat Lahir',
        'Tanggal Lahir',
        'TMT Awal Menjadi Guru Wikrama Garut',
        'No. SK Mulai Menjadi Guru',
        'Tgl. Lulus Pendidikan Terakhir',
        'Tahun Lulus Pendidikan Terakhir',
        'Tgl. Pertama Kali Menjadi Guru Kontrak Di Wikrama',
        'No. SK Kontrak Pertama Kali Menjadi Pegawai di Wikrama',
        'Tgl. Diangkat Menjadi Pegawai Tetap',
        'No. SK Tetap Yayasan',
        'Email',
        'Gol. Darah',
        'NPWP',
        'Nama Wajib Pajak',
        'Tgl. Awal Masuk Wikrama Garut',
        'Lama Bekerja (Tahun)',
        'Masa Kerja',
        'Alamat',
        'Pendidikan Terakhir',
        'Jenjang',
        'Pengalaman Kerja 1',
        'Pengalaman Kerja 2',
        'Pengalaman Kerja 3',
        'Pengalaman Kerja 4',
        'Pengalaman Kerja 5',
        'Pengalaman Kerja 6',
        'Pengalaman Kerja 7',
        'Umur',
        'Mata Pelajaran yang Diampu',
        'Jabatan Struktural',
        'Lama Kontrak Selanjutnya',
        'Keterangan Perpanjang',
        'Jam Masuk Kerja',
        'Status Pernikahan',
        'Nama Istri/Suami',
        'Tgl. Lahir Istri/Suami',
        'Jumlah Anak',
        'Nama Anak 1',
        'Tgl. Lahir Anak 1',
        'Nama Anak 2',
        'Tgl. Lahir Anak 2',
        'Nama Anak 3',
        'Tgl. Lahir Anak 3',
        'Nama Anak 4',
        'Tgl. Lahir Anak 4',
        'Nama Ayah',
        'Nama Ibu',
        'No. Hp',
        'Keterangan',
        'Habis Kontrak',
    ];

    /**
     * Format 1 baris pegawai menjadi array data sesuai 66 kolom.
     */
    public static function formatRow(Pegawai $p, int $no): array
    {
        $exp = is_array($p->pengalaman_kerja) ? $p->pengalaman_kerja : [];
        $anak = is_array($p->data_anak) ? $p->data_anak : [];

        $statusUpper = strtoupper($p->status_kepegawaian ?: 'GTY');
        $jenisKepegawaian = in_array($statusUpper, ['GTY', 'STY', 'PNS', 'PPPK']) ? 'Tetap' : 'Tidak Tetap';

        return [
            $no,
            $p->niy ?? '',
            $p->nama_lengkap ?? '',
            $p->jenis_kelamin ?? 'L',
            $p->nuptk ? "'".$p->nuptk : '',
            $p->nip ? "'".$p->nip : '',
            $p->nomor_peserta_ukg ?? '',
            $p->nomor_registrasi_guru ?? '',
            $p->nomor_peserta_sertifikasi ?? '',
            $p->nik ? "'".$p->nik : '',
            $p->nomor_sertifikat_pendidik ?? '',
            $p->no_kartu_bpjs_kesehatan ?? '',
            $p->no_kartu_bpjs_ketenagakerjaan ?? '',
            $p->jenis_kelamin ?? 'L',
            $statusUpper,
            $jenisKepegawaian,
            $p->tempat_lahir ?? '',
            $p->tanggal_lahir?->format('Y-m-d') ?? '',
            $p->tmt_awal_guru?->format('Y-m-d') ?? '',
            $p->no_sk_mulai_guru ?? '',
            $p->tgl_lulus_pendidikan_terakhir?->format('Y-m-d') ?? '',
            $p->tahun_lulus_pendidikan_terakhir ?? '',
            $p->tgl_pertama_kontrak?->format('Y-m-d') ?? '',
            $p->no_sk_kontrak_pertama ?? '',
            $p->tgl_diangkat_pegawai_tetap?->format('Y-m-d') ?? '',
            $p->no_sk_tetap_yayasan ?? '',
            $p->email ?? '',
            $p->golongan_darah ?? '',
            $p->npwp ? "'".$p->npwp : '',
            $p->nama_wajib_pajak ?? '',
            $p->tgl_awal_masuk_wikrama?->format('Y-m-d') ?? '',
            $p->lama_bekerja_tahun !== null ? "{$p->lama_bekerja_tahun} Tahun" : '',
            $p->masa_kerja_label ?? '',
            $p->alamat ?? '',
            $p->jurusan_dan_instansi ?? $p->pendidikan_terakhir ?? '',
            $p->jenjang_pendidikan ?? '',
            $exp[0] ?? '',
            $exp[1] ?? '',
            $exp[2] ?? '',
            $exp[3] ?? '',
            $exp[4] ?? '',
            $exp[5] ?? '',
            $exp[6] ?? '',
            $p->umur !== null ? "{$p->umur} Tahun" : '',
            $p->mapel_diampu ?? '',
            $p->jabatan_struktural ?? '',
            $p->lama_kontrak_selanjutnya ?? '',
            $p->keterangan_perpanjang ?? '',
            $p->jam_kerja ?? '',
            $p->status_pernikahan ?? '',
            $p->nama_pasangan ?? '',
            $p->tgl_lahir_pasangan?->format('Y-m-d') ?? '',
            $p->jumlah_anak ?? 0,
            $anak[0]['nama'] ?? '',
            $anak[0]['tanggal_lahir'] ?? '',
            $anak[1]['nama'] ?? '',
            $anak[1]['tanggal_lahir'] ?? '',
            $anak[2]['nama'] ?? '',
            $anak[2]['tanggal_lahir'] ?? '',
            $anak[3]['nama'] ?? '',
            $anak[3]['tanggal_lahir'] ?? '',
            $p->nama_ayah ?? '',
            $p->nama_ibu ?? '',
            $p->no_hp ? "'".$p->no_hp : '',
            $p->jenis_gtk ?? '',
            $p->habis_kontrak?->format('Y-m-d') ?? '',
        ];
    }

    /**
     * Export data pegawai ke string CSV.
     */
    public function exportCsv($pegawais = null): string
    {
        $pegawais = $pegawais ?? Pegawai::orderBy('nama_lengkap')->get();

        $output = fopen('php://temp', 'r+');
        fputcsv($output, self::HEADERS);

        foreach ($pegawais as $idx => $p) {
            fputcsv($output, self::formatRow($p, $idx + 1));
        }

        rewind($output);
        $csvContent = stream_get_contents($output);
        fclose($output);

        return $csvContent;
    }

    /**
     * Generate file template CSV siap isi untuk admin.
     */
    public function downloadTemplateCsv(): string
    {
        $output = fopen('php://temp', 'r+');
        fputcsv($output, self::HEADERS);

        // Contoh baris 1 (Guru)
        fputcsv($output, [
            1,
            'PRW2107170999',
            'Fulan bin Fulan, S.Pd',
            'L',
            "'1234567890123456",
            '',
            '201503218999',
            '185231131999',
            '18021152319999',
            "'3205010101900001",
            '',
            '000123456789',
            '14003170099',
            'L',
            'GTY',
            'Tetap',
            'Garut',
            '1990-01-01',
            '2015-07-01',
            '001/YP/VII/2015',
            '2014-08-01',
            '2014',
            '2015-07-01',
            '001/YP/VII/2015',
            '2017-07-01',
            '100/YP/VII/2017',
            'fulan@smkwikrama1garut.sch.id',
            'O',
            "'123456789012000",
            'Fulan bin Fulan',
            '2015-07-01',
            '9 Tahun',
            '9 Tahun 0 Bulan',
            'Jl. Otto Iskandardinata No. 1, Garut',
            'Pendidikan Matematika, Universitas Garut',
            'S1',
            'SMP Negeri 1 Garut',
            'SMK Wikrama 1 Garut',
            '',
            '',
            '',
            '',
            '',
            '34 Tahun',
            'Matematika',
            'Guru Mapel',
            '1 Tahun',
            'Bersedia',
            '07.00 - 16.00',
            'Kawin',
            'Fulanah, S.Pd',
            '1992-05-05',
            1,
            'Ahmad Fulan',
            '2020-01-01',
            '',
            '',
            '',
            '',
            '',
            '',
            'Ayah Fulan',
            'Ibu Fulan',
            "'6281234567890",
            'Guru',
            '2027-06-30',
        ]);

        rewind($output);
        $csvContent = stream_get_contents($output);
        fclose($output);

        return $csvContent;
    }

    /**
     * Export data pegawai ke file Excel (.xlsx) dengan PhpSpreadsheet.
     */
    public function exportXlsx($pegawais = null): string
    {
        $pegawais = $pegawais ?? Pegawai::orderBy('nama_lengkap')->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data GTK Wikrama');

        // Header
        foreach (self::HEADERS as $colIndex => $headerText) {
            $cell = $sheet->getCell([$colIndex + 1, 1]);
            $cell->setValue($headerText);
        }

        // Style header
        $highestCol = $sheet->getHighestColumn();
        $sheet->getStyle("A1:{$highestCol}1")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1E3A8A'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(28);

        // Data rows
        $rowNumber = 2;
        foreach ($pegawais as $p) {
            $row = self::formatRow($p, $rowNumber - 1);
            foreach ($row as $colIndex => $val) {
                $cell = $sheet->getCell([$colIndex + 1, $rowNumber]);
                // Jika berupa teks string berawalan tanda petik satu (misal NIK / NUPTK / No HP)
                $valStr = (string) $val;
                if (str_starts_with($valStr, "'")) {
                    $cell->setValueExplicit(substr($valStr, 1), DataType::TYPE_STRING);
                } else {
                    $cell->setValue($val);
                }
            }
            $rowNumber++;
        }

        // Auto-fit kolom
        foreach (range(1, count(self::HEADERS)) as $col) {
            $sheet->getColumnDimensionByColumn($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $tempPath = tempnam(sys_get_temp_dir(), 'export_gtk_').'.xlsx';
        $writer->save($tempPath);

        return $tempPath;
    }
}
