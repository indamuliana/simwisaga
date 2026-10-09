<?php

namespace App\Modules\Akademik\Livewire;

use App\Core\Concerns\GuardsPermission;
use App\Models\User;
use App\Modules\Akademik\Models\Jurusan;
use App\Modules\Akademik\Models\Rombel;
use App\Modules\Akademik\Models\TahunAjaran;
use App\Modules\Akademik\Support\AcademicContext;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Pengelolaan Rombongan Belajar (Rombel)')]
class RombelIndex extends Component
{
    use GuardsPermission, \App\Core\Livewire\Traits\WithSorting;

    public bool $showModal = false;
    public ?int $rombelId = null;
    public ?int $tahun_ajaran_id = null;
    public ?int $jurusan_id = null;
    public string $tingkat = 'X';
    public int $nomor_urut = 1;
    public string $nama = '';
    public ?int $wali_kelas_id = null;
    public int $kapasitas = 36;

    public ?int $filterTahunAjaran = null;
    public ?int $filterJurusan = null;
    public ?string $filterTingkat = null;

    public function mount(): void
    {
        $this->sortField = 'nama';
        $currentTa = AcademicContext::currentTahunAjaran();
        $this->filterTahunAjaran = $currentTa?->id;
        $this->tahun_ajaran_id = $currentTa?->id;
    }

    public function updated($propertyName): void
    {
        if (in_array($propertyName, ['tingkat', 'jurusan_id', 'nomor_urut'])) {
            $this->updateGeneratedName();
        }
    }

    public function updateGeneratedName(): void
    {
        if ($this->jurusan_id) {
            $jurusan = Jurusan::find($this->jurusan_id);
            if ($jurusan) {
                $this->nama = Rombel::generateNama($this->tingkat, $jurusan->kode, (int) $this->nomor_urut);
            }
        }
    }

    protected function rules(): array
    {
        return [
            'tahun_ajaran_id' => ['required', 'exists:tahun_ajarans,id'],
            'jurusan_id' => ['required', 'exists:jurusans,id'],
            'tingkat' => ['required', 'in:X,XI,XII'],
            'nomor_urut' => ['required', 'integer', 'min:1', 'max:20'],
            'nama' => ['required', 'string', 'max:50'],
            'wali_kelas_id' => ['nullable', 'exists:users,id'],
            'kapasitas' => ['required', 'integer', 'min:1', 'max:60'],
        ];
    }

    public function create(): void
    {
        $this->guard('akademik.rombel.tambah');
        $this->reset(['rombelId', 'wali_kelas_id']);
        $this->tahun_ajaran_id = $this->filterTahunAjaran ?? AcademicContext::currentTahunAjaran()?->id;
        $firstJurusan = Jurusan::where('is_active', true)->first();
        $this->jurusan_id = $firstJurusan?->id;
        $this->tingkat = 'X';
        $this->nomor_urut = 1;
        $this->kapasitas = 36;
        $this->updateGeneratedName();
        $this->showModal = true;
    }

    public function edit(int $id): void
    {
        $this->guard('akademik.rombel.edit');
        $r = Rombel::findOrFail($id);
        $this->rombelId = $r->id;
        $this->tahun_ajaran_id = $r->tahun_ajaran_id;
        $this->jurusan_id = $r->jurusan_id;
        $this->tingkat = $r->tingkat;
        $this->nomor_urut = $r->nomor_urut;
        $this->nama = $r->nama;
        $this->wali_kelas_id = $r->wali_kelas_id;
        $this->kapasitas = $r->kapasitas;
        $this->showModal = true;
    }

    public function save(): void
    {
        if ($this->rombelId) {
            $this->guard('akademik.rombel.edit');
        } else {
            $this->guard('akademik.rombel.tambah');
        }

        $this->validate();

        $data = [
            'tahun_ajaran_id' => $this->tahun_ajaran_id,
            'jurusan_id' => $this->jurusan_id,
            'tingkat' => $this->tingkat,
            'nomor_urut' => $this->nomor_urut,
            'nama' => $this->nama,
            'wali_kelas_id' => $this->wali_kelas_id ?: null,
            'kapasitas' => $this->kapasitas,
        ];

        if ($this->rombelId) {
            Rombel::findOrFail($this->rombelId)->update($data);
        } else {
            Rombel::create($data);
        }

        $this->showModal = false;
        session()->flash('status', "Rombel {$data['nama']} berhasil disimpan.");
    }

    public function delete(int $id): void
    {
        $this->guard('akademik.rombel.hapus');
        $r = Rombel::findOrFail($id);
        $nama = $r->nama;
        $r->delete();
        session()->flash('status', "Rombel {$nama} berhasil dihapus.");
    }

    public function render()
    {
        $query = Rombel::with(['tahunAjaran', 'jurusan', 'waliKelas'])->withCount('siswas');

        if ($this->filterTahunAjaran) {
            $query->where('tahun_ajaran_id', $this->filterTahunAjaran);
        }
        if ($this->filterJurusan) {
            $query->where('jurusan_id', $this->filterJurusan);
        }
        if ($this->filterTingkat) {
            $query->where('tingkat', $this->filterTingkat);
        }

        $rombels = $query->orderBy($this->sortField, $this->sortDirection)->get();

        $tahunAjarans = TahunAjaran::orderByDesc('tgl_mulai')->get();
        $jurusans = Jurusan::where('is_active', true)->orderBy('kode')->get();
        $gurus = User::whereHas('roles', fn ($q) => $q->whereIn('name', ['Guru', 'Operator']))
            ->orWhereDoesntHave('roles')
            ->orderBy('name')
            ->get();

        return view('akademik::livewire.rombel-index', [
            'rombels' => $rombels,
            'tahunAjarans' => $tahunAjarans,
            'jurusans' => $jurusans,
            'gurus' => $gurus,
        ]);
    }
}
