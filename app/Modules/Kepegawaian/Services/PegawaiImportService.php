<?php

namespace App\Modules\Kepegawaian\Services;

use App\Models\User;
use App\Modules\Kepegawaian\Models\Pegawai;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class PegawaiImportService
{
    /**
     * Parsing dan normalisasi tanggal dari berbagai format di Excel/CSV:
     * - Excel numeric serial (e.g. 29541)
     * - Teks bahasa Inggris (e.g. "1 January 1998", "10 May 1967")
     * - Teks bahasa Indonesia (e.g. "21 Pebruari 2022", "3 Juli 2016")
     * - Format US (e.g. "03/24/1997", "07/17/2023")
     * - Format ISO (e.g. "2018-07-16")
     */
    public static function parseDate($value): ?string
    {
        if ($value === null || $value === '' || $value === '-' || $value === '0') {
            return null;
        }

        // 1. Jika serial number Excel
        if (is_numeric($value)) {
            $num = (float) $value;
            if ($num > 1000) {
                try {
                    return ExcelDate::excelToDateTimeObject($num)->format('Y-m-d');
                } catch (\Throwable) {
                    // Fallthrough
                }
            }
        }

        $str = trim((string) $value);
        if ($str === '' || $str === '-' || $str === '0') {
            return null;
        }

        // 2. Format ISO YYYY-MM-DD
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $str)) {
            return $str;
        }

        // 3. Format MM/DD/YYYY atau DD/MM/YYYY
        if (preg_match('/^(\d{1,2})\/(\d{1,2})\/(\d{4})$/', $str, $matches)) {
            $part1 = (int) $matches[1];
            $part2 = (int) $matches[2];
            $year = (int) $matches[3];

            // Jika part1 > 12, maka DD/MM/YYYY
            if ($part1 > 12) {
                return sprintf('%04d-%02d-%02d', $year, $part2, $part1);
            }
            // Asumsi standar US MM/DD/YYYY
            return sprintf('%04d-%02d-%02d', $year, $part1, $part2);
        }

        // 4. Normalisasi bulan Indonesia ke Inggris
        $bulanMap = [
            'januari' => 'January',
            'februari' => 'February',
            'pebruari' => 'February',
            'maret' => 'March',
            'april' => 'April',
            'mei' => 'May',
            'juni' => 'June',
            'juli' => 'July',
            'agustus' => 'August',
            'september' => 'September',
            'oktober' => 'October',
            'nopember' => 'November',
            'november' => 'November',
            'desember' => 'December',
        ];
        $strLower = strtolower($str);
        foreach ($bulanMap as $id => $en) {
            if (str_contains($strLower, $id)) {
                $str = str_ireplace($id, $en, $str);
                break;
            }
        }

        try {
            return Carbon::parse($str)->format('Y-m-d');
        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * Membersihkan teks angka (NIK, NUPTK, dsb) agar tidak tersimpan sebagai notasi ilmiah string.
     */
    public static function cleanNumericString($value): ?string
    {
        if ($value === null || $value === '' || $value === '-' || $value === '0' || $value === 0 || $value === 0.0) {
            return null;
        }

        $trimmed = trim((string) $value);
        if ($trimmed === '' || $trimmed === '-' || $trimmed === '0') {
            return null;
        }

        if (is_numeric($value)) {
            // Format angka float besar menjadi string angka tanpa notasi ilmiah
            $formatted = number_format((float) $value, 0, '', '');
            return ($formatted === '0' || $formatted === '') ? null : $formatted;
        }

        // Jika string mengandung notasi ilmiah seperti 3.20612010198e+15
        if (preg_match('/^[0-9.]+[eE]\+[0-9]+$/', $trimmed)) {
            $formatted = number_format((float) $trimmed, 0, '', '');
            return ($formatted === '0' || $formatted === '') ? null : $formatted;
        }

        return $trimmed;
    }

    /**
     * Membaca file Excel atau CSV dan mengembalikan array data siap simpan.
     */
    public function parseFile(string $filePath): array
    {
        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet();
        $highestRow = $sheet->getHighestRow();

        $rows = [];

        for ($r = 2; $r <= $highestRow; $r++) {
            $nama = trim((string) $sheet->getCell([3, $r])->getValue());
            if ($nama === '') {
                continue;
            }

            $noPegawai = $sheet->getCell([1, $r])->getValue();
            $niy = trim((string) $sheet->getCell([2, $r])->getValue());
            if ($niy === '' || $niy === '-') {
                $niy = null;
            }

            $jkRaw = strtoupper(trim((string) $sheet->getCell([4, $r])->getValue()));
            $jenisKelamin = ($jkRaw === 'P') ? 'P' : 'L';

            $nuptk = self::cleanNumericString($sheet->getCell([5, $r])->getValue());
            $nip = trim((string) $sheet->getCell([6, $r])->getValue());
            if ($nip === '' || $nip === '-') {
                $nip = null;
            }

            $noUkg = self::cleanNumericString($sheet->getCell([7, $r])->getValue());
            $noRegGuru = self::cleanNumericString($sheet->getCell([8, $r])->getValue());
            $noPesertaSertifikasi = self::cleanNumericString($sheet->getCell([9, $r])->getValue());
            $nik = self::cleanNumericString($sheet->getCell([10, $r])->getValue());
            $noSertifikatPendidik = self::cleanNumericString($sheet->getCell([11, $r])->getValue());
            $noBpjsKes = self::cleanNumericString($sheet->getCell([12, $r])->getValue());
            $noBpjsTk = self::cleanNumericString($sheet->getCell([13, $r])->getValue());

            $statusRaw = strtoupper(trim((string) $sheet->getCell([15, $r])->getValue()));
            $jenisKepegawaian = trim((string) $sheet->getCell([16, $r])->getValue());

            // Tentukan status kepegawaian & jenis gtk
            $statusKepegawaian = strtolower($statusRaw ?: 'gty');
            $jenisGtk = 'Guru';
            if (in_array($statusRaw, ['STY', 'STT'])) {
                $jenisGtk = 'Tendik';
            } elseif ($statusRaw === 'PTT') {
                $mapelTmp = trim((string) $sheet->getCell([45, $r])->getValue());
                $jenisGtk = ($mapelTmp !== '' && $mapelTmp !== '-') ? 'Guru' : 'Tendik';
            }

            // Anomali keputusan grill-me:
            // Ardi Yansah (STT), Intan Mutiara Tsani (GTT), Ridwan Abdul Latif (GTT)
            if (str_contains($nama, 'Ardi Yansah')) {
                $statusKepegawaian = 'stt';
                $jenisGtk = 'Tendik';
            } elseif (str_contains($nama, 'Intan Mutiara Tsani')) {
                $statusKepegawaian = 'gtt';
                $jenisGtk = 'Guru';
            } elseif (str_contains($nama, 'Ridwan Abdul Latif')) {
                $statusKepegawaian = 'gtt';
                $jenisGtk = 'Guru';
            }

            $tempatLahir = trim((string) $sheet->getCell([17, $r])->getValue()) ?: null;
            $tanggalLahir = self::parseDate($sheet->getCell([18, $r])->getValue());

            $tmtAwalGuru = self::parseDate($sheet->getCell([19, $r])->getValue());
            $noSkMulaiGuru = trim((string) $sheet->getCell([20, $r])->getValue()) ?: null;

            $tglLulusPend = self::parseDate($sheet->getCell([21, $r])->getValue());
            $tahunLulusPend = trim((string) $sheet->getCell([22, $r])->getValue()) ?: null;

            $tglPertamaKontrak = self::parseDate($sheet->getCell([23, $r])->getValue());
            $noSkKontrakPertama = trim((string) $sheet->getCell([24, $r])->getValue()) ?: null;

            $tglDiangkatTetap = self::parseDate($sheet->getCell([25, $r])->getValue());
            $noSkTetap = trim((string) $sheet->getCell([26, $r])->getValue()) ?: null;

            $email = trim((string) $sheet->getCell([27, $r])->getValue());
            if ($email === '' || $email === '-' || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $email = null;
            }

            $golDarah = trim((string) $sheet->getCell([28, $r])->getValue());
            if ($golDarah === '' || $golDarah === '-' || strtolower($golDarah) === 'tidak tahu') {
                $golDarah = null;
            }

            $npwp = self::cleanNumericString($sheet->getCell([29, $r])->getValue());
            $namaWajibPajak = trim((string) $sheet->getCell([30, $r])->getValue()) ?: null;
            $tglAwalMasuk = self::parseDate($sheet->getCell([31, $r])->getValue());

            $alamat = trim((string) $sheet->getCell([34, $r])->getValue()) ?: null;
            $jurusanInstansi = trim((string) $sheet->getCell([35, $r])->getValue()) ?: null;
            $jenjangPend = trim((string) $sheet->getCell([36, $r])->getValue()) ?: null;

            // Pengalaman Kerja (Kolom 37-43)
            $pengalamanKerja = [];
            for ($colP = 37; $colP <= 43; $colP++) {
                $exp = trim((string) $sheet->getCell([$colP, $r])->getValue());
                if ($exp !== '' && $exp !== '-') {
                    $pengalamanKerja[] = $exp;
                }
            }

            $mapelDiampu = trim((string) $sheet->getCell([45, $r])->getValue());
            if ($mapelDiampu === '' || $mapelDiampu === '-') {
                $mapelDiampu = null;
            }

            $jabatanStruktural = trim((string) $sheet->getCell([46, $r])->getValue()) ?: null;
            $lamaKontrak = trim((string) $sheet->getCell([47, $r])->getValue()) ?: null;
            $keteranganPerpanjang = trim((string) $sheet->getCell([48, $r])->getValue()) ?: null;
            $jamKerja = trim((string) $sheet->getCell([49, $r])->getValue()) ?: null;

            $statusPernikahan = trim((string) $sheet->getCell([50, $r])->getValue()) ?: 'Belum Kawin';
            $namaPasangan = trim((string) $sheet->getCell([51, $r])->getValue()) ?: null;
            $tglLahirPasangan = self::parseDate($sheet->getCell([52, $r])->getValue());

            $jumlahAnak = (int) ($sheet->getCell([53, $r])->getValue() ?: 0);

            // Data Anak (Kolom 54-61)
            $dataAnak = [];
            for ($a = 1; $a <= 4; $a++) {
                $colNamaAnak = 54 + (($a - 1) * 2);
                $colTglAnak = 55 + (($a - 1) * 2);
                $namaAnak = trim((string) $sheet->getCell([$colNamaAnak, $r])->getValue());
                $tglAnak = self::parseDate($sheet->getCell([$colTglAnak, $r])->getValue());
                if ($namaAnak !== '' && $namaAnak !== '-') {
                    $dataAnak[] = [
                        'nama' => $namaAnak,
                        'tanggal_lahir' => $tglAnak,
                    ];
                }
            }

            $namaAyah = trim((string) $sheet->getCell([62, $r])->getValue());
            if ($namaAyah === '' || $namaAyah === '-') {
                $namaAyah = null;
            }
            $namaIbu = trim((string) $sheet->getCell([63, $r])->getValue());
            if ($namaIbu === '' || $namaIbu === '-') {
                $namaIbu = null;
            }

            $noHp = trim((string) $sheet->getCell([64, $r])->getValue()) ?: null;

            // Kolom 66: Habis Kontrak
            // Jika terisi teks NIY (misal PRW2107170386), jangan disimpan (set NULL) sesuai hasil grill-me
            $habisKontrakRaw = trim((string) $sheet->getCell([66, $r])->getValue());
            $habisKontrak = null;
            if ($habisKontrakRaw !== '' && ! str_starts_with($habisKontrakRaw, 'PRW')) {
                $habisKontrak = self::parseDate($habisKontrakRaw);
            }

            // Tautkan otomatis ke User yang sudah ada jika email atau nama cocok persis
            $userId = null;
            if ($email) {
                $userId = User::where('email', $email)->value('id');
            }
            if (! $userId) {
                $cleanNama = trim((string) preg_replace('/,.*$/', '', $nama));
                $userId = User::where('name', $nama)
                    ->orWhere('name', $cleanNama)
                    ->value('id');
            }

            $rows[] = [
                'user_id' => $userId,
                'niy' => $niy,
                'nip' => $nip,
                'nuptk' => $nuptk,
                'nik' => $nik,
                'nama_lengkap' => $nama,
                'jenis_kelamin' => $jenisKelamin,
                'jenis_gtk' => $jenisGtk,
                'status_kepegawaian' => $statusKepegawaian,
                'tempat_lahir' => $tempatLahir,
                'tanggal_lahir' => $tanggalLahir,
                'no_hp' => $noHp,
                'email' => $email,
                'alamat' => $alamat,
                'jabatan_struktural' => $jabatanStruktural,
                'mapel_diampu' => $mapelDiampu,
                'nomor_peserta_ukg' => $noUkg,
                'nomor_registrasi_guru' => $noRegGuru,
                'nomor_peserta_sertifikasi' => $noPesertaSertifikasi,
                'nomor_sertifikat_pendidik' => $noSertifikatPendidik,
                'tmt_awal_guru' => $tmtAwalGuru,
                'no_sk_mulai_guru' => $noSkMulaiGuru,
                'tgl_pertama_kontrak' => $tglPertamaKontrak,
                'no_sk_kontrak_pertama' => $noSkKontrakPertama,
                'tgl_diangkat_pegawai_tetap' => $tglDiangkatTetap,
                'no_sk_tetap_yayasan' => $noSkTetap,
                'habis_kontrak' => $habisKontrak,
                'lama_kontrak_selanjutnya' => $lamaKontrak,
                'keterangan_perpanjang' => $keteranganPerpanjang,
                'jam_kerja' => $jamKerja,
                'tgl_awal_masuk_wikrama' => $tglAwalMasuk,
                'golongan_darah' => $golDarah,
                'npwp' => $npwp,
                'nama_wajib_pajak' => $namaWajibPajak,
                'no_kartu_bpjs_kesehatan' => $noBpjsKes,
                'no_kartu_bpjs_ketenagakerjaan' => $noBpjsTk,
                'pendidikan_terakhir' => $jurusanInstansi,
                'jenjang_pendidikan' => $jenjangPend,
                'jurusan_dan_instansi' => $jurusanInstansi,
                'tgl_lulus_pendidikan_terakhir' => $tglLulusPend,
                'tahun_lulus_pendidikan_terakhir' => $tahunLulusPend,
                'pengalaman_kerja' => ! empty($pengalamanKerja) ? $pengalamanKerja : null,
                'status_pernikahan' => $statusPernikahan,
                'nama_pasangan' => $namaPasangan,
                'tgl_lahir_pasangan' => $tglLahirPasangan,
                'jumlah_anak' => $jumlahAnak,
                'data_anak' => ! empty($dataAnak) ? $dataAnak : null,
                'nama_ayah' => $namaAyah,
                'nama_ibu' => $namaIbu,
                'is_active' => true,
            ];
        }

        return $rows;
    }

    /**
     * Eksekusi pembersihan data awal dan import batch ke database.
     */
    public function importFresh(string $filePath): int
    {
        $data = $this->parseFile($filePath);

        return DB::transaction(function () use ($data) {
            // Bersihkan data pegawais lama sesuai hasil grill-me
            Pegawai::query()->delete();

            $inserted = 0;
            foreach ($data as $item) {
                Pegawai::create($item);
                $inserted++;
            }

            return $inserted;
        });
    }

    /**
     * Import bertahap (upsert / append) untuk upload file baru via UI.
     */
    public function importAppend(string $filePath): array
    {
        $data = $this->parseFile($filePath);

        $inserted = 0;
        $updated = 0;

        DB::transaction(function () use ($data, &$inserted, &$updated) {
            foreach ($data as $item) {
                $existing = null;
                if (! empty($item['niy'])) {
                    $existing = Pegawai::where('niy', $item['niy'])->first();
                }
                if (! $existing && ! empty($item['nik'])) {
                    $existing = Pegawai::where('nik', $item['nik'])->first();
                }
                if (! $existing && ! empty($item['nama_lengkap'])) {
                    $existing = Pegawai::where('nama_lengkap', $item['nama_lengkap'])->first();
                }

                if ($existing) {
                    if ($existing->user_id && empty($item['user_id'])) {
                        unset($item['user_id']);
                    }
                    $existing->update($item);
                    $updated++;
                } else {
                    Pegawai::create($item);
                    $inserted++;
                }
            }
        });

        return ['inserted' => $inserted, 'updated' => $updated];
    }
}
