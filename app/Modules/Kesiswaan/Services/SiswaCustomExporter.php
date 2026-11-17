<?php

namespace App\Modules\Kesiswaan\Services;

use App\Core\Support\DataMasker;
use App\Modules\Kesiswaan\Models\Siswa;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class SiswaCustomExporter
{
    /**
     * Master definisi seluruh kolom yang tersedia untuk diekspor
     */
    public static function getAvailableColumns(): array
    {
        return [
            // Identitas Pokok
            'nipd' => [
                'label' => 'NIS / NIPD',
                'category' => 'Identitas Pokok',
                'is_string' => true,
                'resolver' => fn (Siswa $s) => (string) ($s->nipd ?? ''),
            ],
            'nisn' => [
                'label' => 'NISN',
                'category' => 'Identitas Pokok',
                'is_string' => true,
                'resolver' => fn (Siswa $s, bool $sensitive) => $sensitive ? (string) ($s->nisn ?? '') : DataMasker::maskNisn($s->nisn),
            ],
            'nik' => [
                'label' => 'NIK Siswa',
                'category' => 'Identitas Pokok',
                'is_string' => true,
                'resolver' => fn (Siswa $s, bool $sensitive) => $sensitive ? (string) ($s->nik ?? '') : DataMasker::maskNik($s->nik),
            ],
            'no_kk' => [
                'label' => 'No. Kartu Keluarga (KK)',
                'category' => 'Identitas Pokok',
                'is_string' => true,
                'resolver' => fn (Siswa $s, bool $sensitive) => $sensitive ? (string) ($s->no_kk ?? '') : DataMasker::maskKk($s->no_kk),
            ],
            'nama' => [
                'label' => 'Nama Lengkap',
                'category' => 'Identitas Pokok',
                'is_string' => false,
                'resolver' => fn (Siswa $s) => (string) ($s->nama ?? ''),
            ],
            'jk' => [
                'label' => 'Jenis Kelamin (L/P)',
                'category' => 'Identitas Pokok',
                'is_string' => false,
                'resolver' => fn (Siswa $s) => $s->jk === 'L' ? 'L' : ($s->jk === 'P' ? 'P' : ''),
            ],
            'tempat_lahir' => [
                'label' => 'Tempat Lahir',
                'category' => 'Identitas Pokok',
                'is_string' => false,
                'resolver' => fn (Siswa $s) => (string) ($s->tempat_lahir ?? ''),
            ],
            'tanggal_lahir' => [
                'label' => 'Tanggal Lahir',
                'category' => 'Identitas Pokok',
                'is_string' => false,
                'resolver' => fn (Siswa $s) => $s->tanggal_lahir ? $s->tanggal_lahir->format('Y-m-d') : '',
            ],
            'agama' => [
                'label' => 'Agama',
                'category' => 'Identitas Pokok',
                'is_string' => false,
                'resolver' => fn (Siswa $s) => (string) ($s->agama ?? ''),
            ],
            'no_akta_lahir' => [
                'label' => 'No. Registrasi Akta Lahir',
                'category' => 'Identitas Pokok',
                'is_string' => true,
                'resolver' => fn (Siswa $s) => (string) ($s->no_akta_lahir ?? ''),
            ],
            'anak_ke_berapa' => [
                'label' => 'Anak Ke-berapa',
                'category' => 'Identitas Pokok',
                'is_string' => false,
                'resolver' => fn (Siswa $s) => $s->anak_ke_berapa !== null ? (string) $s->anak_ke_berapa : '',
            ],
            'jml_saudara_kandung' => [
                'label' => 'Jml Saudara Kandung',
                'category' => 'Identitas Pokok',
                'is_string' => false,
                'resolver' => fn (Siswa $s) => $s->jml_saudara_kandung !== null ? (string) $s->jml_saudara_kandung : '',
            ],

            // Akademik & Penempatan
            'tingkat' => [
                'label' => 'Tingkat',
                'category' => 'Akademik & Sekolah',
                'is_string' => false,
                'resolver' => fn (Siswa $s) => (string) ($s->rombel?->tingkat ?? ''),
            ],
            'rombel' => [
                'label' => 'Kelas / Rombel',
                'category' => 'Akademik & Sekolah',
                'is_string' => false,
                'resolver' => fn (Siswa $s) => (string) ($s->rombel?->nama ?? '-'),
            ],
            'rayon' => [
                'label' => 'Rayon',
                'category' => 'Akademik & Sekolah',
                'is_string' => false,
                'resolver' => fn (Siswa $s) => (string) ($s->rayon?->nama ?? '-'),
            ],
            'program' => [
                'label' => 'Program',
                'category' => 'Akademik & Sekolah',
                'is_string' => false,
                'resolver' => fn (Siswa $s) => (string) ($s->program?->nama ?? 'Reguler'),
            ],
            'sekolah_asal' => [
                'label' => 'Sekolah Asal',
                'category' => 'Akademik & Sekolah',
                'is_string' => false,
                'resolver' => fn (Siswa $s) => (string) ($s->sekolah_asal ?? ''),
            ],
            'skhun' => [
                'label' => 'SKHUN',
                'category' => 'Akademik & Sekolah',
                'is_string' => true,
                'resolver' => fn (Siswa $s) => (string) ($s->skhun ?? ''),
            ],
            'no_peserta_un' => [
                'label' => 'No. Peserta UN',
                'category' => 'Akademik & Sekolah',
                'is_string' => true,
                'resolver' => fn (Siswa $s) => (string) ($s->no_peserta_un ?? ''),
            ],
            'no_seri_ijazah' => [
                'label' => 'No. Seri Ijazah',
                'category' => 'Akademik & Sekolah',
                'is_string' => true,
                'resolver' => fn (Siswa $s) => (string) ($s->no_seri_ijazah ?? ''),
            ],

            // Kontak & Domisili
            'hp' => [
                'label' => 'No. HP Siswa',
                'category' => 'Kontak & Domisili',
                'is_string' => true,
                'resolver' => fn (Siswa $s) => (string) ($s->hp ?? ''),
            ],
            'telepon' => [
                'label' => 'Telepon Rumah',
                'category' => 'Kontak & Domisili',
                'is_string' => true,
                'resolver' => fn (Siswa $s) => (string) ($s->telepon ?? ''),
            ],
            'email' => [
                'label' => 'Email Siswa',
                'category' => 'Kontak & Domisili',
                'is_string' => false,
                'resolver' => fn (Siswa $s) => (string) ($s->email ?? ''),
            ],
            'alamat' => [
                'label' => 'Alamat Jalan',
                'category' => 'Kontak & Domisili',
                'is_string' => false,
                'resolver' => fn (Siswa $s) => (string) ($s->alamat ?? ''),
            ],
            'rt' => [
                'label' => 'RT',
                'category' => 'Kontak & Domisili',
                'is_string' => true,
                'resolver' => fn (Siswa $s) => (string) ($s->rt ?? ''),
            ],
            'rw' => [
                'label' => 'RW',
                'category' => 'Kontak & Domisili',
                'is_string' => true,
                'resolver' => fn (Siswa $s) => (string) ($s->rw ?? ''),
            ],
            'dusun' => [
                'label' => 'Dusun',
                'category' => 'Kontak & Domisili',
                'is_string' => false,
                'resolver' => fn (Siswa $s) => (string) ($s->dusun ?? ''),
            ],
            'kelurahan' => [
                'label' => 'Kelurahan / Desa',
                'category' => 'Kontak & Domisili',
                'is_string' => false,
                'resolver' => fn (Siswa $s) => (string) ($s->kelurahan ?? ''),
            ],
            'kecamatan' => [
                'label' => 'Kecamatan',
                'category' => 'Kontak & Domisili',
                'is_string' => false,
                'resolver' => fn (Siswa $s) => (string) ($s->kecamatan ?? ''),
            ],
            'kabupaten_kota' => [
                'label' => 'Kabupaten / Kota',
                'category' => 'Kontak & Domisili',
                'is_string' => false,
                'resolver' => fn (Siswa $s) => (string) ($s->kabupaten_kota ?? ''),
            ],
            'provinsi' => [
                'label' => 'Provinsi',
                'category' => 'Kontak & Domisili',
                'is_string' => false,
                'resolver' => fn (Siswa $s) => (string) ($s->provinsi ?? ''),
            ],
            'kode_pos' => [
                'label' => 'Kode Pos',
                'category' => 'Kontak & Domisili',
                'is_string' => true,
                'resolver' => fn (Siswa $s) => (string) ($s->kode_pos ?? ''),
            ],
            'jenis_tinggal' => [
                'label' => 'Jenis Tinggal',
                'category' => 'Kontak & Domisili',
                'is_string' => false,
                'resolver' => fn (Siswa $s) => (string) ($s->jenis_tinggal ?? ''),
            ],
            'alat_transportasi' => [
                'label' => 'Alat Transportasi',
                'category' => 'Kontak & Domisili',
                'is_string' => false,
                'resolver' => fn (Siswa $s) => (string) ($s->alat_transportasi ?? ''),
            ],
            'jarak_ke_sekolah_km' => [
                'label' => 'Jarak ke Sekolah (km)',
                'category' => 'Kontak & Domisili',
                'is_string' => false,
                'resolver' => fn (Siswa $s) => $s->jarak_ke_sekolah_km !== null ? (string) $s->jarak_ke_sekolah_km : '',
            ],

            // Orang Tua & Wali
            'nama_ayah' => [
                'label' => 'Nama Ayah',
                'category' => 'Orang Tua & Wali',
                'is_string' => false,
                'resolver' => fn (Siswa $s) => (string) ($s->ayah?->nama ?? ''),
            ],
            'nik_ayah' => [
                'label' => 'NIK Ayah',
                'category' => 'Orang Tua & Wali',
                'is_string' => true,
                'resolver' => fn (Siswa $s, bool $sensitive) => $sensitive ? (string) ($s->ayah?->nik ?? '') : DataMasker::maskNik($s->ayah?->nik),
            ],
            'tahun_lahir_ayah' => [
                'label' => 'Tahun Lahir Ayah',
                'category' => 'Orang Tua & Wali',
                'is_string' => true,
                'resolver' => fn (Siswa $s) => (string) ($s->ayah?->tahun_lahir ?? ''),
            ],
            'pendidikan_ayah' => [
                'label' => 'Pendidikan Ayah',
                'category' => 'Orang Tua & Wali',
                'is_string' => false,
                'resolver' => fn (Siswa $s) => (string) ($s->ayah?->jenjang_pendidikan ?? ''),
            ],
            'pekerjaan_ayah' => [
                'label' => 'Pekerjaan Ayah',
                'category' => 'Orang Tua & Wali',
                'is_string' => false,
                'resolver' => fn (Siswa $s) => (string) ($s->ayah?->pekerjaan ?? ''),
            ],
            'penghasilan_ayah' => [
                'label' => 'Penghasilan Ayah',
                'category' => 'Orang Tua & Wali',
                'is_string' => false,
                'resolver' => fn (Siswa $s) => (string) ($s->ayah?->penghasilan ?? ''),
            ],
            'hp_ayah' => [
                'label' => 'No. HP Ayah',
                'category' => 'Orang Tua & Wali',
                'is_string' => true,
                'resolver' => fn (Siswa $s) => (string) ($s->ayah?->no_hp ?? ''),
            ],
            'nama_ibu' => [
                'label' => 'Nama Ibu',
                'category' => 'Orang Tua & Wali',
                'is_string' => false,
                'resolver' => fn (Siswa $s) => (string) ($s->ibu?->nama ?? ''),
            ],
            'nik_ibu' => [
                'label' => 'NIK Ibu',
                'category' => 'Orang Tua & Wali',
                'is_string' => true,
                'resolver' => fn (Siswa $s, bool $sensitive) => $sensitive ? (string) ($s->ibu?->nik ?? '') : DataMasker::maskNik($s->ibu?->nik),
            ],
            'tahun_lahir_ibu' => [
                'label' => 'Tahun Lahir Ibu',
                'category' => 'Orang Tua & Wali',
                'is_string' => true,
                'resolver' => fn (Siswa $s) => (string) ($s->ibu?->tahun_lahir ?? ''),
            ],
            'pendidikan_ibu' => [
                'label' => 'Pendidikan Ibu',
                'category' => 'Orang Tua & Wali',
                'is_string' => false,
                'resolver' => fn (Siswa $s) => (string) ($s->ibu?->jenjang_pendidikan ?? ''),
            ],
            'pekerjaan_ibu' => [
                'label' => 'Pekerjaan Ibu',
                'category' => 'Orang Tua & Wali',
                'is_string' => false,
                'resolver' => fn (Siswa $s) => (string) ($s->ibu?->pekerjaan ?? ''),
            ],
            'penghasilan_ibu' => [
                'label' => 'Penghasilan Ibu',
                'category' => 'Orang Tua & Wali',
                'is_string' => false,
                'resolver' => fn (Siswa $s) => (string) ($s->ibu?->penghasilan ?? ''),
            ],
            'hp_ibu' => [
                'label' => 'No. HP Ibu',
                'category' => 'Orang Tua & Wali',
                'is_string' => true,
                'resolver' => fn (Siswa $s) => (string) ($s->ibu?->no_hp ?? ''),
            ],
            'nama_wali' => [
                'label' => 'Nama Wali',
                'category' => 'Orang Tua & Wali',
                'is_string' => false,
                'resolver' => fn (Siswa $s) => (string) ($s->wali?->nama ?? ''),
            ],
            'nik_wali' => [
                'label' => 'NIK Wali',
                'category' => 'Orang Tua & Wali',
                'is_string' => true,
                'resolver' => fn (Siswa $s, bool $sensitive) => $sensitive ? (string) ($s->wali?->nik ?? '') : DataMasker::maskNik($s->wali?->nik),
            ],
            'tahun_lahir_wali' => [
                'label' => 'Tahun Lahir Wali',
                'category' => 'Orang Tua & Wali',
                'is_string' => true,
                'resolver' => fn (Siswa $s) => (string) ($s->wali?->tahun_lahir ?? ''),
            ],
            'pendidikan_wali' => [
                'label' => 'Pendidikan Wali',
                'category' => 'Orang Tua & Wali',
                'is_string' => false,
                'resolver' => fn (Siswa $s) => (string) ($s->wali?->jenjang_pendidikan ?? ''),
            ],
            'pekerjaan_wali' => [
                'label' => 'Pekerjaan Wali',
                'category' => 'Orang Tua & Wali',
                'is_string' => false,
                'resolver' => fn (Siswa $s) => (string) ($s->wali?->pekerjaan ?? ''),
            ],
            'penghasilan_wali' => [
                'label' => 'Penghasilan Wali',
                'category' => 'Orang Tua & Wali',
                'is_string' => false,
                'resolver' => fn (Siswa $s) => (string) ($s->wali?->penghasilan ?? ''),
            ],
            'hp_wali' => [
                'label' => 'No. HP Wali',
                'category' => 'Orang Tua & Wali',
                'is_string' => true,
                'resolver' => fn (Siswa $s) => (string) ($s->wali?->no_hp ?? ''),
            ],

            // Bantuan & Perbankan
            'penerima_kip' => [
                'label' => 'Penerima KIP',
                'category' => 'Bantuan & Perbankan',
                'is_string' => false,
                'resolver' => fn (Siswa $s) => $s->penerima_kip ? 'Ya' : 'Tidak',
            ],
            'nomor_kip' => [
                'label' => 'Nomor KIP',
                'category' => 'Bantuan & Perbankan',
                'is_string' => true,
                'resolver' => fn (Siswa $s) => (string) ($s->nomor_kip ?? ''),
            ],
            'nama_di_kip' => [
                'label' => 'Nama Tertera di KIP',
                'category' => 'Bantuan & Perbankan',
                'is_string' => false,
                'resolver' => fn (Siswa $s) => (string) ($s->nama_di_kip ?? ''),
            ],
            'penerima_kps' => [
                'label' => 'Penerima KPS',
                'category' => 'Bantuan & Perbankan',
                'is_string' => false,
                'resolver' => fn (Siswa $s) => $s->penerima_kps ? 'Ya' : 'Tidak',
            ],
            'no_kps' => [
                'label' => 'Nomor KPS',
                'category' => 'Bantuan & Perbankan',
                'is_string' => true,
                'resolver' => fn (Siswa $s) => (string) ($s->no_kps ?? ''),
            ],
            'nomor_kks' => [
                'label' => 'Nomor KKS',
                'category' => 'Bantuan & Perbankan',
                'is_string' => true,
                'resolver' => fn (Siswa $s) => (string) ($s->nomor_kks ?? ''),
            ],
            'layak_pip' => [
                'label' => 'Layak PIP',
                'category' => 'Bantuan & Perbankan',
                'is_string' => false,
                'resolver' => fn (Siswa $s) => $s->layak_pip ? 'Ya' : 'Tidak',
            ],
            'alasan_layak_pip' => [
                'label' => 'Alasan Layak PIP',
                'category' => 'Bantuan & Perbankan',
                'is_string' => false,
                'resolver' => fn (Siswa $s) => (string) ($s->alasan_layak_pip ?? ''),
            ],
            'bank' => [
                'label' => 'Nama Bank',
                'category' => 'Bantuan & Perbankan',
                'is_string' => false,
                'resolver' => fn (Siswa $s) => (string) ($s->bank ?? ''),
            ],
            'nomor_rekening_bank' => [
                'label' => 'Nomor Rekening Bank',
                'category' => 'Bantuan & Perbankan',
                'is_string' => true,
                'resolver' => fn (Siswa $s) => (string) ($s->nomor_rekening_bank ?? ''),
            ],
            'rekening_atas_nama' => [
                'label' => 'Rekening Atas Nama',
                'category' => 'Bantuan & Perbankan',
                'is_string' => false,
                'resolver' => fn (Siswa $s) => (string) ($s->rekening_atas_nama ?? ''),
            ],

            // Fisik & Khusus
            'berat_badan' => [
                'label' => 'Berat Badan (kg)',
                'category' => 'Fisik & Khusus',
                'is_string' => false,
                'resolver' => fn (Siswa $s) => $s->berat_badan !== null ? (string) $s->berat_badan : '',
            ],
            'tinggi_badan' => [
                'label' => 'Tinggi Badan (cm)',
                'category' => 'Fisik & Khusus',
                'is_string' => false,
                'resolver' => fn (Siswa $s) => $s->tinggi_badan !== null ? (string) $s->tinggi_badan : '',
            ],
            'lingkar_kepala' => [
                'label' => 'Lingkar Kepala (cm)',
                'category' => 'Fisik & Khusus',
                'is_string' => false,
                'resolver' => fn (Siswa $s) => $s->lingkar_kepala !== null ? (string) $s->lingkar_kepala : '',
            ],
            'kebutuhan_khusus' => [
                'label' => 'Kebutuhan Khusus',
                'category' => 'Fisik & Khusus',
                'is_string' => false,
                'resolver' => fn (Siswa $s) => (string) ($s->kebutuhan_khusus ?? 'Tidak Ada'),
            ],

            // Status
            'status' => [
                'label' => 'Status Siswa',
                'category' => 'Status Siswa',
                'is_string' => false,
                'resolver' => fn (Siswa $s) => ucfirst($s->status ?? 'Aktif'),
            ],
        ];
    }

    /**
     * Preset pilihan kolom siap pakai
     */
    public static function getPresets(): array
    {
        return [
            'pokok' => [
                'label' => 'Identitas Pokok',
                'columns' => ['nipd', 'nisn', 'nama', 'jk', 'tempat_lahir', 'tanggal_lahir', 'tingkat', 'rombel', 'rayon', 'status'],
            ],
            'kontak_alamat' => [
                'label' => 'Kontak & Alamat',
                'columns' => ['nipd', 'nama', 'rombel', 'rayon', 'hp', 'telepon', 'email', 'alamat', 'rt', 'rw', 'kelurahan', 'kecamatan', 'kabupaten_kota'],
            ],
            'ortu_wali' => [
                'label' => 'Orang Tua / Wali',
                'columns' => ['nipd', 'nama', 'rombel', 'rayon', 'nama_ayah', 'hp_ayah', 'pekerjaan_ayah', 'nama_ibu', 'hp_ibu', 'pekerjaan_ibu', 'nama_wali', 'hp_wali'],
            ],
            'bantuan_bank' => [
                'label' => 'Bantuan & Rekening Bank',
                'columns' => ['nipd', 'nisn', 'nama', 'rombel', 'penerima_kip', 'nomor_kip', 'penerima_kps', 'layak_pip', 'bank', 'nomor_rekening_bank', 'rekening_atas_nama'],
            ],
            'semua' => [
                'label' => 'Pilih Semua Kolom',
                'columns' => array_keys(self::getAvailableColumns()),
            ],
        ];
    }

    /**
     * Generate file spreadsheet PhpSpreadsheet (.xlsx)
     */
    public function exportXlsx(Collection $siswas, array $selectedKeys): Spreadsheet
    {
        $allCols = self::getAvailableColumns();
        $validKeys = array_values(array_filter($selectedKeys, fn ($k) => isset($allCols[$k])));

        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Siswa Kustom');

        // Header No
        $sheet->setCellValue('A1', 'No');

        // Tulis Header Kolom Terpilih
        foreach ($validKeys as $i => $key) {
            $colLetter = Coordinate::stringFromColumnIndex($i + 2);
            $sheet->setCellValue($colLetter.'1', $allCols[$key]['label']);
        }

        $lastColLetter = Coordinate::stringFromColumnIndex(count($validKeys) + 1);

        // Styling Header
        $sheet->getStyle("A1:{$lastColLetter}1")->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => '1e293b'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'e2e8f0'],
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'cbd5e1'],
                ],
            ],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(26);

        $sensitive = DataMasker::canViewSensitive();
        $row = 2;

        foreach ($siswas as $idx => $s) {
            $sheet->setCellValue('A'.$row, $idx + 1);
            $sheet->getStyle('A'.$row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            foreach ($validKeys as $i => $key) {
                $colLetter = Coordinate::stringFromColumnIndex($i + 2);
                $def = $allCols[$key];
                $val = ($def['resolver'])($s, $sensitive);

                if ($def['is_string']) {
                    $sheet->setCellValueExplicit($colLetter.$row, (string) $val, DataType::TYPE_STRING);
                } else {
                    $sheet->setCellValue($colLetter.$row, $val);
                }
            }

            // Garis pembatas tipis antar data
            $sheet->getStyle("A{$row}:{$lastColLetter}{$row}")->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'e2e8f0'],
                    ],
                ],
            ]);

            $row++;
        }

        // Auto-fit kolom
        $colIndexLimit = count($validKeys) + 1;
        for ($c = 1; $c <= $colIndexLimit; $c++) {
            $letter = Coordinate::stringFromColumnIndex($c);
            $sheet->getColumnDimension($letter)->setAutoSize(true);
        }

        return $spreadsheet;
    }

    /**
     * Generate format CSV string dengan UTF-8 BOM
     */
    public function exportCsv(Collection $siswas, array $selectedKeys): string
    {
        $allCols = self::getAvailableColumns();
        $validKeys = array_values(array_filter($selectedKeys, fn ($k) => isset($allCols[$k])));

        $output = fopen('php://temp', 'r+');

        // Tulis UTF-8 BOM agar Excel dapat membuka CSV langsung tanpa karakter rusak
        fputs($output, "\xEF\xBB\xBF");

        // Header baris
        $headers = array_merge(['No'], array_map(fn ($k) => $allCols[$k]['label'], $validKeys));
        fputcsv($output, $headers);

        $sensitive = DataMasker::canViewSensitive();

        foreach ($siswas as $idx => $s) {
            $row = [$idx + 1];
            foreach ($validKeys as $key) {
                $def = $allCols[$key];
                $val = ($def['resolver'])($s, $sensitive);
                $row[] = $val;
            }
            fputcsv($output, $row);
        }

        rewind($output);
        $csvContent = stream_get_contents($output);
        fclose($output);

        return $csvContent;
    }
}
