<?php

namespace App\Modules\Kesiswaan\Livewire;

use App\Core\Concerns\GuardsPermission;
use App\Modules\Akademik\Models\Rayon;
use App\Modules\Akademik\Models\Rombel;
use App\Modules\Kesiswaan\Models\Program;
use App\Modules\Kesiswaan\Models\Siswa;
use App\Modules\Kesiswaan\Models\SiswaOrangTua;
use App\Modules\Kesiswaan\Services\SingleSiswaDocumentExporter;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('components.layouts.app')]
#[Title('Edit Biodata Lengkap Siswa')]
class SiswaEdit extends Component
{
    use GuardsPermission, WithFileUploads;

    public Siswa $siswa;
    public string $activeTab = 'identitas';

    // Foto Siswa
    public $foto;
    public ?string $existingFotoPath = null;

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

    public function mount(int $id): void
    {
        $this->guard('kesiswaan.siswa.edit');
        $this->siswa = Siswa::with(['rombel', 'rayon', 'program', 'orangTuas', 'user'])->findOrFail($id);

        $s = $this->siswa;
        $this->existingFotoPath = $s->foto_path;

        // 1. Identitas
        $this->nama = $s->nama;
        $this->nipd = $s->nipd;
        $this->jk = $s->jk;
        $this->nisn = $s->nisn;
        $this->tempat_lahir = $s->tempat_lahir;
        $this->tanggal_lahir = $s->tanggal_lahir?->format('Y-m-d');
        $this->nik = $s->nik;
        $this->agama = $s->agama;
        $this->rombel_id = $s->rombel_id;
        $this->rayon_id = $s->rayon_id;
        $this->program_id = $s->program_id;
        $this->status = $s->status;
        $this->is_active = (bool) $s->is_active;

        // 2. Alamat
        $this->alamat = $s->alamat;
        $this->rt = $s->rt;
        $this->rw = $s->rw;
        $this->dusun = $s->dusun;
        $this->kelurahan = $s->kelurahan;
        $this->kecamatan = $s->kecamatan;
        $this->kode_pos = $s->kode_pos;
        $this->jenis_tinggal = $s->jenis_tinggal;
        $this->alat_transportasi = $s->alat_transportasi;
        $this->lintang = $s->lintang;
        $this->bujur = $s->bujur;
        $this->jarak_ke_sekolah_km = $s->jarak_ke_sekolah_km ? (float) $s->jarak_ke_sekolah_km : null;

        // 3. Orang Tua
        $ayah = $s->ayah;
        if ($ayah) {
            $this->ayah_nama = $ayah->nama;
            $this->ayah_tahun_lahir = $ayah->tahun_lahir;
            $this->ayah_jenjang_pendidikan = $ayah->jenjang_pendidikan;
            $this->ayah_pekerjaan = $ayah->pekerjaan;
            $this->ayah_penghasilan = $ayah->penghasilan;
            $this->ayah_nik = $ayah->nik;
        }

        $ibu = $s->ibu;
        if ($ibu) {
            $this->ibu_nama = $ibu->nama;
            $this->ibu_tahun_lahir = $ibu->tahun_lahir;
            $this->ibu_jenjang_pendidikan = $ibu->jenjang_pendidikan;
            $this->ibu_pekerjaan = $ibu->pekerjaan;
            $this->ibu_penghasilan = $ibu->penghasilan;
            $this->ibu_nik = $ibu->nik;
        }

        $wali = $s->wali;
        if ($wali) {
            $this->wali_nama = $wali->nama;
            $this->wali_tahun_lahir = $wali->tahun_lahir;
            $this->wali_jenjang_pendidikan = $wali->jenjang_pendidikan;
            $this->wali_pekerjaan = $wali->pekerjaan;
            $this->wali_penghasilan = $wali->penghasilan;
            $this->wali_nik = $wali->nik;
        }

        // 4. Bantuan & Dokumen
        $this->no_kk = $s->no_kk;
        $this->no_akta_lahir = $s->no_akta_lahir;
        $this->skhun = $s->skhun;
        $this->no_peserta_un = $s->no_peserta_un;
        $this->no_seri_ijazah = $s->no_seri_ijazah;
        $this->sekolah_asal = $s->sekolah_asal;
        $this->penerima_kps = (bool) $s->penerima_kps;
        $this->no_kps = $s->no_kps;
        $this->penerima_kip = (bool) $s->penerima_kip;
        $this->nomor_kip = $s->nomor_kip;
        $this->nama_di_kip = $s->nama_di_kip;
        $this->nomor_kks = $s->nomor_kks;
        $this->layak_pip = (bool) $s->layak_pip;
        $this->alasan_layak_pip = $s->alasan_layak_pip;
        $this->bank = $s->bank;
        $this->nomor_rekening_bank = $s->nomor_rekening_bank;
        $this->rekening_atas_nama = $s->rekening_atas_nama;

        // 5. Fisik & Kontak
        $this->kebutuhan_khusus = $s->kebutuhan_khusus;
        $this->anak_ke_berapa = $s->anak_ke_berapa;
        $this->berat_badan = $s->berat_badan;
        $this->tinggi_badan = $s->tinggi_badan;
        $this->lingkar_kepala = $s->lingkar_kepala;
        $this->jml_saudara_kandung = $s->jml_saudara_kandung;
        $this->telepon = $s->telepon;
        $this->hp = $s->hp;
        $this->email = $s->email;
    }

    protected function rules(): array
    {
        return [
            'nama' => ['required', 'string', 'max:150'],
            'nipd' => ['nullable', 'string', 'max:30'],
            'jk' => ['required', 'in:L,P'],
            'nisn' => ['required', 'string', 'max:20', Rule::unique('siswas', 'nisn')->ignore($this->siswa->id)],
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

    public function resetPasswordDefault(): void
    {
        $this->guard('kesiswaan.siswa.edit');

        if ($this->siswa->user && $this->tanggal_lahir) {
            $defaultPass = Carbon::parse($this->tanggal_lahir)->format('dmY');
            $this->siswa->user->update([
                'password' => Hash::make($defaultPass),
                'must_change_password' => true,
            ]);
            session()->flash('status', "Password login siswa ({$this->siswa->user->username}) berhasil di-reset ke default tanggal lahir: {$defaultPass}");
        }
    }

    public function downloadDetail(string $format = 'pdf')
    {
        $this->guard('kesiswaan.siswa.lihat');
        $exporter = new SingleSiswaDocumentExporter();

        return match ($format) {
            'word' => $exporter->downloadWord($this->siswa),
            'excel' => $exporter->downloadExcel($this->siswa),
            default => $exporter->downloadPdf($this->siswa),
        };
    }

    public function save(): void
    {
        $this->guard('kesiswaan.siswa.edit');
        $this->validate();

        // 1. Simpan Foto jika diunggah baru
        $fotoPath = $this->existingFotoPath;
        if ($this->foto) {
            $fotoPath = $this->foto->store('photos/siswa', 'public');
        }

        // 2. Simpan Data Siswa
        $this->siswa->update([
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

        // 3. Simpan / Perbarui Data Orang Tua
        $this->updateOrangTua('ayah', [
            'nama' => $this->ayah_nama,
            'tahun_lahir' => $this->ayah_tahun_lahir,
            'jenjang_pendidikan' => $this->ayah_jenjang_pendidikan,
            'pekerjaan' => $this->ayah_pekerjaan,
            'penghasilan' => $this->ayah_penghasilan,
            'nik' => $this->ayah_nik,
        ]);

        $this->updateOrangTua('ibu', [
            'nama' => $this->ibu_nama,
            'tahun_lahir' => $this->ibu_tahun_lahir,
            'jenjang_pendidikan' => $this->ibu_jenjang_pendidikan,
            'pekerjaan' => $this->ibu_pekerjaan,
            'penghasilan' => $this->ibu_penghasilan,
            'nik' => $this->ibu_nik,
        ]);

        $this->updateOrangTua('wali', [
            'nama' => $this->wali_nama,
            'tahun_lahir' => $this->wali_tahun_lahir,
            'jenjang_pendidikan' => $this->wali_jenjang_pendidikan,
            'pekerjaan' => $this->wali_pekerjaan,
            'penghasilan' => $this->wali_penghasilan,
            'nik' => $this->wali_nik,
        ]);

        // 4. Sinkronisasi Akun Login Siswa jika NIS berubah
        if ($this->siswa->user) {
            $usernameSiswa = $this->nipd ?: $this->nisn;
            $this->siswa->user->update([
                'name' => $this->nama,
                'username' => $usernameSiswa,
            ]);
        }

        session()->flash('status', "Seluruh data siswa {$this->nama} berhasil disimpan.");
    }

    protected function updateOrangTua(string $hubungan, array $data): void
    {
        if (empty($data['nama'])) {
            SiswaOrangTua::where('siswa_id', $this->siswa->id)->where('hubungan', $hubungan)->delete();
            return;
        }

        SiswaOrangTua::updateOrCreate(
            ['siswa_id' => $this->siswa->id, 'hubungan' => $hubungan],
            $data
        );
    }

    public function render()
    {
        $rombels = Rombel::orderBy('tingkat')->orderBy('nama')->get();
        $rayons = Rayon::orderBy('nama')->get();
        $programs = Program::where('is_active', true)->orderBy('nama')->get();

        return view('kesiswaan::livewire.siswa-edit', [
            'rombels' => $rombels,
            'rayons' => $rayons,
            'programs' => $programs,
        ]);
    }
}
