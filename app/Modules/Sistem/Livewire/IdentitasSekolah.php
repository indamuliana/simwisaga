<?php

namespace App\Modules\Sistem\Livewire;

use App\Core\Concerns\GuardsPermission;
use App\Modules\Kesiswaan\Services\DapodikImporter;
use App\Modules\Sistem\Models\School;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('components.layouts.app')]
#[Title('Identitas Sekolah')]
class IdentitasSekolah extends Component
{
    use GuardsPermission, WithFileUploads;

    public string $nama = '';
    public ?string $npsn = null;
    public ?string $nss = null;
    public ?string $nama_yayasan = null;
    public ?string $alamat = null;
    public ?string $kelurahan = null;
    public ?string $kecamatan = null;
    public ?string $kabupaten = null;
    public ?string $provinsi = null;
    public ?string $kode_pos = null;
    public ?string $telepon = null;
    public ?string $email = null;
    public ?string $website = null;
    public ?string $kepala_sekolah_nama = null;
    public ?string $kepala_sekolah_nip = null;
    public string $nama_aplikasi = 'SIRAGA';
    public ?string $kepanjangan_aplikasi = null;

    /** Unggahan sementara (file belum disimpan). */
    public $logo_sekolah = null;
    public $logo_aplikasi = null;
    public $kop_surat = null;

    private const FIELDS = [
        'nama', 'npsn', 'nss', 'nama_yayasan', 'alamat', 'kelurahan', 'kecamatan', 'kabupaten',
        'provinsi', 'kode_pos', 'telepon', 'email', 'website', 'kepala_sekolah_nama',
        'kepala_sekolah_nip', 'nama_aplikasi', 'kepanjangan_aplikasi',
    ];

    public function mount(): void
    {
        $school = School::current();
        foreach (self::FIELDS as $field) {
            $this->{$field} = $school->{$field};
        }
    }

    protected function rules(): array
    {
        $image = ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:2048']; // SVG sengaja tidak diizinkan (risiko XSS)

        return [
            'nama' => ['required', 'string', 'max:150'],
            'npsn' => ['nullable', 'string', 'max:20'],
            'nss' => ['nullable', 'string', 'max:20'],
            'nama_yayasan' => ['nullable', 'string', 'max:150'],
            'alamat' => ['nullable', 'string', 'max:500'],
            'kelurahan' => ['nullable', 'string', 'max:100'],
            'kecamatan' => ['nullable', 'string', 'max:100'],
            'kabupaten' => ['nullable', 'string', 'max:100'],
            'provinsi' => ['nullable', 'string', 'max:100'],
            'kode_pos' => ['nullable', 'string', 'max:10'],
            'telepon' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:150'],
            'website' => ['nullable', 'string', 'max:150'],
            'kepala_sekolah_nama' => ['nullable', 'string', 'max:150'],
            'kepala_sekolah_nip' => ['nullable', 'string', 'max:30'],
            'nama_aplikasi' => ['required', 'string', 'max:50'],
            'kepanjangan_aplikasi' => ['nullable', 'string', 'max:150'],
            'logo_sekolah' => $image,
            'logo_aplikasi' => $image,
            'kop_surat' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:4096', 'dimensions:min_width=800'],
        ];
    }

    protected function validationAttributes(): array
    {
        return [
            'nama' => 'nama sekolah',
            'nama_aplikasi' => 'nama aplikasi',
            'logo_sekolah' => 'logo sekolah',
            'logo_aplikasi' => 'logo aplikasi',
            'kop_surat' => 'kop surat',
        ];
    }

    public function save(): void
    {
        $this->guard('sistem.sekolah.edit');
        $this->validate();

        $school = School::query()->first() ?? new School;
        $school->fill($this->only(self::FIELDS));

        foreach (['logo_sekolah' => 'logo_sekolah_path', 'logo_aplikasi' => 'logo_aplikasi_path', 'kop_surat' => 'kop_surat_path'] as $input => $column) {
            if ($this->{$input}) {
                $this->deleteFile($school->{$column});
                $school->{$column} = $this->{$input}->store('sekolah', 'public');
                $this->{$input} = null;
            }
        }

        $school->save();

        session()->flash('status', 'Identitas sekolah berhasil disimpan.');
    }

    /** Hapus salah satu gambar yang sudah tersimpan. */
    public function removeFile(string $type): void
    {
        $this->guard('sistem.sekolah.edit');

        $column = ['logo_sekolah' => 'logo_sekolah_path', 'logo_aplikasi' => 'logo_aplikasi_path', 'kop_surat' => 'kop_surat_path'][$type] ?? null;
        abort_unless($column, 422);

        $school = School::query()->first();
        if ($school && $school->{$column}) {
            $this->deleteFile($school->{$column});
            $school->{$column} = null;
            $school->save();
        }
    }

    private function deleteFile(?string $path): void
    {
        if ($path) {
            Storage::disk('public')->delete($path);
        }
    }

    /**
     * Menghapus seluruh data siswa dan akun user siswa & orang tua
     */
    public function resetAllSiswa(): void
    {
        if (! auth()->user()->can('sistem.sekolah.edit') && ! auth()->user()->can('kesiswaan.siswa.hapus')) {
            abort(403, 'Anda tidak memiliki hak akses untuk mereset data siswa.');
        }

        $cleaned = DapodikImporter::cleanSiswaData();

        session()->flash('status', "Seluruh data siswa ({$cleaned['siswas']} siswa) dan {$cleaned['users']} akun pengguna (Siswa & Orang Tua) telah berhasil dihapus bersih dari sistem.");
    }

    public function render()
    {
        return view('sistem::livewire.identitas-sekolah', ['school' => School::query()->first() ?? School::current()]);
    }
}
