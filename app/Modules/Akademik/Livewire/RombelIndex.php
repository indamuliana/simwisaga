<?php

namespace App\Modules\Akademik\Livewire;

use App\Core\Concerns\GuardsPermission;
use App\Models\User;
use App\Modules\Akademik\Models\Jurusan;
use App\Modules\Akademik\Models\Rombel;
use App\Modules\Akademik\Models\TahunAjaran;
use App\Modules\Akademik\Support\AcademicContext;
use Illuminate\Support\Facades\DB;
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

    // Generator / Duplikasi Rombel Massal
    public bool $showDuplicateModal = false;
    public ?int $sourceTahunAjaranId = null;
    public ?int $targetTahunAjaranId = null;
    public string $duplicateTingkat = 'semua'; // 'semua', 'X', 'XI', 'XII'
    public bool $includeWaliKelas = false;
    public int $sourceRombelCount = 0;

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

        if (in_array($propertyName, ['sourceTahunAjaranId', 'duplicateTingkat'])) {
            $this->updateSourceRombelCount();
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

    public function openDuplicateModal(): void
    {
        $this->guard('akademik.rombel.tambah');
        $this->targetTahunAjaranId = $this->filterTahunAjaran ?? AcademicContext::currentTahunAjaran()?->id;

        $prevTa = TahunAjaran::where('id', '!=', $this->targetTahunAjaranId)
            ->orderByDesc('id')
            ->first();

        $this->sourceTahunAjaranId = $prevTa?->id;
        $this->duplicateTingkat = 'semua';
        $this->includeWaliKelas = false;
        $this->updateSourceRombelCount();
        $this->showDuplicateModal = true;
    }

    public function closeDuplicateModal(): void
    {
        $this->showDuplicateModal = false;
    }

    public function updateSourceRombelCount(): void
    {
        if ($this->sourceTahunAjaranId) {
            $query = Rombel::where('tahun_ajaran_id', $this->sourceTahunAjaranId);
            if ($this->duplicateTingkat !== 'semua') {
                $query->where('tingkat', $this->duplicateTingkat);
            }
            $this->sourceRombelCount = $query->count();
        } else {
            $this->sourceRombelCount = 0;
        }
    }

    public function executeDuplicate(): void
    {
        $this->guard('akademik.rombel.tambah');

        if (! $this->sourceTahunAjaranId || ! $this->targetTahunAjaranId) {
            session()->flash('error', 'Pilih Tahun Ajaran sumber dan tujuan dengan benar.');
            return;
        }

        if ($this->sourceTahunAjaranId === $this->targetTahunAjaranId) {
            session()->flash('error', 'Tahun Ajaran sumber dan tujuan tidak boleh sama.');
            return;
        }

        $query = Rombel::where('tahun_ajaran_id', $this->sourceTahunAjaranId);
        if ($this->duplicateTingkat !== 'semua') {
            $query->where('tingkat', $this->duplicateTingkat);
        }
        $sourceRombels = $query->get();

        if ($sourceRombels->isEmpty()) {
            session()->flash('error', 'Tidak ada data rombel pada Tahun Ajaran sumber yang dapat disalin.');
            return;
        }

        $createdCount = 0;
        $skippedCount = 0;

        DB::transaction(function () use ($sourceRombels, &$createdCount, &$skippedCount) {
            foreach ($sourceRombels as $src) {
                $exists = Rombel::where('tahun_ajaran_id', $this->targetTahunAjaranId)
                    ->where('jurusan_id', $src->jurusan_id)
                    ->where('tingkat', $src->tingkat)
                    ->where('nomor_urut', $src->nomor_urut)
                    ->exists();

                if (! $exists) {
                    Rombel::create([
                        'tahun_ajaran_id' => $this->targetTahunAjaranId,
                        'jurusan_id' => $src->jurusan_id,
                        'tingkat' => $src->tingkat,
                        'nomor_urut' => $src->nomor_urut,
                        'nama' => $src->nama,
                        'wali_kelas_id' => $this->includeWaliKelas ? $src->wali_kelas_id : null,
                        'kapasitas' => $src->kapasitas,
                    ]);
                    $createdCount++;
                } else {
                    $skippedCount++;
                }
            }
        });

        $this->showDuplicateModal = false;
        $targetTa = TahunAjaran::find($this->targetTahunAjaranId);

        $msg = "Berhasil menyalin {$createdCount} rombel baru ke T.A. {$targetTa?->nama}.";
        if ($skippedCount > 0) {
            $msg .= " ({$skippedCount} rombel dilewati karena sudah ada)";
        }

        session()->flash('status', $msg);
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

        $tahunAjarans = TahunAjaran::orderByDesc('id')->get();
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
