<?php

namespace App\Modules\Kesiswaan\Livewire;

use App\Core\Concerns\GuardsPermission;
use App\Models\User;
use App\Modules\Akademik\Models\Rayon;
use App\Modules\Akademik\Models\Rombel;
use App\Modules\Kesiswaan\Models\Program;
use App\Modules\Kesiswaan\Models\RayonSiswa;
use App\Modules\Kesiswaan\Models\Siswa;
use App\Modules\Kesiswaan\Models\SiswaOrangTua;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('components.layouts.app')]
#[Title('Tambah Data Siswa Baru')]
class SiswaCreate extends Component
{
    use GuardsPermission, WithFileUploads;

    public string $activeTab = 'identitas';

    // Foto Siswa
    public $foto = null;

    // 1. Identitas & Akademik
    public string $nama = '';
    public ?string $nipd = null; // NIS
    public string $jk = 'L';
    public string $nisn = '';
    public ?string $tempat_lahir = null;
    public ?string $tanggal_lahir = null;
    public ?string $nik = null;
    public ?string $agama = null;
    public ?int $rombel_id = null;
    public ?int $rayon_id = null;
    public ?int $program_id = null;
    public string $status = 'aktif';
    public bool $is_active = true;

    // 2. Alamat & Domisili
    public ?string $alamat = null;
    public ?string $rt = null;
    public ?string $rw = null;
    public ?string $dusun = null;
    public ?string $kelurahan = null;
    public ?string $kecamatan = null;
    public ?string $kode_pos = null;
    public ?string $jenis_tinggal = null;
    public ?string $alat_transportasi = null;
    public ?string $lintang = null;
    public ?string $bujur = null;
    public ?float $jarak_ke_sekolah_km = null;

    // 3. Orang Tua & Wali
    // Ayah
    public ?string $ayah_nama = null;
    public ?int $ayah_tahun_lahir = null;
    public ?string $ayah_jenjang_pendidikan = null;
    public ?string $ayah_pekerjaan = null;
    public ?string $ayah_penghasilan = null;
    public ?string $ayah_nik = null;

    // Ibu
    public ?string $ibu_nama = null;
    public ?int $ibu_tahun_lahir = null;
    public ?string $ibu_jenjang_pendidikan = null;
    public ?string $ibu_pekerjaan = null;
    public ?string $ibu_penghasilan = null;
    public ?string $ibu_nik = null;

    // Wali
    public ?string $wali_nama = null;
    public ?int $wali_tahun_lahir = null;
    public ?string $wali_jenjang_pendidikan = null;
    public ?string $wali_pekerjaan = null;
    public ?string $wali_penghasilan = null;
    public ?string $wali_nik = null;

    // 4. Bantuan, Rekening & Dokumen
    public ?string $no_kk = null;
    public ?string $no_akta_lahir = null;
    public ?string $skhun = null;
    public ?string $no_peserta_un = null;
    public ?string $no_seri_ijazah = null;
    public ?string $sekolah_asal = null;
    public bool $penerima_kps = false;
    public ?string $no_kps = null;
    public bool $penerima_kip = false;
    public ?string $nomor_kip = null;
    public ?string $nama_di_kip = null;
    public ?string $nomor_kks = null;
    public bool $layak_pip = false;
    public ?string $alasan_layak_pip = null;
    public ?string $bank = null;
    public ?string $nomor_rekening_bank = null;
    public ?string $rekening_atas_nama = null;

    // 5. Fisik, Periodik & Kontak
    public ?string $kebutuhan_khusus = null;
    public ?int $anak_ke_berapa = null;
    public ?int $berat_badan = null;
    public ?int $tinggi_badan = null;
    public ?int $lingkar_kepala = null;
    public ?int $jml_saudara_kandung = null;
    public ?string $telepon = null;
    public ?string $hp = null;
    public ?string $email = null;

    public function mount(): void
    {
        $this->guard('kesiswaan.siswa.tambah');

        // Set default Program ke Reguler jika tersedia
        $reguler = Program::where('kode', 'REG')->first();
        if ($reguler) {
            $this->program_id = $reguler->id;
        }
    }

    protected function rules(): array
    {
        return [
            'nama' => ['required', 'string', 'max:150'],
            'nipd' => ['nullable', 'string', 'max:30'],
            'jk' => ['required', 'in:L,P'],
            'nisn' => ['required', 'string', 'max:20', Rule::unique('siswas', 'nisn')],
            'tempat_lahir' => ['nullable', 'string', 'max:100'],
            'tanggal_lahir' => ['nullable', 'date'],
            'nik' => ['nullable', 'string', 'max:20'],
            'agama' => ['nullable', 'string', 'max:30'],
            'rombel_id' => ['nullable', 'exists:rombels,id'],
            'rayon_id' => ['nullable', 'exists:rayons,id'],
            'program_id' => ['nullable', 'exists:programs,id'],
            'status' => ['required', 'in:aktif,lulus,pindah,dikeluarkan,cuti'],
            'is_active' => ['required', 'boolean'],
            'foto' => ['nullable', 'image', 'max:2048'], // Max 2MB
        ];
    }

    protected $messages = [
        'nama.required' => 'Nama lengkap siswa wajib diisi.',
        'nisn.required' => 'NISN wajib diisi.',
        'nisn.unique' => 'NISN ini sudah terdaftar pada siswa lain.',
        'jk.required' => 'Pilih jenis kelamin siswa.',
    ];

    public function save(bool $redirect = true)
    {
        $this->guard('kesiswaan.siswa.tambah');
        $this->validate();

        $siswa = DB::transaction(function () {
            // 1. Simpan Foto jika ada
            $fotoPath = null;
            if ($this->foto) {
                $fotoPath = $this->foto->store('photos/siswa', 'public');
            }

            // 2. Akun Login Siswa & Ortu
            $usernameSiswa = $this->nipd ?: $this->nisn;
            $defaultPassword = $this->tanggal_lahir
                ? Carbon::parse($this->tanggal_lahir)->format('dmY')
                : $usernameSiswa;

            // Akun User Siswa
            $userSiswa = User::firstOrCreate(
                ['username' => $usernameSiswa],
                [
                    'name' => $this->nama,
                    'email' => $this->email ?: "{$usernameSiswa}@siswa.wikrama.sch.id",
                    'password' => Hash::make($defaultPassword),
                    'is_active' => true,
                    'must_change_password' => true,
                ]
            );
            if (!$userSiswa->hasRole('Siswa')) {
                $userSiswa->assignRole('Siswa');
            }

            // Akun User Orang Tua
            $usernameOrtu = 'ortu_' . $usernameSiswa;
            $userOrtu = User::firstOrCreate(
                ['username' => $usernameOrtu],
                [
                    'name' => 'Orang Tua dari ' . $this->nama,
                    'email' => "{$usernameOrtu}@ortu.wikrama.sch.id",
                    'password' => Hash::make($defaultPassword),
                    'is_active' => true,
                    'must_change_password' => false,
                ]
            );
            if (!$userOrtu->hasRole('Orang Tua')) {
                $userOrtu->assignRole('Orang Tua');
            }

            // 3. Simpan Profil Siswa
            $siswa = Siswa::create([
                'user_id' => $userSiswa->id,
                'foto_path' => $fotoPath,
                'nama' => $this->nama,
                'nipd' => $this->nipd ?: null,
                'jk' => $this->jk,
                'nisn' => $this->nisn,
                'tempat_lahir' => $this->tempat_lahir ?: null,
                'tanggal_lahir' => $this->tanggal_lahir ?: null,
                'nik' => $this->nik ?: null,
                'agama' => $this->agama ?: null,
                'rombel_id' => $this->rombel_id,
                'rayon_id' => $this->rayon_id,
                'program_id' => $this->program_id,
                'status' => $this->status,
                'is_active' => $this->is_active,

                'alamat' => $this->alamat ?: null,
                'rt' => $this->rt ?: null,
                'rw' => $this->rw ?: null,
                'dusun' => $this->dusun ?: null,
                'kelurahan' => $this->kelurahan ?: null,
                'kecamatan' => $this->kecamatan ?: null,
                'kode_pos' => $this->kode_pos ?: null,
                'jenis_tinggal' => $this->jenis_tinggal ?: null,
                'alat_transportasi' => $this->alat_transportasi ?: null,
                'lintang' => $this->lintang ?: null,
                'bujur' => $this->bujur ?: null,
                'jarak_ke_sekolah_km' => $this->jarak_ke_sekolah_km,

                'no_kk' => $this->no_kk ?: null,
                'no_akta_lahir' => $this->no_akta_lahir ?: null,
                'skhun' => $this->skhun ?: null,
                'no_peserta_un' => $this->no_peserta_un ?: null,
                'no_seri_ijazah' => $this->no_seri_ijazah ?: null,
                'sekolah_asal' => $this->sekolah_asal ?: null,
                'penerima_kps' => $this->penerima_kps,
                'no_kps' => $this->no_kps ?: null,
                'penerima_kip' => $this->penerima_kip,
                'nomor_kip' => $this->nomor_kip ?: null,
                'nama_di_kip' => $this->nama_di_kip ?: null,
                'nomor_kks' => $this->nomor_kks ?: null,
                'layak_pip' => $this->layak_pip,
                'alasan_layak_pip' => $this->alasan_layak_pip ?: null,
                'bank' => $this->bank ?: null,
                'nomor_rekening_bank' => $this->nomor_rekening_bank ?: null,
                'rekening_atas_nama' => $this->rekening_atas_nama ?: null,

                'kebutuhan_khusus' => $this->kebutuhan_khusus ?: null,
                'anak_ke_berapa' => $this->anak_ke_berapa,
                'berat_badan' => $this->berat_badan,
                'tinggi_badan' => $this->tinggi_badan,
                'lingkar_kepala' => $this->lingkar_kepala,
                'jml_saudara_kandung' => $this->jml_saudara_kandung,
                'telepon' => $this->telepon ?: null,
                'hp' => $this->hp ?: null,
                'email' => $this->email ?: null,
            ]);

            // 4. Catat riwayat Rayon jika dipilih
            if ($this->rayon_id) {
                RayonSiswa::create([
                    'siswa_id' => $siswa->id,
                    'rayon_id' => $this->rayon_id,
                    'tgl_mulai' => now(),
                    'alasan_pindah' => 'Penempatan Siswa Baru',
                ]);
            }

            // 5. Simpan Data Orang Tua
            $this->saveOrangTua($siswa->id, 'ayah', [
                'nama' => $this->ayah_nama,
                'tahun_lahir' => $this->ayah_tahun_lahir,
                'jenjang_pendidikan' => $this->ayah_jenjang_pendidikan,
                'pekerjaan' => $this->ayah_pekerjaan,
                'penghasilan' => $this->ayah_penghasilan,
                'nik' => $this->ayah_nik,
            ]);

            $this->saveOrangTua($siswa->id, 'ibu', [
                'nama' => $this->ibu_nama,
                'tahun_lahir' => $this->ibu_tahun_lahir,
                'jenjang_pendidikan' => $this->ibu_jenjang_pendidikan,
                'pekerjaan' => $this->ibu_pekerjaan,
                'penghasilan' => $this->ibu_penghasilan,
                'nik' => $this->ibu_nik,
            ]);

            $this->saveOrangTua($siswa->id, 'wali', [
                'nama' => $this->wali_nama,
                'tahun_lahir' => $this->wali_tahun_lahir,
                'jenjang_pendidikan' => $this->wali_jenjang_pendidikan,
                'pekerjaan' => $this->wali_pekerjaan,
                'penghasilan' => $this->wali_penghasilan,
                'nik' => $this->wali_nik,
            ]);

            return $siswa;
        });

        $namaSiswa = $siswa->nama;

        if ($redirect) {
            session()->flash('status', "Siswa {$namaSiswa} berhasil ditambahkan beserta akun loginnya.");
            return redirect()->route('kesiswaan.siswa');
        }

        // Simpan & Tambah Lagi: reset formulir
        $this->resetForm();
        session()->flash('status', "Siswa {$namaSiswa} berhasil ditambahkan! Silakan input siswa berikutnya.");
    }

    protected function saveOrangTua(int $siswaId, string $hubungan, array $data): void
    {
        if (empty($data['nama'])) {
            return;
        }

        SiswaOrangTua::create(array_merge($data, [
            'siswa_id' => $siswaId,
            'hubungan' => $hubungan,
        ]));
    }

    public function resetForm(): void
    {
        $this->reset([
            'foto',
            'nama',
            'nipd',
            'jk',
            'nisn',
            'tempat_lahir',
            'tanggal_lahir',
            'nik',
            'agama',
            'alamat',
            'rt',
            'rw',
            'dusun',
            'kelurahan',
            'kecamatan',
            'kode_pos',
            'jenis_tinggal',
            'alat_transportasi',
            'lintang',
            'bujur',
            'jarak_ke_sekolah_km',
            'ayah_nama',
            'ayah_tahun_lahir',
            'ayah_jenjang_pendidikan',
            'ayah_pekerjaan',
            'ayah_penghasilan',
            'ayah_nik',
            'ibu_nama',
            'ibu_tahun_lahir',
            'ibu_jenjang_pendidikan',
            'ibu_pekerjaan',
            'ibu_penghasilan',
            'ibu_nik',
            'wali_nama',
            'wali_tahun_lahir',
            'wali_jenjang_pendidikan',
            'wali_pekerjaan',
            'wali_penghasilan',
            'wali_nik',
            'no_kk',
            'no_akta_lahir',
            'skhun',
            'no_peserta_un',
            'no_seri_ijazah',
            'sekolah_asal',
            'penerima_kps',
            'no_kps',
            'penerima_kip',
            'nomor_kip',
            'nama_di_kip',
            'nomor_kks',
            'layak_pip',
            'alasan_layak_pip',
            'bank',
            'nomor_rekening_bank',
            'rekening_atas_nama',
            'kebutuhan_khusus',
            'anak_ke_berapa',
            'berat_badan',
            'tinggi_badan',
            'lingkar_kepala',
            'jml_saudara_kandung',
            'telepon',
            'hp',
            'email',
        ]);

        $this->jk = 'L';
        $this->status = 'aktif';
        $this->is_active = true;
        $this->activeTab = 'identitas';

        $reguler = Program::where('kode', 'REG')->first();
        if ($reguler) {
            $this->program_id = $reguler->id;
        }
    }

    public function render()
    {
        $rombels = Rombel::orderBy('tingkat')->orderBy('nama')->get();
        $rayons = Rayon::orderBy('nama')->get();
        $programs = Program::where('is_active', true)->orderBy('nama')->get();

        return view('kesiswaan::livewire.siswa-create', [
            'rombels' => $rombels,
            'rayons' => $rayons,
            'programs' => $programs,
        ]);
    }
}
