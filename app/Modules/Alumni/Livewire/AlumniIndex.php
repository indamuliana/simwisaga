<?php

namespace App\Modules\Alumni\Livewire;

use App\Core\Concerns\GuardsPermission;
use App\Modules\Akademik\Models\Jurusan;
use App\Modules\Akademik\Models\TahunAjaran;
use App\Modules\Alumni\Models\Alumni;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
#[Title('Pengelolaan Data Alumni & Tracer Study')]
class AlumniIndex extends Component
{
    use GuardsPermission, WithPagination;

    public string $search = '';
    public string $filterTahunLulus = '';
    public string $filterStatusKarir = '';
    public ?int $filterJurusan = null;

    // Modal Edit Tracer Study
    public bool $showEditModal = false;
    public ?int $selectedAlumniId = null;
    public string $status_karir = 'belum_memutuskan';
    public ?string $nama_instansi = null;
    public ?string $posisi_jurusan = null;
    public ?string $pendapatan_range = null;
    public ?string $nomor_ijazah = null;
    public ?string $no_hp_terkini = null;
    public ?string $email_terkini = null;
    public ?string $alamat_terkini = null;
    public ?string $linkedin_url = null;
    public ?string $catatan = null;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedFilterTahunLulus(): void
    {
        $this->resetPage();
    }

    public function updatedFilterStatusKarir(): void
    {
        $this->resetPage();
    }

    public function editTracer(int $id): void
    {
        $this->guard('alumni.alumni.edit');
        $alumni = Alumni::with('siswa')->findOrFail($id);
        $this->selectedAlumniId = $alumni->id;
        $this->status_karir = $alumni->status_karir;
        $this->nama_instansi = $alumni->nama_instansi;
        $this->posisi_jurusan = $alumni->posisi_jurusan;
        $this->pendapatan_range = $alumni->pendapatan_range;
        $this->nomor_ijazah = $alumni->nomor_ijazah;
        $this->no_hp_terkini = $alumni->no_hp_terkini ?: $alumni->siswa?->hp;
        $this->email_terkini = $alumni->email_terkini ?: $alumni->siswa?->email;
        $this->alamat_terkini = $alumni->alamat_terkini ?: $alumni->siswa?->alamat;
        $this->linkedin_url = $alumni->linkedin_url;
        $this->catatan = $alumni->catatan;
        $this->showEditModal = true;
    }

    public function saveTracer(): void
    {
        $this->guard('alumni.alumni.edit');
        if (! $this->selectedAlumniId) {
            return;
        }

        $alumni = Alumni::findOrFail($this->selectedAlumniId);
        $alumni->update([
            'status_karir' => $this->status_karir,
            'nama_instansi' => $this->nama_instansi,
            'posisi_jurusan' => $this->posisi_jurusan,
            'pendapatan_range' => $this->pendapatan_range,
            'nomor_ijazah' => $this->nomor_ijazah,
            'no_hp_terkini' => $this->no_hp_terkini,
            'email_terkini' => $this->email_terkini,
            'alamat_terkini' => $this->alamat_terkini,
            'linkedin_url' => $this->linkedin_url,
            'catatan' => $this->catatan,
        ]);

        $this->showEditModal = false;
        session()->flash('status', "Data alumni {$alumni->siswa?->nama} berhasil diperbarui.");
    }

    public function exportExcel()
    {
        $this->guard('alumni.alumni.lihat');

        $query = Alumni::with(['siswa', 'tahunAjaran']);
        if ($this->search) {
            $query->whereHas('siswa', function ($q) {
                $q->where('nama', 'like', "%{$this->search}%")
                    ->orWhere('nisn', 'like', "%{$this->search}%")
                    ->orWhere('nipd', 'like', "%{$this->search}%");
            })->orWhere('nomor_ijazah', 'like', "%{$this->search}%")
              ->orWhere('nama_instansi', 'like', "%{$this->search}%");
        }

        if ($this->filterTahunLulus) {
            $query->where('tahun_lulus', $this->filterTahunLulus);
        }

        if ($this->filterStatusKarir) {
            $query->where('status_karir', $this->filterStatusKarir);
        }

        $alumnis = $query->orderBy('tahun_lulus', 'desc')->get();
        $exporter = new \App\Modules\Alumni\Services\AlumniExporter();
        $spreadsheet = $exporter->export($alumnis, $this->filterTahunLulus ?: null);

        $filename = 'Data_Alumni_Tracer_Study_' . date('Ymd_His') . '.xlsx';

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    public function render()
    {
        $query = Alumni::with(['siswa.rombel.jurusan', 'tahunAjaran']);

        if ($this->search) {
            $query->whereHas('siswa', function ($q) {
                $q->where('nama', 'like', "%{$this->search}%")
                    ->orWhere('nisn', 'like', "%{$this->search}%")
                    ->orWhere('nipd', 'like', "%{$this->search}%");
            })->orWhere('nomor_ijazah', 'like', "%{$this->search}%")
              ->orWhere('nama_instansi', 'like', "%{$this->search}%");
        }

        if ($this->filterTahunLulus) {
            $query->where('tahun_lulus', $this->filterTahunLulus);
        }

        if ($this->filterStatusKarir) {
            $query->where('status_karir', $this->filterStatusKarir);
        }

        $alumnis = $query->latest('id')->paginate(15);

        $daftarTahunLulus = Alumni::distinct()->pluck('tahun_lulus')->sortDesc();
        $totalAlumni = Alumni::count();
        $totalBekerja = Alumni::where('status_karir', 'bekerja')->count();
        $totalKuliah = Alumni::where('status_karir', 'kuliah')->count();
        $totalWirausaha = Alumni::where('status_karir', 'wirausaha')->count();

        return view('alumni::livewire.alumni-index', [
            'alumnis' => $alumnis,
            'daftarTahunLulus' => $daftarTahunLulus,
            'totalAlumni' => $totalAlumni,
            'totalBekerja' => $totalBekerja,
            'totalKuliah' => $totalKuliah,
            'totalWirausaha' => $totalWirausaha,
        ]);
    }
}
