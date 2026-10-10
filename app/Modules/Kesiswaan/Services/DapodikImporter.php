<?php

namespace App\Modules\Kesiswaan\Services;

use App\Models\User;
use App\Modules\Akademik\Models\Jurusan;
use App\Modules\Akademik\Models\Rayon;
use App\Modules\Akademik\Models\Rombel;
use App\Modules\Akademik\Models\TahunAjaran;
use App\Modules\Akademik\Support\AcademicContext;
use App\Modules\Kesiswaan\Models\RayonSiswa;
use App\Modules\Kesiswaan\Models\RombelSiswa;
use App\Modules\Kesiswaan\Models\Siswa;
use App\Modules\Kesiswaan\Models\SiswaOrangTua;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class DapodikImporter
{
    protected array $jurusansCache = [];
    protected ?TahunAjaran $tahunAjaran = null;

    public function __construct(?TahunAjaran $ta = null)
    {
        $this->tahunAjaran = $ta ?? AcademicContext::currentTahunAjaran();
        $this->loadJurusans();
    }

    protected function loadJurusans(): void
    {
        $all = Jurusan::all();
        foreach ($all as $j) {
            $this->jurusansCache[strtoupper($j->kode)] = $j;
            foreach ($j->kode_alias ?? [] as $alias) {
                $this->jurusansCache[strtoupper($alias)] = $j;
            }
        }
    }

    /**
     * Memproses file Excel DAPODIK (66 kolom)
     */
    /**
     * Memproses file Excel DAPODIK (66 kolom)
     */
    public function import(string $filePath, ?callable $onProgress = null): array
    {
        set_time_limit(0);
        ini_set('memory_limit', '512M');

        $reader = IOFactory::createReaderForFile($filePath);
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($filePath);
        $sheet = $spreadsheet->getActiveSheet();

        $highestRow = $sheet->getHighestRow();
        $imported = 0;
        $updated = 0;
        $errors = [];
        $hashCache = [];

        // Deteksi kolom 'Rombel' dan 'Rayon' secara dinamis dari baris header 1 dan 2
        $colRombel = null;
        $colRayon = null;

        $highestCol = $sheet->getHighestColumn();
        $highestColIndex = Coordinate::columnIndexFromString($highestCol);

        for ($i = 1; $i <= $highestColIndex; $i++) {
            $colLetter = Coordinate::stringFromColumnIndex($i);
            $h1 = strtoupper(trim((string) $sheet->getCell($colLetter . '1')->getValue()));
            $h2 = strtoupper(trim((string) $sheet->getCell($colLetter . '2')->getValue()));

            if (!$colRombel && (str_contains($h1, 'ROMBEL') || str_contains($h2, 'ROMBEL'))) {
                $colRombel = $colLetter;
            }
            if (!$colRayon && (str_contains($h1, 'RAYON') || str_contains($h2, 'RAYON'))) {
                $colRayon = $colLetter;
            }
        }

        // Fallback default kolom AQ untuk Rombel jika tidak terdeteksi via nama header
        if (!$colRombel && $highestColIndex >= 43) {
            $colRombel = 'AQ';
        }

        for ($r = 3; $r <= $highestRow; $r++) {
            $nama = trim((string) $sheet->getCell('B' . $r)->getValue());
            if ($nama === '') {
                continue; // Lewati baris kosong
            }

            $nisn = trim((string) $sheet->getCell('E' . $r)->getValue());
            if ($nisn === '') {
                $errors[] = ['baris' => $r, 'nama' => $nama, 'pesan' => 'NISN tidak boleh kosong.'];
                continue;
            }

            if ($onProgress) {
                $onProgress($r, $highestRow, $nama, $nisn);
            }

            try {
                DB::transaction(function () use ($sheet, $r, $nama, $nisn, $colRombel, $colRayon, &$imported, &$updated, &$hashCache) {
                    $nipd = trim((string) $sheet->getCell('C' . $r)->getValue());
                    $jk = strtoupper(trim((string) $sheet->getCell('D' . $r)->getValue())) === 'P' ? 'P' : 'L';
                    $tempatLahir = trim((string) $sheet->getCell('F' . $r)->getValue());

                    // Tanggal Lahir
                    $rawTgl = $sheet->getCell('G' . $r)->getValue();
                    $tglLahir = $this->parseDate($rawTgl);

                    $nik = trim((string) $sheet->getCell('H' . $r)->getValue());
                    $agama = trim((string) $sheet->getCell('I' . $r)->getValue());
                    $alamat = trim((string) $sheet->getCell('J' . $r)->getValue());
                    $rt = trim((string) $sheet->getCell('K' . $r)->getValue());
                    $rw = trim((string) $sheet->getCell('L' . $r)->getValue());
                    $dusun = trim((string) $sheet->getCell('M' . $r)->getValue());
                    $kelurahan = trim((string) $sheet->getCell('N' . $r)->getValue());
                    $kecamatan = trim((string) $sheet->getCell('O' . $r)->getValue());
                    $kodePos = trim((string) $sheet->getCell('P' . $r)->getValue());
                    $jenisTinggal = trim((string) $sheet->getCell('Q' . $r)->getValue());
                    $alatTransportasi = trim((string) $sheet->getCell('R' . $r)->getValue());
                    $telepon = trim((string) $sheet->getCell('S' . $r)->getValue());
                    $hp = trim((string) $sheet->getCell('T' . $r)->getValue());
                    $email = trim((string) $sheet->getCell('U' . $r)->getValue());
                    $skhun = trim((string) $sheet->getCell('V' . $r)->getValue());
                    $penerimaKps = strtolower(trim((string) $sheet->getCell('W' . $r)->getValue())) === 'ya';
                    $noKps = trim((string) $sheet->getCell('X' . $r)->getValue());

                    // Rombel Saat Ini (jika ada dan terisi)
                    $rawRombel = $colRombel ? trim((string) $sheet->getCell($colRombel . $r)->getValue()) : '';
                    $rombel = $rawRombel !== '' ? $this->resolveRombel($rawRombel) : null;

                    // Rayon (jika ada kolom Rayon di Excel)
                    $rayon = null;
                    if ($colRayon) {
                        $rawRayon = trim((string) $sheet->getCell($colRayon . $r)->getValue());
                        if ($rawRayon !== '') {
                            $rayon = Rayon::firstOrCreate(['nama' => $rawRayon]);
                        }
                    }

                    // Dokumen & Bantuan
                    $noPesertaUn = trim((string) $sheet->getCell('AR' . $r)->getValue());
                    $noSeriIjazah = trim((string) $sheet->getCell('AS' . $r)->getValue());
                    $penerimaKip = strtolower(trim((string) $sheet->getCell('AT' . $r)->getValue())) === 'ya';
                    $nomorKip = trim((string) $sheet->getCell('AU' . $r)->getValue());
                    $namaDiKip = trim((string) $sheet->getCell('AV' . $r)->getValue());
                    $nomorKks = trim((string) $sheet->getCell('AW' . $r)->getValue());
                    $noAktaLahir = trim((string) $sheet->getCell('AX' . $r)->getValue());
                    $bank = trim((string) $sheet->getCell('AY' . $r)->getValue());
                    $nomorRekeningBank = trim((string) $sheet->getCell('AZ' . $r)->getValue());
                    $rekeningAtasNama = trim((string) $sheet->getCell('BA' . $r)->getValue());
                    $layakPip = strtolower(trim((string) $sheet->getCell('BB' . $r)->getValue())) === 'ya';
                    $alasanLayakPip = trim((string) $sheet->getCell('BC' . $r)->getValue());
                    $kebutuhanKhusus = trim((string) $sheet->getCell('BD' . $r)->getValue());
                    $sekolahAsal = trim((string) $sheet->getCell('BE' . $r)->getValue());
                    $anakKe = is_numeric($sheet->getCell('BF' . $r)->getValue()) ? (int) $sheet->getCell('BF' . $r)->getValue() : null;
                    $lintang = trim((string) $sheet->getCell('BG' . $r)->getValue());
                    $bujur = trim((string) $sheet->getCell('BH' . $r)->getValue());
                    $noKk = trim((string) $sheet->getCell('BI' . $r)->getValue());
                    $bb = is_numeric($sheet->getCell('BJ' . $r)->getValue()) ? (int) $sheet->getCell('BJ' . $r)->getValue() : null;
                    $tb = is_numeric($sheet->getCell('BK' . $r)->getValue()) ? (int) $sheet->getCell('BK' . $r)->getValue() : null;
                    $lk = is_numeric($sheet->getCell('BL' . $r)->getValue()) ? (int) $sheet->getCell('BL' . $r)->getValue() : null;
                    $jmlSaudara = is_numeric($sheet->getCell('BM' . $r)->getValue()) ? (int) $sheet->getCell('BM' . $r)->getValue() : null;
                    $jarakKm = is_numeric($sheet->getCell('BN' . $r)->getValue()) ? (float) $sheet->getCell('BN' . $r)->getValue() : null;

                    // Username Siswa: NIS (NIPD kolom C), jika kosong gunakan NISN
                    $usernameSiswa = $nipd ?: $nisn;

                    // Password default Siswa: DDMMYYYY dari tanggal lahir (misal: 08072006)
                    $defaultPassword = $tglLahir ? Carbon::parse($tglLahir)->format('dmY') : $usernameSiswa;

                    // Program default: Reguler
                    $programDefault = \App\Modules\Kesiswaan\Models\Program::where('kode', 'REG')->first();

                    // Buat Akun Siswa Otomatis
                    $userSiswa = User::where('username', $usernameSiswa)->first();
                    if (!$userSiswa) {
                        if (!isset($hashCache[$defaultPassword])) {
                            $hashCache[$defaultPassword] = Hash::make($defaultPassword);
                        }
                        $userSiswa = User::create([
                            'username' => $usernameSiswa,
                            'name' => $nama,
                            'email' => $email ?: "{$usernameSiswa}@siswa.wikrama.sch.id",
                            'password' => $hashCache[$defaultPassword],
                            'is_active' => true,
                            'must_change_password' => true,
                        ]);
                    }
                    if (!$userSiswa->hasRole('Siswa')) {
                        $userSiswa->assignRole('Siswa');
                    }

                    // Buat Akun Orang Tua Otomatis: ortu_[NIS]
                    $usernameOrtu = 'ortu_' . $usernameSiswa;
                    $userOrtu = User::where('username', $usernameOrtu)->first();
                    if (!$userOrtu) {
                        if (!isset($hashCache[$defaultPassword])) {
                            $hashCache[$defaultPassword] = Hash::make($defaultPassword);
                        }
                        $userOrtu = User::create([
                            'username' => $usernameOrtu,
                            'name' => 'Orang Tua dari ' . $nama,
                            'email' => "{$usernameOrtu}@ortu.wikrama.sch.id",
                            'password' => $hashCache[$defaultPassword],
                            'is_active' => true,
                            'must_change_password' => false,
                        ]);
                    }
                    if (!$userOrtu->hasRole('Orang Tua')) {
                        $userOrtu->assignRole('Orang Tua');
                    }

                    $siswaData = [
                        'user_id' => $userSiswa->id,
                        'rayon_id' => $rayon?->id,
                        'program_id' => $programDefault?->id,
                        'nama' => $nama,
                        'nipd' => $nipd ?: null,
                        'jk' => $jk,
                        'nisn' => $nisn,
                        'tempat_lahir' => $tempatLahir ?: null,
                        'tanggal_lahir' => $tglLahir,
                        'nik' => $nik ?: null,
                        'agama' => $agama ?: null,
                        'alamat' => $alamat ?: null,
                        'rt' => $rt ?: null,
                        'rw' => $rw ?: null,
                        'dusun' => $dusun ?: null,
                        'kelurahan' => $kelurahan ?: null,
                        'kecamatan' => $kecamatan ?: null,
                        'kode_pos' => $kodePos ?: null,
                        'jenis_tinggal' => $jenisTinggal ?: null,
                        'alat_transportasi' => $alatTransportasi ?: null,
                        'telepon' => $telepon ?: null,
                        'hp' => $hp ?: null,
                        'email' => $email ?: null,
                        'skhun' => $skhun ?: null,
                        'penerima_kps' => $penerimaKps,
                        'no_kps' => $noKps ?: null,
                        'no_peserta_un' => $noPesertaUn ?: null,
                        'no_seri_ijazah' => $noSeriIjazah ?: null,
                        'penerima_kip' => $penerimaKip,
                        'nomor_kip' => $nomorKip ?: null,
                        'nama_di_kip' => $namaDiKip ?: null,
                        'nomor_kks' => $nomorKks ?: null,
                        'no_akta_lahir' => $noAktaLahir ?: null,
                        'bank' => $bank ?: null,
                        'nomor_rekening_bank' => $nomorRekeningBank ?: null,
                        'rekening_atas_nama' => $rekeningAtasNama ?: null,
                        'layak_pip' => $layakPip,
                        'alasan_layak_pip' => $alasanLayakPip ?: null,
                        'kebutuhan_khusus' => $kebutuhanKhusus ?: null,
                        'sekolah_asal' => $sekolahAsal ?: null,
                        'anak_ke_berapa' => $anakKe,
                        'lintang' => $lintang ?: null,
                        'bujur' => $bujur ?: null,
                        'no_kk' => $noKk ?: null,
                        'berat_badan' => $bb,
                        'tinggi_badan' => $tb,
                        'lingkar_kepala' => $lk,
                        'jml_saudara_kandung' => $jmlSaudara,
                        'jarak_ke_sekolah_km' => $jarakKm,
                        'status' => 'aktif',
                    ];

                    $existing = Siswa::where('nisn', $nisn)->first();
                    if ($existing) {
                        // Jika di Excel rombel terisi, update rombel_id.
                        // Jika di Excel rombel kosong, pertahankan rombel_id lama agar tidak terhapus.
                        if ($rombel) {
                            $siswaData['rombel_id'] = $rombel->id;
                        } else {
                            $siswaData['rombel_id'] = $existing->rombel_id;
                        }
                        $existing->update($siswaData);
                        $siswa = $existing;
                        $updated++;
                    } else {
                        // Siswa baru: jika rombel terisi gunakan id-nya, jika kosong biarkan NULL (siap untuk Plotting Rombel)
                        $siswaData['rombel_id'] = $rombel?->id;
                        $siswa = Siswa::create($siswaData);
                        $imported++;
                    }

                    // Rekam Rombel Siswa jika ada rombel
                    if ($rombel && $this->tahunAjaran) {
                        RombelSiswa::updateOrCreate(
                            ['tahun_ajaran_id' => $this->tahunAjaran->id, 'siswa_id' => $siswa->id],
                            ['rombel_id' => $rombel->id]
                        );
                    }

                    // Rekam Rayon Siswa jika ada rayon
                    if ($rayon) {
                        RayonSiswa::updateOrCreate(
                            ['siswa_id' => $siswa->id, 'rayon_id' => $rayon->id],
                            ['tgl_mulai' => now()]
                        );
                    }

                    // Simpan Data Orang Tua (Ayah, Ibu, Wali)
                    $this->saveOrangTua($siswa, 'ayah', [
                        'nama' => trim((string) $sheet->getCell('Y' . $r)->getValue()),
                        'tahun_lahir' => is_numeric($sheet->getCell('Z' . $r)->getValue()) ? (int) $sheet->getCell('Z' . $r)->getValue() : null,
                        'jenjang_pendidikan' => trim((string) $sheet->getCell('AA' . $r)->getValue()) ?: null,
                        'pekerjaan' => trim((string) $sheet->getCell('AB' . $r)->getValue()) ?: null,
                        'penghasilan' => trim((string) $sheet->getCell('AC' . $r)->getValue()) ?: null,
                        'nik' => trim((string) $sheet->getCell('AD' . $r)->getValue()) ?: null,
                    ]);

                    $this->saveOrangTua($siswa, 'ibu', [
                        'nama' => trim((string) $sheet->getCell('AE' . $r)->getValue()),
                        'tahun_lahir' => is_numeric($sheet->getCell('AF' . $r)->getValue()) ? (int) $sheet->getCell('AF' . $r)->getValue() : null,
                        'jenjang_pendidikan' => trim((string) $sheet->getCell('AG' . $r)->getValue()) ?: null,
                        'pekerjaan' => trim((string) $sheet->getCell('AH' . $r)->getValue()) ?: null,
                        'penghasilan' => trim((string) $sheet->getCell('AI' . $r)->getValue()) ?: null,
                        'nik' => trim((string) $sheet->getCell('AJ' . $r)->getValue()) ?: null,
                    ]);

                    $this->saveOrangTua($siswa, 'wali', [
                        'nama' => trim((string) $sheet->getCell('AK' . $r)->getValue()),
                        'tahun_lahir' => is_numeric($sheet->getCell('AL' . $r)->getValue()) ? (int) $sheet->getCell('AL' . $r)->getValue() : null,
                        'jenjang_pendidikan' => trim((string) $sheet->getCell('AM' . $r)->getValue()) ?: null,
                        'pekerjaan' => trim((string) $sheet->getCell('AN' . $r)->getValue()) ?: null,
                        'penghasilan' => trim((string) $sheet->getCell('AO' . $r)->getValue()) ?: null,
                        'nik' => trim((string) $sheet->getCell('AP' . $r)->getValue()) ?: null,
                    ]);
                });
            } catch (\Throwable $e) {
                $errors[] = [
                    'baris' => $r,
                    'nama' => $nama,
                    'pesan' => $e->getMessage(),
                ];
            }
        }

        return [
            'total_baris' => $highestRow - 2,
            'imported' => $imported,
            'updated' => $updated,
            'errors' => $errors,
        ];
    }

    /**
     * Memecah string 'Rombel Saat Ini' (misal: 'TJKT XI', 'PPLG X', 'BDP XII')
     * dan mengembalikannya ke Rombel nomor 1 standar '[Tingkat] [Jurusan] 1'
     */
    public function resolveRombel(?string $raw): ?Rombel
    {
        if (empty($raw)) {
            return null;
        }

        $parts = preg_split('/\s+/', trim($raw));
        if (count($parts) < 2) {
            return null;
        }

        $tingkat = null;
        $kodeJurusan = null;

        foreach ($parts as $p) {
            $upper = strtoupper($p);
            if (in_array($upper, ['X', 'XI', 'XII'])) {
                $tingkat = $upper;
            } elseif (isset($this->jurusansCache[$upper])) {
                $kodeJurusan = $this->jurusansCache[$upper]->kode;
            }
        }

        if (!$tingkat || !$kodeJurusan) {
            if ($this->tahunAjaran) {
                $exact = Rombel::where('tahun_ajaran_id', $this->tahunAjaran->id)
                    ->where('nama', trim($raw))
                    ->first();
                if ($exact) {
                    return $exact;
                }
            }
            return null;
        }

        $jurusan = Jurusan::where('kode', $kodeJurusan)->first();
        if (!$jurusan || !$this->tahunAjaran) {
            return null;
        }

        // Cari atau buat Rombel nomor urut 1 (misal: 'X PPLG 1', 'XI TJKT 1')
        $namaRombel = Rombel::generateNama($tingkat, $jurusan->kode, 1);

        return Rombel::firstOrCreate(
            [
                'tahun_ajaran_id' => $this->tahunAjaran->id,
                'jurusan_id' => $jurusan->id,
                'tingkat' => $tingkat,
                'nomor_urut' => 1,
            ],
            [
                'nama' => $namaRombel,
                'kapasitas' => 36,
            ]
        );
    }

    protected function saveOrangTua(Siswa $siswa, string $hubungan, array $data): void
    {
        if (empty($data['nama'])) {
            return;
        }

        SiswaOrangTua::updateOrCreate(
            ['siswa_id' => $siswa->id, 'hubungan' => $hubungan],
            $data
        );
    }

    protected function parseDate($value): ?string
    {
        if (empty($value)) {
            return null;
        }

        try {
            if (is_numeric($value)) {
                return Carbon::instance(ExcelDate::excelToDateTimeObject($value))->format('Y-m-d');
            }

            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * Membersihkan seluruh data siswa, relasi kelas/rayon/ortu/nilai,
     * dan akun user bertipe Siswa & Orang Tua.
     * Master data (Rombel, Rayon, Jurusan, dll) dan Akun Admin/Guru/Staff TIDAK disentuh.
     */
    public static function cleanSiswaData(): array
    {
        Schema::disableForeignKeyConstraints();

        $countSiswa = Siswa::count();

        // Truncate tabel relasi yang bergantung pada siswa jika ada
        $tablesToTruncate = [
            'absensi_mapels',
            'absensi_harians',
            'catatan_wali_kelas',
            'nilai_sumatif',
            'nilai_formatif',
            'nilai_akhir',
            'ekstrakurikuler_anggotas',
            'rayon_siswa',
            'rombel_siswa',
            'siswa_orang_tuas',
            'siswas',
        ];

        foreach ($tablesToTruncate as $tbl) {
            if (Schema::hasTable($tbl)) {
                DB::table($tbl)->truncate();
            }
        }

        // Hapus akun user dengan role Siswa & Orang Tua beserta relasi role-nya
        $siswaUserIds = DB::table('model_has_roles')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->whereIn('roles.name', ['Siswa', 'Orang Tua'])
            ->where('model_has_roles.model_type', User::class)
            ->pluck('model_has_roles.model_id')
            ->unique()
            ->toArray();

        $countUsers = count($siswaUserIds);

        if (!empty($siswaUserIds)) {
            DB::table('model_has_roles')
                ->where('model_type', User::class)
                ->whereIn('model_id', $siswaUserIds)
                ->delete();

            DB::table('users')->whereIn('id', $siswaUserIds)->delete();
        }

        Schema::enableForeignKeyConstraints();

        return [
            'siswas' => $countSiswa,
            'users' => $countUsers,
        ];
    }
}
