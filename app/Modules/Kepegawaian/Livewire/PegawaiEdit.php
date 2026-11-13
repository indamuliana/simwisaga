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
#[Title('Edit Data Pegawai (GTK)')]
class PegawaiEdit extends Component
{
    use GuardsPermission, WithFileUploads;

    public Pegawai $pegawai;

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
    public string $jenis_wilayah = 'dalam_negeri';

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

    // 7. Berkas 5 Foto (New & Existing)
    public $foto_1;

    public ?string $keterangan_1 = 'Foto Profil Utama';

    public ?string $existingFoto1 = null;

    public $foto_2;

    public ?string $keterangan_2 = null;

    public ?string $existingFoto2 = null;

    public $foto_3;

    public ?string $keterangan_3 = null;

    public ?string $existingFoto3 = null;

    public $foto_4;

    public ?string $keterangan_4 = null;

    public ?string $existingFoto4 = null;

    public $foto_5;

    public ?string $keterangan_5 = null;

    public ?string $existingFoto5 = null;

    // 8. Akun Pengguna
    public bool $buat_akun = false;

    public string $username = '';

    public string $password = '';

    public string $role_name = 'Guru';

    public ?User $linkedUser = null;

    public function mount(int $id): void
    {
        $this->guard('kepegawaian.pegawai.edit');

        $this->pegawai = Pegawai::with(['fotos', 'user.roles'])->findOrFail($id);
        $p = $this->pegawai;

        // Prefill Data
        $this->niy = $p->niy;
        $this->nama_lengkap = $p->nama_lengkap;
        $this->gelar_depan = $p->gelar_depan;
        $this->gelar_belakang = $p->gelar_belakang;
        $this->jenis_kelamin = $p->jenis_kelamin;
        $this->tempat_lahir = $p->tempat_lahir;
        $this->tanggal_lahir = $p->tanggal_lahir ? $p->tanggal_lahir->format('Y-m-d') : null;
        $this->nik = $p->nik;
        $this->golongan_darah = $p->golongan_darah;
        $this->no_hp = $p->no_hp;
        $this->email = $p->email;

        $this->jenis_wilayah = $p->jenis_wilayah ?: 'dalam_negeri';
        $this->negara = $p->negara ?: 'Indonesia';
        $this->provinsi_kode = $p->provinsi_kode;
        $this->provinsi_nama = $p->provinsi_nama;
        $this->kabupaten_kode = $p->kabupaten_kode;
        $this->kabupaten_nama = $p->kabupaten_nama;
        $this->kecamatan_kode = $p->kecamatan_kode;
        $this->kecamatan_nama = $p->kecamatan_nama;
        $this->desa_kode = $p->desa_kode;
        $this->desa_nama = $p->desa_nama;
        $this->kode_pos = $p->kode_pos;
        $this->alamat = $p->alamat;

        $this->jenis_gtk = $p->jenis_gtk ?: 'Guru';
        $this->status_kepegawaian = $p->status_kepegawaian ?: 'GTY';
        $this->jabatan_struktural = $p->jabatan_struktural;
        $this->mapel_diampu = $p->mapel_diampu;
        $this->jam_kerja = $p->jam_kerja;
        $this->nip = $p->nip;
        $this->nuptk = $p->nuptk;
        $this->nomor_peserta_ukg = $p->nomor_peserta_ukg;
        $this->nomor_registrasi_guru = $p->nomor_registrasi_guru;
        $this->nomor_peserta_sertifikasi = $p->nomor_peserta_sertifikasi;
        $this->nomor_sertifikat_pendidik = $p->nomor_sertifikat_pendidik;
        $this->tmt_awal_guru = $p->tmt_awal_guru ? $p->tmt_awal_guru->format('Y-m-d') : null;
        $this->no_sk_mulai_guru = $p->no_sk_mulai_guru;
        $this->tgl_pertama_kontrak = $p->tgl_pertama_kontrak ? $p->tgl_pertama_kontrak->format('Y-m-d') : null;
        $this->no_sk_kontrak_pertama = $p->no_sk_kontrak_pertama;
        $this->tgl_diangkat_pegawai_tetap = $p->tgl_diangkat_pegawai_tetap ? $p->tgl_diangkat_pegawai_tetap->format('Y-m-d') : null;
        $this->no_sk_tetap_yayasan = $p->no_sk_tetap_yayasan;
        $this->habis_kontrak = $p->habis_kontrak ? $p->habis_kontrak->format('Y-m-d') : null;
        $this->lama_kontrak_selanjutnya = $p->lama_kontrak_selanjutnya;
        $this->keterangan_perpanjang = $p->keterangan_perpanjang;
        $this->tgl_awal_masuk_wikrama = $p->tgl_awal_masuk_wikrama ? $p->tgl_awal_masuk_wikrama->format('Y-m-d') : null;
        $this->is_active = (bool) $p->is_active;

        $this->npwp = $p->npwp;
        $this->nama_wajib_pajak = $p->nama_wajib_pajak;
        $this->no_kartu_bpjs_kesehatan = $p->no_kartu_bpjs_kesehatan;
        $this->no_kartu_bpjs_ketenagakerjaan = $p->no_kartu_bpjs_ketenagakerjaan;

        $this->pendidikan_terakhir = $p->pendidikan_terakhir;
        $this->jenjang_pendidikan = $p->jenjang_pendidikan;
        $this->jurusan_dan_instansi = $p->jurusan_dan_instansi;
        $this->tgl_lulus_pendidikan_terakhir = $p->tgl_lulus_pendidikan_terakhir ? $p->tgl_lulus_pendidikan_terakhir->format('Y-m-d') : null;
        $this->tahun_lulus_pendidikan_terakhir = $p->tahun_lulus_pendidikan_terakhir;

        // Inisialisasi Pengalaman Kerja 1..7
        $savedPengalaman = is_array($p->pengalaman_kerja) ? $p->pengalaman_kerja : [];
        for ($i = 0; $i < 7; $i++) {
            $this->pengalaman_kerja[$i] = [
                'lembaga' => $savedPengalaman[$i]['lembaga'] ?? '',
                'posisi' => $savedPengalaman[$i]['posisi'] ?? '',
                'periode' => $savedPengalaman[$i]['periode'] ?? '',
            ];
        }

        // Data Keluarga
        $this->status_pernikahan = $p->status_pernikahan ?: 'Belum Menikah';
        $this->nama_pasangan = $p->nama_pasangan;
        $this->tgl_lahir_pasangan = $p->tgl_lahir_pasangan ? $p->tgl_lahir_pasangan->format('Y-m-d') : null;
        $this->jumlah_anak = $p->jumlah_anak ?: 0;
        $this->nama_ayah = $p->nama_ayah;
        $this->nama_ibu = $p->nama_ibu;

        // Inisialisasi Data Anak 1..4
        $savedAnak = is_array($p->data_anak) ? $p->data_anak : [];
        for ($i = 0; $i < 4; $i++) {
            $this->data_anak[$i] = [
                'nama' => $savedAnak[$i]['nama'] ?? '',
                'tgl_lahir' => $savedAnak[$i]['tgl_lahir'] ?? '',
            ];
        }

        // Inisialisasi Foto
        $fotosMap = $p->fotos->keyBy('slot');

        $f1 = $fotosMap->get(1);
        $this->existingFoto1 = $f1?->foto_path ?: $p->foto_path;
        $this->keterangan_1 = $f1?->keterangan ?: 'Foto Profil Utama';

        for ($s = 2; $s <= 5; $s++) {
            $f = $fotosMap->get($s);
            $propExist = "existingFoto{$s}";
            $propKet = "keterangan_{$s}";
            $this->{$propExist} = $f?->foto_path;
            $this->{$propKet} = $f?->keterangan ?: 'Foto Tambahan '.($s - 1);
        }

        // Akun Login Pengguna
        if ($p->user) {
            $this->linkedUser = $p->user;
            $this->username = $p->user->username;
            $this->role_name = $p->user->getRoleNames()->first() ?: 'Guru';
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
        $this->guard('kepegawaian.pegawai.edit');

        $this->validate([
            'nama_lengkap' => 'required|string|max:150',
            'niy' => ['nullable', 'string', 'max:30', Rule::unique('pegawais', 'niy')->ignore($this->pegawai->id)],
            'jenis_kelamin' => 'required|in:L,P',
            'jenis_gtk' => 'required|string|max:50',
            'status_kepegawaian' => 'required|string|max:50',
            'nip' => ['nullable', 'string', 'max:30', Rule::unique('pegawais', 'nip')->ignore($this->pegawai->id)],
            'nuptk' => ['nullable', 'string', 'max:30', Rule::unique('pegawais', 'nuptk')->ignore($this->pegawai->id)],
            'nik' => ['nullable', 'string', 'max:20', Rule::unique('pegawais', 'nik')->ignore($this->pegawai->id)],
            'email' => 'nullable|email|max:100',
            'tgl_pertama_kontrak' => 'nullable|date',
            'habis_kontrak' => 'nullable|date',
            'foto_1' => ['nullable', 'image', 'max:3072'],
            'foto_2' => 'nullable|image|max:3072',
            'foto_3' => 'nullable|image|max:3072',
            'foto_4' => 'nullable|image|max:3072',
            'foto_5' => 'nullable|image|max:3072',
        ], [
            'foto_1.image' => 'File Foto Profil Utama harus berupa gambar (JPG, PNG, WebP).',
            'foto_1.max' => 'Ukuran foto maksimal 3MB.',
            'nama_lengkap.required' => 'Nama lengkap wajib diisi.',
        ]);

        return DB::transaction(function () {
            // Update akun user jika ada
            if ($this->linkedUser) {
                $this->linkedUser->name = $this->nama_lengkap;
                if (! empty($this->password)) {
                    $this->linkedUser->password = Hash::make($this->password);
                }
                $this->linkedUser->save();
                $this->linkedUser->syncRoles([$this->role_name]);
            } elseif ($this->buat_akun) {
                $this->validate([
                    'username' => 'required|string|unique:users,username',
                    'password' => 'required|string|min:6',
                ]);
                $user = User::create([
                    'name' => $this->nama_lengkap,
                    'username' => $this->username,
                    'password' => Hash::make($this->password),
                    'is_active' => true,
                    'must_change_password' => false,
                ]);
                $user->assignRole($this->role_name);
                $this->pegawai->user_id = $user->id;
            }

            // Handle Foto Slot 1
            $fotoPath = $this->existingFoto1;
            if ($this->foto_1) {
                $fotoPath = $this->foto_1->store('pegawai-fotos', 'public');
                PegawaiFoto::updateOrCreate(
                    ['pegawai_id' => $this->pegawai->id, 'slot' => 1],
                    ['foto_path' => $fotoPath, 'keterangan' => $this->keterangan_1 ?: 'Foto Profil Utama']
                );
            } else {
                PegawaiFoto::updateOrCreate(
                    ['pegawai_id' => $this->pegawai->id, 'slot' => 1],
                    ['foto_path' => $fotoPath ?: '', 'keterangan' => $this->keterangan_1 ?: 'Foto Profil Utama']
                );
            }

            // Handle Foto Slot 2..5
            for ($s = 2; $s <= 5; $s++) {
                $propFoto = "foto_{$s}";
                $propKet = "keterangan_{$s}";
                $propExist = "existingFoto{$s}";

                if ($this->{$propFoto}) {
                    $newPath = $this->{$propFoto}->store('pegawai-fotos', 'public');
                    PegawaiFoto::updateOrCreate(
                        ['pegawai_id' => $this->pegawai->id, 'slot' => $s],
                        ['foto_path' => $newPath, 'keterangan' => $this->{$propKet} ?: 'Foto Tambahan '.($s - 1)]
                    );
                } elseif ($this->{$propExist}) {
                    PegawaiFoto::updateOrCreate(
                        ['pegawai_id' => $this->pegawai->id, 'slot' => $s],
                        ['keterangan' => $this->{$propKet} ?: 'Foto Tambahan '.($s - 1)]
                    );
                }
            }

            $this->pegawai->update([
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

            session()->flash('status', "Perubahan data pegawai {$this->pegawai->nama_lengkap} berhasil disimpan!");

            return redirect()->route('kepegawaian.pegawai.show', $this->pegawai->id);
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

        return view('kepegawaian::livewire.pegawai-edit', [
            'provinceList' => $provinceList,
            'kabupatenList' => $kabupatenList,
            'kecamatanList' => $kecamatanList,
            'desaList' => $desaList,
            'negaraList' => $negaraList,
            'roles' => $roles,
        ]);
    }
}
