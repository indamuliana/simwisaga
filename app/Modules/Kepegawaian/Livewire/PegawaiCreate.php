<?php

namespace App\Modules\Kepegawaian\Livewire;

use App\Core\Concerns\GuardsPermission;
use App\Models\User;
use App\Modules\AnalitikSpmb\Models\MasterWilayah;
use App\Modules\AnalitikSpmb\Services\SpmbWilayahService;
use App\Modules\Kepegawaian\Models\Pegawai;
use App\Modules\Kepegawaian\Models\PegawaiFoto;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Spatie\Permission\Models\Role;

#[Layout('components.layouts.app')]
#[Title('Tambah Pegawai (GTK) Baru')]
class PegawaiCreate extends Component
{
    use GuardsPermission, WithFileUploads;

    public string $activeTab = 'identitas';

    // 1. Identitas Pribadi
    public ?string $niy = null;

    public string $nama_lengkap = '';

    public ?string $gelar_depan = null;

    public ?string $gelar_belakang = null;

    public string $jenis_kelamin = 'L';

    public ?string $tempat_lahir = null;

    public ?string $tanggal_lahir = null;

    public ?string $nik = null;

    public ?string $golongan_darah = null;

    public ?string $no_hp = null;

    public ?string $email = null;

    // 2. Alamat & Wilayah
    public string $jenis_wilayah = 'dalam_negeri'; // 'dalam_negeri' | 'luar_negeri'

    public string $negara = 'Indonesia';

    public ?string $provinsi_nama = null;

    public ?string $provinsi_kode = null;

    public ?string $kabupaten_nama = null;

    public ?string $kabupaten_kode = null;

    public ?string $kecamatan_nama = null;

    public ?string $kecamatan_kode = null;

    public ?string $desa_nama = null;

    public ?string $desa_kode = null;

    public ?string $kode_pos = null;

    public ?string $alamat = null;

    // 3. Kepegawaian & SK
    public string $jenis_gtk = 'Guru';

    public string $status_kepegawaian = 'GTY';

    public ?string $jabatan_struktural = null;

    public ?string $mapel_diampu = null;

    public ?string $jam_kerja = null;

    public ?string $nip = null;

    public ?string $nuptk = null;

    public ?string $nomor_peserta_ukg = null;

    public ?string $nomor_registrasi_guru = null;

    public ?string $nomor_peserta_sertifikasi = null;

    public ?string $nomor_sertifikat_pendidik = null;

    public ?string $tmt_awal_guru = null;

    public ?string $no_sk_mulai_guru = null;

    public ?string $tgl_pertama_kontrak = null;

    public ?string $no_sk_kontrak_pertama = null;

    public ?string $tgl_diangkat_pegawai_tetap = null;

    public ?string $no_sk_tetap_yayasan = null;

    public ?string $habis_kontrak = null;

    public ?string $lama_kontrak_selanjutnya = null;

    public ?string $keterangan_perpanjang = null;

    public ?string $tgl_awal_masuk_wikrama = null;

    public bool $is_active = true;

    // 4. Pajak & BPJS
    public ?string $npwp = null;

    public ?string $nama_wajib_pajak = null;

    public ?string $no_kartu_bpjs_kesehatan = null;

    public ?string $no_kartu_bpjs_ketenagakerjaan = null;

    // 5. Pendidikan & Pengalaman
    public ?string $pendidikan_terakhir = null;

    public ?string $jenjang_pendidikan = null;

    public ?string $jurusan_dan_instansi = null;

    public ?string $tgl_lulus_pendidikan_terakhir = null;

    public ?string $tahun_lulus_pendidikan_terakhir = null;

    public array $pengalaman_kerja = [];

    // 6. Data Keluarga
    public ?string $status_pernikahan = 'Belum Menikah';

    public ?string $nama_pasangan = null;

    public ?string $tgl_lahir_pasangan = null;

    public int $jumlah_anak = 0;

    public array $data_anak = [];

    public ?string $nama_ayah = null;

    public ?string $nama_ibu = null;

    // 7. Berkas 5 Foto
    public $foto_1;

    public ?string $keterangan_1 = 'Foto Profil Utama';

    public $foto_2;

    public ?string $keterangan_2 = null;

    public $foto_3;

    public ?string $keterangan_3 = null;

    public $foto_4;

    public ?string $keterangan_4 = null;

    public $foto_5;

    public ?string $keterangan_5 = null;

    // 8. Akun Pengguna
    public bool $buat_akun = false;

    public string $username = '';

    public string $password = '';

    public string $role_name = 'Guru';

    public function mount(): void
    {
        $this->guard('kepegawaian.pegawai.tambah');

        // Inisialisasi slot Pengalaman Kerja 1..7
        for ($i = 0; $i < 7; $i++) {
            $this->pengalaman_kerja[$i] = [
                'lembaga' => '',
                'posisi' => '',
                'periode' => '',
            ];
        }

        // Inisialisasi slot Data Anak 1..4
        for ($i = 0; $i < 4; $i++) {
            $this->data_anak[$i] = [
                'nama' => '',
                'tgl_lahir' => '',
            ];
        }
    }

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    public function updatedJenisWilayah(string $val): void
    {
        if ($val === 'dalam_negeri') {
            $this->negara = 'Indonesia';
        } else {
            if ($this->negara === 'Indonesia' || empty($this->negara)) {
                $this->negara = 'Malaysia';
            }
        }
    }

    public function updatedProvinsiNama(string $val): void
    {
        $this->kabupaten_nama = '';
        $this->kabupaten_kode = null;
        $this->kecamatan_nama = '';
        $this->kecamatan_kode = null;
        $this->desa_nama = '';
        $this->desa_kode = null;
        $this->kode_pos = null;

        if (! empty($val)) {
            $p = MasterWilayah::where('level', 1)->where('nama', $val)->first();
            $this->provinsi_kode = $p?->kode;
        }
    }

    public function updatedKabupatenNama(string $val): void
    {
        $this->kecamatan_nama = '';
        $this->kecamatan_kode = null;
        $this->desa_nama = '';
        $this->desa_kode = null;
        $this->kode_pos = null;

        if (! empty($val)) {
            $kab = MasterWilayah::where('level', 2)->where('nama', $val)->first();
            $this->kabupaten_kode = $kab?->kode;
            if ($kab && $kab->parent_kode && empty($this->provinsi_nama)) {
                $prov = MasterWilayah::where('kode', $kab->parent_kode)->first();
                if ($prov) {
                    $this->provinsi_nama = $prov->nama;
                    $this->provinsi_kode = $prov->kode;
                }
            }
        }
    }

    public function updatedKecamatanNama(string $val): void
    {
        $this->desa_nama = '';
        $this->desa_kode = null;

        if (! empty($val)) {
            $kec = MasterWilayah::where('level', 3)->where('nama', $val)->first();
            $this->kecamatan_kode = $kec?->kode;
        }
    }

    public function updatedDesaNama(string $val): void
    {
        if (! empty($val)) {
            $desa = MasterWilayah::where('level', 4)->where('nama', $val)->first();
            $this->desa_kode = $desa?->kode;
            if ($desa && $desa->kode_pos && empty($this->kode_pos)) {
                $this->kode_pos = $desa->kode_pos;
            }
        }
    }

    public function updatedDataAnak(): void
    {
        // Hitung otomatis jumlah anak dari slot yang terisi nama
        $terisi = 0;
        foreach ($this->data_anak as $anak) {
            if (! empty(trim($anak['nama'] ?? ''))) {
                $terisi++;
            }
        }
        $this->jumlah_anak = $terisi;
    }

    public function updatedTglLulusPendidikanTerakhir(?string $val): void
    {
        if ($val && empty($this->tahun_lulus_pendidikan_terakhir)) {
            $this->tahun_lulus_pendidikan_terakhir = Carbon::parse($val)->format('Y');
        }
    }

    public function save()
    {
        $this->guard('kepegawaian.pegawai.tambah');

        $this->validate([
            'nama_lengkap' => 'required|string|max:150',
            'niy' => 'nullable|string|max:30|unique:pegawais,niy',
            'jenis_kelamin' => 'required|in:L,P',
            'jenis_gtk' => 'required|string|max:50',
            'status_kepegawaian' => 'required|string|max:50',
            'nip' => 'nullable|string|max:30|unique:pegawais,nip',
            'nuptk' => 'nullable|string|max:30|unique:pegawais,nuptk',
            'nik' => 'nullable|string|max:20|unique:pegawais,nik',
            'email' => 'nullable|email|max:100',
            'tgl_pertama_kontrak' => 'nullable|date',
            'habis_kontrak' => 'nullable|date',
            'foto_1' => 'required|image|max:3072', // Wajib slot 1
            'foto_2' => 'nullable|image|max:3072',
            'foto_3' => 'nullable|image|max:3072',
            'foto_4' => 'nullable|image|max:3072',
            'foto_5' => 'nullable|image|max:3072',
            'username' => Rule::requiredIf($this->buat_akun),
            'password' => Rule::requiredIf($this->buat_akun),
        ], [
            'foto_1.required' => 'Foto profil utama (Slot 1) wajib diunggah.',
            'foto_1.image' => 'File Foto Profil Utama harus berupa gambar (JPG, PNG, WebP).',
            'foto_1.max' => 'Ukuran foto maksimal 3MB.',
            'nama_lengkap.required' => 'Nama lengkap wajib diisi.',
        ]);

        return DB::transaction(function () {
            $userId = null;
            if ($this->buat_akun) {
                $user = User::create([
                    'name' => $this->nama_lengkap,
                    'username' => $this->username,
                    'password' => Hash::make($this->password),
                    'is_active' => true,
                    'must_change_password' => false,
                ]);
                $user->assignRole($this->role_name);
                $userId = $user->id;
            }

            // Simpan foto utama (Slot 1)
            $fotoPath = $this->foto_1->store('pegawai-fotos', 'public');

            $pegawai = Pegawai::create([
                'user_id' => $userId,
                'niy' => $this->niy ?: null,
                'nama_lengkap' => $this->nama_lengkap,
                'gelar_depan' => $this->gelar_depan ?: null,
                'gelar_belakang' => $this->gelar_belakang ?: null,
                'nip' => $this->nip ?: null,
                'nuptk' => $this->nuptk ?: null,
                'nik' => $this->nik ?: null,
                'jenis_kelamin' => $this->jenis_kelamin,
                'jenis_gtk' => $this->jenis_gtk,
                'status_kepegawaian' => $this->status_kepegawaian,
                'tempat_lahir' => $this->tempat_lahir ?: null,
                'tanggal_lahir' => $this->tanggal_lahir ?: null,
                'no_hp' => $this->no_hp ?: null,
                'email' => $this->email ?: null,
                'alamat' => $this->alamat ?: null,
                'jenis_wilayah' => $this->jenis_wilayah,
                'negara' => $this->negara ?: 'Indonesia',
                'provinsi_kode' => $this->provinsi_kode ?: null,
                'provinsi_nama' => $this->provinsi_nama ?: null,
                'kabupaten_kode' => $this->kabupaten_kode ?: null,
                'kabupaten_nama' => $this->kabupaten_nama ?: null,
                'kecamatan_kode' => $this->kecamatan_kode ?: null,
                'kecamatan_nama' => $this->kecamatan_nama ?: null,
                'desa_kode' => $this->desa_kode ?: null,
                'desa_nama' => $this->desa_nama ?: null,
                'kode_pos' => $this->kode_pos ?: null,
                'jabatan_struktural' => $this->jabatan_struktural ?: null,
                'mapel_diampu' => $this->mapel_diampu ?: null,
                'jam_kerja' => $this->jam_kerja ?: null,
                'nomor_peserta_ukg' => $this->nomor_peserta_ukg ?: null,
                'nomor_registrasi_guru' => $this->nomor_registrasi_guru ?: null,
                'nomor_peserta_sertifikasi' => $this->nomor_peserta_sertifikasi ?: null,
                'nomor_sertifikat_pendidik' => $this->nomor_sertifikat_pendidik ?: null,
                'tmt_awal_guru' => $this->tmt_awal_guru ?: null,
                'no_sk_mulai_guru' => $this->no_sk_mulai_guru ?: null,
                'tgl_pertama_kontrak' => $this->tgl_pertama_kontrak ?: null,
                'no_sk_kontrak_pertama' => $this->no_sk_kontrak_pertama ?: null,
                'tgl_diangkat_pegawai_tetap' => $this->tgl_diangkat_pegawai_tetap ?: null,
                'no_sk_tetap_yayasan' => $this->no_sk_tetap_yayasan ?: null,
                'habis_kontrak' => $this->habis_kontrak ?: null,
                'lama_kontrak_selanjutnya' => $this->lama_kontrak_selanjutnya ?: null,
                'keterangan_perpanjang' => $this->keterangan_perpanjang ?: null,
                'tgl_awal_masuk_wikrama' => $this->tgl_awal_masuk_wikrama ?: null,
                'golongan_darah' => $this->golongan_darah ?: null,
                'npwp' => $this->npwp ?: null,
                'nama_wajib_pajak' => $this->nama_wajib_pajak ?: null,
                'no_kartu_bpjs_kesehatan' => $this->no_kartu_bpjs_kesehatan ?: null,
                'no_kartu_bpjs_ketenagakerjaan' => $this->no_kartu_bpjs_ketenagakerjaan ?: null,
                'pendidikan_terakhir' => $this->pendidikan_terakhir ?: null,
                'jenjang_pendidikan' => $this->jenjang_pendidikan ?: null,
                'jurusan_dan_instansi' => $this->jurusan_dan_instansi ?: null,
                'tgl_lulus_pendidikan_terakhir' => $this->tgl_lulus_pendidikan_terakhir ?: null,
                'tahun_lulus_pendidikan_terakhir' => $this->tahun_lulus_pendidikan_terakhir ?: null,
                'pengalaman_kerja' => $this->pengalaman_kerja,
                'status_pernikahan' => $this->status_pernikahan,
                'nama_pasangan' => $this->nama_pasangan ?: null,
                'tgl_lahir_pasangan' => $this->tgl_lahir_pasangan ?: null,
                'jumlah_anak' => $this->jumlah_anak,
                'data_anak' => $this->data_anak,
                'nama_ayah' => $this->nama_ayah ?: null,
                'nama_ibu' => $this->nama_ibu ?: null,
                'foto_path' => $fotoPath,
                'is_active' => $this->is_active,
            ]);

            // Simpan record Slot 1 di pegawai_fotos
            PegawaiFoto::create([
                'pegawai_id' => $pegawai->id,
                'slot' => 1,
                'foto_path' => $fotoPath,
                'keterangan' => $this->keterangan_1 ?: 'Foto Profil Utama',
            ]);

            // Simpan slot 2 s/d 5 jika diunggah
            for ($s = 2; $s <= 5; $s++) {
                $propFoto = "foto_{$s}";
                $propKet = "keterangan_{$s}";
                if ($this->{$propFoto}) {
                    $pathAdd = $this->{$propFoto}->store('pegawai-fotos', 'public');
                    PegawaiFoto::create([
                        'pegawai_id' => $pegawai->id,
                        'slot' => $s,
                        'foto_path' => $pathAdd,
                        'keterangan' => $this->{$propKet} ?: 'Foto Tambahan '.($s - 1),
                    ]);
                }
            }

            session()->flash('status', "Data pegawai {$pegawai->nama_lengkap} berhasil ditambahkan!");

            return redirect()->route('kepegawaian.pegawai.show', $pegawai->id);
        });
    }

    public function render()
    {
        $wilayahService = app(SpmbWilayahService::class);
        $provinceList = [];
        $kabupatenList = [];
        $kecamatanList = [];
        $desaList = [];

        if ($this->jenis_wilayah === 'dalam_negeri') {
            $provinceList = $wilayahService->getProvincesList();
            $kabupatenList = $wilayahService->getKabupatenList($this->provinsi_nama ?: null);
            $kecamatanList = $wilayahService->getKecamatanList($this->kabupaten_nama ?: null, $this->provinsi_nama ?: null);
            $desaList = $wilayahService->getDesaList($this->kecamatan_nama ?: null, $this->kabupaten_nama ?: null);
        }

        $negaraList = [
            'Indonesia', 'Malaysia', 'Singapura', 'Brunei Darussalam', 'Arab Saudi', 'Mesir',
            'Yaman', 'Turki', 'Jepang', 'Korea Selatan', 'Australia', 'Inggris', 'Amerika Serikat',
        ];

        $roles = Role::whereNotIn('name', ['Super Admin', 'Siswa', 'Orang Tua'])->get();

        return view('kepegawaian::livewire.pegawai-create', [
            'provinceList' => $provinceList,
            'kabupatenList' => $kabupatenList,
            'kecamatanList' => $kecamatanList,
            'desaList' => $desaList,
            'negaraList' => $negaraList,
            'roles' => $roles,
        ]);
    }
}
