<?php

namespace App\Modules\Akademik\Livewire;

use App\Core\Concerns\GuardsPermission;
use App\Modules\Akademik\Models\Jurusan;
use App\Modules\Akademik\Models\Mapel;
use App\Modules\Akademik\Models\StrukturKurikulum;
use App\Modules\Akademik\Models\TahunAjaran;
use App\Modules\Akademik\Support\AcademicContext;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Struktur Kurikulum')]
class StrukturKurikulumIndex extends Component
{
    use GuardsPermission;

    public bool $showModal = false;
    public ?int $kurikulumId = null;

    public ?int $filterTahunAjaran = null;
    public ?int $filterJurusan = null;
    public string $filterTingkat = 'X';

    // Form modal state
    public ?int $tahun_ajaran_id = null;
    public ?int $jurusan_id = null;
    public string $tingkat = 'X';
    public ?int $mapel_id = null;
    public int $jp_per_minggu = 2;
    public int $urutan = 1;

    public function mount(): void
    {
        $currentTa = AcademicContext::currentTahunAjaran();
        $this->filterTahunAjaran = $currentTa?->id;
        $firstJurusan = Jurusan::where('is_active', true)->first();
        $this->filterJurusan = $firstJurusan?->id;
    }

    protected function rules(): array
    {
        return [
            'tahun_ajaran_id' => ['required', 'exists:tahun_ajarans,id'],
            'jurusan_id' => ['required', 'exists:jurusans,id'],
            'tingkat' => ['required', 'in:X,XI,XII'],
            'mapel_id' => ['required', 'exists:mapels,id'],
            'jp_per_minggu' => ['required', 'integer', 'min:1', 'max:30'],
            'urutan' => ['required', 'integer', 'min:1'],
        ];
    }

    public function create(): void
    {
        $this->guard('akademik.kurikulum.tambah');
        $this->reset(['kurikulumId']);
        $this->tahun_ajaran_id = $this->filterTahunAjaran ?? AcademicContext::currentTahunAjaran()?->id;
        $this->jurusan_id = $this->filterJurusan;
        $this->tingkat = $this->filterTingkat;
        $firstMapel = Mapel::where('is_active', true)->first();
        $this->mapel_id = $firstMapel?->id;
        $this->jp_per_minggu = 2;
        $this->urutan = (StrukturKurikulum::where('tahun_ajaran_id', $this->tahun_ajaran_id)
            ->where('jurusan_id', $this->jurusan_id)
            ->where('tingkat', $this->tingkat)
            ->max('urutan') ?? 0) + 1;
        $this->showModal = true;
    }

    public function edit(int $id): void
    {
        $this->guard('akademik.kurikulum.edit');
        $k = StrukturKurikulum::findOrFail($id);
        $this->kurikulumId = $k->id;
        $this->tahun_ajaran_id = $k->tahun_ajaran_id;
        $this->jurusan_id = $k->jurusan_id;
        $this->tingkat = $k->tingkat;
        $this->mapel_id = $k->mapel_id;
        $this->jp_per_minggu = $k->jp_per_minggu;
        $this->urutan = $k->urutan;
        $this->showModal = true;
    }

    public function save(): void
    {
        if ($this->kurikulumId) {
            $this->guard('akademik.kurikulum.edit');
        } else {
            $this->guard('akademik.kurikulum.tambah');
        }

        $this->validate();

        $data = [
            'tahun_ajaran_id' => $this->tahun_ajaran_id,
            'jurusan_id' => $this->jurusan_id,
            'tingkat' => $this->tingkat,
            'mapel_id' => $this->mapel_id,
            'jp_per_minggu' => $this->jp_per_minggu,
            'urutan' => $this->urutan,
        ];

        if ($this->kurikulumId) {
            StrukturKurikulum::findOrFail($this->kurikulumId)->update($data);
        } else {
            StrukturKurikulum::updateOrCreate(
                [
                    'tahun_ajaran_id' => $this->tahun_ajaran_id,
                    'jurusan_id' => $this->jurusan_id,
                    'tingkat' => $this->tingkat,
                    'mapel_id' => $this->mapel_id,
                ],
                $data
            );
        }

        $this->showModal = false;
        session()->flash('status', 'Struktur kurikulum berhasil disimpan.');
    }

    public function delete(int $id): void
    {
        $this->guard('akademik.kurikulum.hapus');
        StrukturKurikulum::findOrFail($id)->delete();
        session()->flash('status', 'Mapel telah dihapus dari struktur kurikulum.');
    }

    public function render()
    {
        $items = collect();
        $totalJp = 0;

        if ($this->filterTahunAjaran && $this->filterJurusan && $this->filterTingkat) {
            $items = StrukturKurikulum::with('mapel')
                ->where('tahun_ajaran_id', $this->filterTahunAjaran)
                ->where('jurusan_id', $this->filterJurusan)
                ->where('tingkat', $this->filterTingkat)
                ->orderBy('urutan')
                ->get();

            $totalJp = $items->sum('jp_per_minggu');
        }

        $tahunAjarans = TahunAjaran::orderByDesc('tgl_mulai')->get();
        $jurusans = Jurusan::where('is_active', true)->orderBy('kode')->get();
        $allMapels = Mapel::where('is_active', true)->orderBy('kelompok')->orderBy('nama')->get();

        return view('akademik::livewire.struktur-kurikulum-index', [
            'items' => $items,
            'totalJp' => $totalJp,
            'tahunAjarans' => $tahunAjarans,
            'jurusans' => $jurusans,
            'allMapels' => $allMapels,
        ]);
    }
}
