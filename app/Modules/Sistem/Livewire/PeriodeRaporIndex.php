<?php

namespace App\Modules\Sistem\Livewire;

use App\Core\Concerns\GuardsPermission;
use App\Modules\Akademik\Models\TahunAjaran;
use App\Modules\Akademik\Support\AcademicContext;
use App\Modules\Sistem\Models\PeriodeRapor;
use App\Modules\Sistem\Models\School;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Pengaturan Titi Mangsa & Periode Rapor')]
class PeriodeRaporIndex extends Component
{
    use GuardsPermission;

    // Filter
    public ?int $filterTahunAjaranId = null;

    // Modal Create / Edit
    public bool $showModal = false;
    public ?int $periodeId = null;
    public string $semester = 'ganjil';
    public string $kode = '';
    public string $nama = '';
    public string $sebutan = '';
    public ?string $tanggal_mulai = null;
    public ?string $tanggal_selesai = null;
    public string $tanggal_bagi = '';
    public string $tempat = 'Garut';
    public bool $is_active = false;
    public int $urutan = 1;

    // Modal Salin Periode Antar Tahun Ajaran
    public bool $showCopyModal = false;
    public ?int $sourceTahunAjaranId = null;
    public ?int $targetTahunAjaranId = null;

    public function mount(): void
    {
        $currentTa = AcademicContext::currentTahunAjaran();
        $this->filterTahunAjaranId = $currentTa?->id ?? TahunAjaran::first()?->id;
    }

    public function openCreateModal(): void
    {
        $this->guard('sistem.titimangsa.tambah');

        $this->resetValidation();
        $this->periodeId = null;
        $this->semester = 'ganjil';
        $this->kode = '';
        $this->nama = '';
        $this->sebutan = '';
        $this->tanggal_mulai = null;
        $this->tanggal_selesai = null;
        $this->tanggal_bagi = date('Y-m-d');

        $sekolah = School::current();
        $this->tempat = $sekolah->kabupaten ?: 'Garut';
        $this->is_active = false;

        $maxUrutan = PeriodeRapor::where('tahun_ajaran_id', $this->filterTahunAjaranId)->max('urutan') ?? 0;
        $this->urutan = $maxUrutan + 1;

        $this->showModal = true;
    }

    public function edit(int $id): void
    {
        $this->guard('sistem.titimangsa.edit');

        $this->resetValidation();
        $p = PeriodeRapor::findOrFail($id);

        $this->periodeId = $p->id;
        $this->semester = $p->semester;
        $this->kode = $p->kode;
        $this->nama = $p->nama;
        $this->sebutan = $p->sebutan;
        $this->tanggal_mulai = $p->tanggal_mulai?->format('Y-m-d');
        $this->tanggal_selesai = $p->tanggal_selesai?->format('Y-m-d');
        $this->tanggal_bagi = $p->tanggal_bagi ? $p->tanggal_bagi->format('Y-m-d') : date('Y-m-d');
        $this->tempat = $p->tempat;
        $this->is_active = (bool) $p->is_active;
        $this->urutan = (int) $p->urutan;

        $this->showModal = true;
    }

    public function save(): void
    {
        if ($this->periodeId) {
            $this->guard('sistem.titimangsa.edit');
        } else {
            $this->guard('sistem.titimangsa.tambah');
        }

        $this->validate([
            'filterTahunAjaranId' => ['required', 'exists:tahun_ajarans,id'],
            'semester' => ['required', 'in:ganjil,genap'],
            'kode' => ['required', 'string', 'max:50'],
            'nama' => ['required', 'string', 'max:100'],
            'sebutan' => ['required', 'string', 'max:100'],
            'tanggal_mulai' => ['nullable', 'date'],
            'tanggal_selesai' => ['nullable', 'date', 'after_or_equal:tanggal_mulai'],
            'tanggal_bagi' => ['required', 'date'],
            'tempat' => ['required', 'string', 'max:100'],
            'urutan' => ['required', 'integer', 'min:1'],
        ], [
            'nama.required' => 'Nama resmi periode rapor wajib diisi.',
            'sebutan.required' => 'Istilah/sebutan rapor (misal: 9 Minggu ke-1) wajib diisi.',
            'tanggal_bagi.required' => 'Tanggal pembagian rapor (Titi Mangsa) wajib diisi.',
            'tempat.required' => 'Tempat penerbitan titi mangsa wajib diisi.',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai harus sama atau setelah tanggal mulai.',
        ]);

        DB::transaction(function () {
            if ($this->is_active) {
                // Nonaktifkan periode lain di tahun ajaran yang sama
                PeriodeRapor::where('tahun_ajaran_id', $this->filterTahunAjaranId)
                    ->when($this->periodeId, fn ($q) => $q->where('id', '!=', $this->periodeId))
                    ->update(['is_active' => false]);
            }

            $data = [
                'tahun_ajaran_id' => $this->filterTahunAjaranId,
                'semester' => $this->semester,
                'kode' => $this->kode,
                'nama' => $this->nama,
                'sebutan' => $this->sebutan,
                'tanggal_mulai' => $this->tanggal_mulai ?: null,
                'tanggal_selesai' => $this->tanggal_selesai ?: null,
                'tanggal_bagi' => $this->tanggal_bagi,
                'tempat' => $this->tempat,
                'is_active' => $this->is_active,
                'urutan' => $this->urutan,
            ];

            if ($this->periodeId) {
                PeriodeRapor::findOrFail($this->periodeId)->update($data);
            } else {
                PeriodeRapor::create($data);
            }
        });

        $this->showModal = false;
        session()->flash('status', 'Periode Rapor & Titi Mangsa berhasil disimpan.');
    }

    public function toggleActive(int $id): void
    {
        $this->guard('sistem.titimangsa.edit');

        $periode = PeriodeRapor::findOrFail($id);
        $newStatus = ! $periode->is_active;

        DB::transaction(function () use ($periode, $newStatus) {
            if ($newStatus) {
                // Jika diaktifkan, nonaktifkan periode lain di Tahun Ajaran yang sama
                PeriodeRapor::where('tahun_ajaran_id', $periode->tahun_ajaran_id)
                    ->where('id', '!=', $periode->id)
                    ->update(['is_active' => false]);
            }

            $periode->update(['is_active' => $newStatus]);
        });

        session()->flash('status', $newStatus
            ? "Periode '{$periode->sebutan}' berhasil diaktifkan sebagai periode cetak default."
            : "Periode '{$periode->sebutan}' dinonaktifkan.");
    }

    public function delete(int $id): void
    {
        $this->guard('sistem.titimangsa.hapus');

        $periode = PeriodeRapor::findOrFail($id);
        $nama = $periode->sebutan;
        $periode->delete();

        session()->flash('status', "Periode Rapor '{$nama}' berhasil dihapus.");
    }

    public function openCopyModal(): void
    {
        $this->guard('sistem.titimangsa.tambah');

        $this->sourceTahunAjaranId = null;
        $this->targetTahunAjaranId = $this->filterTahunAjaranId;
        $this->showCopyModal = true;
    }

    public function executeCopy(): void
    {
        $this->guard('sistem.titimangsa.tambah');

        $this->validate([
            'sourceTahunAjaranId' => ['required', 'exists:tahun_ajarans,id', 'different:targetTahunAjaranId'],
            'targetTahunAjaranId' => ['required', 'exists:tahun_ajarans,id'],
        ], [
            'sourceTahunAjaranId.different' => 'Tahun Ajaran sumber dan tujuan tidak boleh sama.',
            'sourceTahunAjaranId.required' => 'Pilih Tahun Ajaran sumber yang ingin disalin.',
        ]);

        $sources = PeriodeRapor::where('tahun_ajaran_id', $this->sourceTahunAjaranId)->get();

        if ($sources->isEmpty()) {
            session()->flash('error', 'Tidak ada data periode rapor pada Tahun Ajaran sumber.');
            return;
        }

        DB::transaction(function () use ($sources) {
            foreach ($sources as $src) {
                PeriodeRapor::updateOrCreate(
                    [
                        'tahun_ajaran_id' => $this->targetTahunAjaranId,
                        'kode' => $src->kode,
                    ],
                    [
                        'semester' => $src->semester,
                        'nama' => $src->nama,
                        'sebutan' => $src->sebutan,
                        'tanggal_mulai' => $src->tanggal_mulai,
                        'tanggal_selesai' => $src->tanggal_selesai,
                        'tanggal_bagi' => $src->tanggal_bagi,
                        'tempat' => $src->tempat,
                        'is_active' => false,
                        'urutan' => $src->urutan,
                    ]
                );
            }
        });

        $this->showCopyModal = false;
        $this->filterTahunAjaranId = $this->targetTahunAjaranId;
        session()->flash('status', "Berhasil menyalin {$sources->count()} periode rapor ke Tahun Ajaran target.");
    }

    public function render()
    {
        $this->guard('sistem.titimangsa.lihat');

        $tahunAjarans = TahunAjaran::orderByDesc('tgl_mulai')->orderByDesc('id')->get();

        $periodes = PeriodeRapor::where('tahun_ajaran_id', $this->filterTahunAjaranId)
            ->orderBy('urutan')
            ->orderBy('semester')
            ->get();

        return view('sistem::livewire.periode-rapor-index', [
            'tahunAjarans' => $tahunAjarans,
            'periodes' => $periodes,
        ]);
    }
}
